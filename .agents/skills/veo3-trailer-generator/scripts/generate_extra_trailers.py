#!/usr/bin/env python3
"""
generate_extra_trailers.py
Fair Queue Balancing Trailer Generator for Novel Story Projects.

Algorithm: Least-Trailers-First & Round-Robin Batching
1. Scans story projects in workspace.
2. Counts existing valid .mp4 files in project_path/trailer-videos/.
3. Sorts projects ascending by trailer count (0 videos first, then 1, 2...).
4. Identifies pending trailer prompt JSONs in prompts/trailers/.
5. Generates missing trailer videos, saves into trailer-videos/, and updates SQLite DB.
Designed to be triggered non-interactively via /schedule.
"""

import os
import sys
import json
import time
import argparse
import subprocess

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# Add parent directory of script to sys.path if needed
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
if SCRIPT_DIR not in sys.path:
    sys.path.insert(0, SCRIPT_DIR)

WORKFLOW_SCRIPT_DIR = os.path.abspath(os.path.join(SCRIPT_DIR, "..", "..", "novel-pipeline-workflow", "scripts"))
if WORKFLOW_SCRIPT_DIR not in sys.path:
    sys.path.insert(0, WORKFLOW_SCRIPT_DIR)

try:
    import generate_veo3_trailer
except ImportError:
    generate_veo3_trailer = None

try:
    import story_db
except ImportError:
    story_db = None


def get_existing_trailers(project_path):
    tv_dir = os.path.join(project_path, "trailer-videos")
    if not os.path.exists(tv_dir) or not os.path.isdir(tv_dir):
        return []
    return [f for f in os.listdir(tv_dir) if f.endswith(".mp4") and os.path.getsize(os.path.join(tv_dir, f)) > 0]


def get_prompt_to_video_mapping(project_path):
    trailers_dir = os.path.join(project_path, "prompts", "trailers")
    prompts = []

    if os.path.exists(trailers_dir) and os.path.isdir(trailers_dir):
        for fname in sorted(os.listdir(trailers_dir)):
            if fname.endswith(".json"):
                prompts.append(os.path.join(trailers_dir, fname))

    root_prompt = os.path.join(project_path, "trailer-prompt.json")
    if os.path.exists(root_prompt) and root_prompt not in prompts:
        prompts.append(root_prompt)

    mappings = []
    tv_dir = os.path.join(project_path, "trailer-videos")
    existing_mp4s = get_existing_trailers(project_path)

    for p_path in prompts:
        base_name = os.path.basename(p_path)
        if base_name.endswith("-prompt.json"):
            video_name = base_name[:-12] + ".mp4"
        elif base_name.endswith(".json"):
            video_name = base_name[:-5] + ".mp4"
        else:
            video_name = "trailer-video.mp4"

        # Special case: global-trailer-prompt.json can map to global-trailer.mp4 or trailer-video.mp4
        if base_name == "global-trailer-prompt.json":
            if "trailer-video.mp4" in existing_mp4s:
                video_name = "trailer-video.mp4"
            elif "global-trailer.mp4" in existing_mp4s:
                video_name = "global-trailer.mp4"
            else:
                video_name = "trailer-video.mp4"

        target_mp4 = os.path.join(tv_dir, video_name)
        exists = os.path.exists(target_mp4) and os.path.getsize(target_mp4) > 0

        mappings.append({
            "prompt_file": p_path,
            "prompt_name": base_name,
            "video_name": video_name,
            "video_path": target_mp4,
            "exists": exists
        })

    return mappings


def discover_projects(workspace_dir):
    projects = []
    if not os.path.exists(workspace_dir):
        return projects

    for entry in os.listdir(workspace_dir):
        full_path = os.path.join(workspace_dir, entry)
        if os.path.isdir(full_path) and not entry.startswith(".") and not entry.startswith("_"):
            # Check if it is a story project directory
            has_trailers_dir = os.path.exists(os.path.join(full_path, "prompts", "trailers"))
            has_settings_dir = os.path.exists(os.path.join(full_path, "settings"))
            has_root_prompt = os.path.exists(os.path.join(full_path, "trailer-prompt.json"))
            if has_trailers_dir or has_settings_dir or has_root_prompt:
                projects.append(full_path)

    return sorted(projects)


def process_extra_trailers(
    workspace_dir,
    target_project=None,
    prompt_file=None,
    output_name=None,
    project_url=None,
    profile_name="VEO3",
    max_per_story=1,
    max_total_batch=3,
    max_trailers_cap=5,
    db_path=None
):
    print("==================================================")
    print("[*] KHỞI ĐỘNG FAIR QUEUE BALANCING TRAILER GENERATOR")
    print(f"[*] Max Per Story: {max_per_story} | Max Total Batch: {max_total_batch} | Cap: {max_trailers_cap}")
    print("==================================================")

    # Mode A: Specific prompt file and output name passed directly
    if target_project and prompt_file:
        proj_path = os.path.abspath(target_project)
        p_file = os.path.abspath(prompt_file)
        out_name = output_name or (os.path.basename(p_file).replace("-prompt.json", ".mp4").replace(".json", ".mp4"))
        
        print(f"[*] Chạy chế độ tạo prompt chỉ định: {p_file} -> {out_name}")
        if generate_veo3_trailer:
            res = generate_veo3_trailer.generate_trailer(
                project_path=proj_path,
                project_url=project_url or generate_veo3_trailer.DEFAULT_PROJECT_URL,
                profile_name=profile_name,
                prompt_file=p_file,
                output_name=out_name
            )
            if story_db and res.get("success"):
                story_db.update_trailer(proj_path, os.path.join("trailer-videos", out_name), db_path)
            return res
        else:
            return {"success": False, "error": "generate_veo3_trailer module missing"}

    # Mode B: Scanning projects for pending trailers
    candidate_projects = []
    if target_project:
        candidate_projects = [os.path.abspath(target_project)]
    else:
        candidate_projects = discover_projects(workspace_dir)

    print(f"[*] Tìm thấy {len(candidate_projects)} dự án tiểu thuyết để kiểm tra.")

    project_stats = []
    for proj in candidate_projects:
        existing_mp4s = get_existing_trailers(proj)
        mappings = get_prompt_to_video_mapping(proj)
        pending = [m for m in mappings if not m["exists"]]

        project_stats.append({
            "project_path": proj,
            "project_name": os.path.basename(proj),
            "existing_count": len(existing_mp4s),
            "existing_mp4s": existing_mp4s,
            "mappings": mappings,
            "pending": pending
        })

    # Least-Trailers-First Sorting: 0 trailers first, then 1, 2...
    project_stats.sort(key=lambda x: (x["existing_count"], x["project_name"]))

    print("\n--- BẢNG XẾP HẠNG HÀNG ĐỢI (LEAST-TRAILERS-FIRST) ---")
    for ps in project_stats:
        print(f"  - [{ps['existing_count']} video] {ps['project_name']} (Còn thiếu: {len(ps['pending'])} trailer)")

    generated_results = []
    total_generated = 0

    for ps in project_stats:
        if total_generated >= max_total_batch:
            print(f"\n[*] Đã đạt giới hạn batch cho lượt này ({max_total_batch} video). Dừng hàng đợi.")
            break

        if ps["existing_count"] >= max_trailers_cap:
            print(f"[*] Dự án {ps['project_name']} đã đạt giới hạn tối đa ({max_trailers_cap} video). Bỏ qua.")
            continue

        if not ps["pending"]:
            continue

        story_generated = 0
        print(f"\n[>>>] Đang xử lý dự án: {ps['project_name']} (Hiện có: {ps['existing_count']} video)")

        for item in ps["pending"]:
            if story_generated >= max_per_story or total_generated >= max_total_batch:
                break

            p_file = item["prompt_file"]
            out_name = item["video_name"]

            print(f"  - Tạo video: {item['prompt_name']} -> trailer-videos/{out_name}")

            if generate_veo3_trailer:
                try:
                    res = generate_veo3_trailer.generate_trailer(
                        project_path=ps["project_path"],
                        project_url=project_url or generate_veo3_trailer.DEFAULT_PROJECT_URL,
                        profile_name=profile_name,
                        prompt_file=p_file,
                        output_name=out_name
                    )

                    if res.get("success"):
                        rel_path = os.path.join("trailer-videos", out_name)
                        if story_db:
                            story_db.update_trailer(ps["project_path"], rel_path, db_path)
                        
                        story_generated += 1
                        total_generated += 1
                        generated_results.append({
                            "project": ps["project_name"],
                            "prompt": item["prompt_name"],
                            "video": out_name,
                            "path": res.get("localVideoPath")
                        })
                    else:
                        print(f"  [!] Lỗi tạo video cho {ps['project_name']}: {res.get('error')}")
                except Exception as e:
                    print(f"  [X] Ngoại lệ khi tạo video: {str(e)}")

    print("\n==================================================")
    print(f"[V] HOÀN THÀNH. Đã tạo thành công {total_generated} video trailer mới.")
    print("==================================================")

    return {
        "success": True,
        "total_generated": total_generated,
        "generated": generated_results
    }


def main():
    parser = argparse.ArgumentParser(description="Tạo thêm video trailer Veo3 cho các dự án tiểu thuyết theo ưu tiên công bằng (Least-Trailers-First)")
    parser.add_argument("--workspace-dir", default=os.path.abspath(os.path.join(SCRIPT_DIR, "..", "..", "..", "..")), help="Thư mục làm việc chứa các dự án tiểu thuyết")
    parser.add_argument("--project-path", default=None, help="Đường dẫn tới 1 dự án tiểu thuyết cụ thể (tùy chọn)")
    parser.add_argument("--prompt-file", default=None, help="Đường dẫn file prompt JSON cụ thể (tùy chọn)")
    parser.add_argument("--output-name", default=None, help="Tên file video đầu ra (tùy chọn)")
    parser.add_argument("--veo3-project-url", default=None, help="URL project Veo3")
    parser.add_argument("--profile-name", default="VEO3", help="Tên profile anti-detect (mặc định VEO3)")
    parser.add_argument("--max-per-story", type=int, default=1, help="Số video tối đa tạo cho 1 story trong 1 chu kỳ chạy (mặc định 1)")
    parser.add_argument("--max-total-batch", type=int, default=3, help="Tổng số video tối đa tạo trong 1 chu kỳ chạy (mặc định 3)")
    parser.add_argument("--max-trailers-cap", type=int, default=5, help="Giới hạn tối đa số trailer cho mỗi dự án (mặc định 5)")
    parser.add_argument("--db-path", default=None, help="Đường dẫn CSDL SQLite")

    args = parser.parse_args()

    res = process_extra_trailers(
        workspace_dir=args.workspace_dir,
        target_project=args.project_path,
        prompt_file=args.prompt_file,
        output_name=args.output_name,
        project_url=args.veo3_project_url,
        profile_name=args.profile_name,
        max_per_story=args.max_per_story,
        max_total_batch=args.max_total_batch,
        max_trailers_cap=args.max_trailers_cap,
        db_path=args.db_path
    )

    print(json.dumps(res, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
