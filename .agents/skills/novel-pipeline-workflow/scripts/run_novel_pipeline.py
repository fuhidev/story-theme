#!/usr/bin/env python3
"""
run_novel_pipeline.py
Master orchestrator script that manages the novel pipeline:
1. Extract story title/description/tags from story.md (created by awesome-novel)
2. Publish story to Noirvella WordPress (publish-noirvella-story)
3. Save story metadata + WordPress URLs to SQLite DB (video_trailer_url can be NULL)
4. Generate Veo3 video trailer (veo3-trailer-generator), download video to novel folder, and update video_trailer_url in SQLite DB.
"""

import os
import sys
import json
import argparse
import subprocess
import story_db

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
SKILL_DIR = os.path.dirname(os.path.dirname(SCRIPT_DIR))
VEO3_SKILL_DIR = os.path.join(SKILL_DIR, "veo3-trailer-generator", "scripts")
PUBLISH_SKILL_DIR = os.path.join(SKILL_DIR, "publish-noirvella-story", "scripts")

def parse_story_md(project_path):
    story_file = os.path.join(project_path, "story.md")
    if not os.path.exists(story_file):
        raise FileNotFoundError(f"Không tìm thấy tệp story.md tại: {project_path}")
        
    with open(story_file, 'r', encoding='utf-8') as f:
        content = f.read().strip()
        
    lines = content.splitlines()
    fallback_title = os.path.basename(os.path.normpath(project_path)).replace("-", " ").title()
    title = ""
    genre = ""
    tags = ""
    synopsis_lines = []
    
    in_synopsis = False
    in_meta = False
    
    for line in lines:
        sline = line.strip()
        if not sline:
            if in_synopsis:
                synopsis_lines.append("")
            continue
        if sline.startswith("# ") and not title:
            title = sline[2:].strip()
            continue
        if sline.startswith("## Meta Information"):
            in_meta = True
            in_synopsis = False
            continue
        if sline.startswith("## Story Core Concept") or sline.startswith("## Synopsis") or sline.startswith("## Premise"):
            in_meta = False
            in_synopsis = True
            continue
        if sline.startswith("## Main Storyline Outline") or sline.startswith("## Volume"):
            in_synopsis = False
            in_meta = False
            continue
            
        if in_meta:
            if "genre:" in sline.lower():
                genre = sline.split(":", 1)[1].strip("* ").strip()
            elif "tags:" in sline.lower():
                tags = sline.split(":", 1)[1].strip("* ").strip()
            continue
            
        if in_synopsis:
            synopsis_lines.append(sline)
            
    synopsis_text = "\n".join(synopsis_lines).strip()
    if not synopsis_text:
        fallback_paras = []
        for line in lines:
            sline = line.strip()
            if sline and not sline.startswith("#") and not sline.startswith("- **") and "skill_version" not in sline:
                fallback_paras.append(sline)
        synopsis_text = "\n\n".join(fallback_paras)

    clean_title = title if title else fallback_title
    
    return {
        "title": clean_title,
        "description": synopsis_text,
        "tags": tags if tags else genre
    }

def run_publish_step(project_path, username, password, site_url=None, status="publish"):
    publish_script = os.path.join(PUBLISH_SKILL_DIR, "publish_story.py")
    if not os.path.exists(publish_script):
        print(f"[!] Không tìm thấy publish_story.py tại {publish_script}")
        return None, None
        
    cmd = [
        sys.executable, publish_script,
        "--project-path", project_path,
        "--username", username,
        "--password", password,
        "--status", status
    ]
    if site_url:
        cmd.extend(["--site-url", site_url])
        
    print(f"[*] Đang xuất bản truyện lên WordPress ({site_url or 'https://story.icestech.info'})...")
    try:
        res = subprocess.run(cmd, capture_output=True, encoding='utf-8', errors='replace')
        stdout = res.stdout or ""
        try:
            data = json.loads(stdout.strip())
            if data.get("success") or "story_id" in data or "link" in data:
                story_url = data.get("link", "")
                chapters = data.get("chapters", [])
                first_chapter_url = chapters[0].get("link", "") if chapters else ""
                print(f"[V] Xuất bản bài viết thành công: {story_url}")
                return story_url, first_chapter_url
        except Exception:
            for line in reversed(stdout.strip().splitlines()):
                line = line.strip()
                if line.startswith("{") and line.endswith("}"):
                    try:
                        data = json.loads(line)
                        if data.get("success") or "story_id" in data or "link" in data:
                            story_url = data.get("link", "")
                            chapters = data.get("chapters", [])
                            first_chapter_url = chapters[0].get("link", "") if chapters else ""
                            print(f"[V] Xuất bản bài viết thành công: {story_url}")
                            return story_url, first_chapter_url
                    except Exception:
                        pass
    except Exception as e:
        print(f"[X] Lỗi khi xuất bản truyện: {str(e)}")
    return None, None

def run_veo3_step(project_path, project_url, profile_name="via-01", timeout=780):
    veo3_script = os.path.join(VEO3_SKILL_DIR, "generate_veo3_trailer.py")
    if not os.path.exists(veo3_script):
        print(f"[!] Không tìm thấy generate_veo3_trailer.py tại {veo3_script}")
        return None
        
    cmd = [
        sys.executable, veo3_script,
        "--project-path", project_path,
        "--project-url", project_url,
        "--profile-name", profile_name,
        "--timeout", str(timeout)
    ]
    print(f"[*] Đang kích hoạt tạo video Veo3 & tải video về máy...")
    try:
        res = subprocess.run(cmd, capture_output=True, encoding='utf-8', errors='replace')
        stdout = res.stdout or ""
        stderr = res.stderr or ""
        if res.returncode != 0:
            print(f"[X] generate_veo3_trailer.py thoát với mã lỗi {res.returncode}: {stderr or stdout}")
        
        output_lines = stdout.strip().splitlines()
        for line in reversed(output_lines):
            line = line.strip()
            if line.startswith("{") and line.endswith("}"):
                try:
                    data = json.loads(line)
                    if data.get("success") and "videoUrl" in data:
                        video_url = data["videoUrl"]
                        print(f"[V] Tạo video Veo3 thành công: {video_url}")
                        return video_url
                except Exception:
                    pass
    except Exception as e:
        print(f"[X] Lỗi khi tạo video Veo3: {str(e)}")
    return None


DEFAULT_VEO3_PROJECT_URL = "https://labs.google/fx/tools/flow/project/b401d61b-8cd7-40ad-a85f-c2335107e938"
DEFAULT_PROFILE_NAME = "VEO3"

def main():
    parser = argparse.ArgumentParser(description="Chạy toàn bộ quy trình xuất bản truyện, tạo trailer Veo3 và lưu vào SQLite DB")
    parser.add_argument("--project-path", required=True, help="Thư mục dự án tiểu thuyết")
    parser.add_argument("--veo3-project-url", default=DEFAULT_VEO3_PROJECT_URL, help="URL Google Labs Veo3 Project (để tạo video)")
    parser.add_argument("--wp-username", help="Tài khoản WordPress")
    parser.add_argument("--wp-password", help="Mật khẩu Application Password WordPress")
    parser.add_argument("--wp-site-url", help="URL trang WordPress")
    parser.add_argument("--profile-name", default=DEFAULT_PROFILE_NAME, help="Tên profile anti-detect Veo3 (mặc định VEO3)")
    parser.add_argument("--skip-publish", action="store_true", help="Bỏ qua bước xuất bản WordPress")
    parser.add_argument("--skip-veo3", action="store_true", help="Bỏ qua bước tạo video Veo3")
    parser.add_argument("--update-trailer-only", action="store_true", help="Chỉ chạy lại tạo video trailer Veo3 và cập nhật DB")
    parser.add_argument("--db-path", default=None, help="Đường dẫn SQLite database (mặc định stories.db)")

    args = parser.parse_args()
    proj_path = os.path.abspath(args.project_path)

    print(f"==================================================")
    print(f"[*] KHỞI ĐỘNG WORKFLOW TIỂU THUYẾT: {os.path.basename(proj_path)}")
    print(f"==================================================")

    # Mode 1: Update trailer video only for an existing record
    if args.update_trailer_only:
        veo3_url = args.veo3_project_url or DEFAULT_VEO3_PROJECT_URL
        v_url = run_veo3_step(proj_path, veo3_url, args.profile_name)
        if v_url and os.path.exists(os.path.join(proj_path, "trailer-video.mp4")):
            db_res = story_db.update_trailer(proj_path, "trailer-video.mp4", args.db_path)
            print(f"[V] Đã xác nhận file trailer-video.mp4 trên đĩa và cập nhật SQLite DB!")
        else:
            print(f"[!] Chưa tạo thành công file trailer-video.mp4 trên đĩa, video_trailer_path hiện để NULL.")
        return


    # Mode 2: Standard End-to-End Pipeline
    meta = parse_story_md(proj_path)
    print(f"[*] Tiêu đề truyện: {meta['title']}")

    existing_record = story_db.get_story(proj_path, args.db_path)
    story_url = existing_record.get("story_url", "") if existing_record else ""
    first_chapter_url = existing_record.get("first_chapter_url", "") if existing_record else ""
    
    # Check if local trailer-video.mp4 actually exists on disk
    local_mp4_path = os.path.join(proj_path, "trailer-video.mp4")
    video_trailer_path = "trailer-video.mp4" if (os.path.exists(local_mp4_path) and os.path.getsize(local_mp4_path) > 0) else None

    # Step 1: Publish story to WordPress
    if not args.skip_publish and args.wp_username and args.wp_password:
        pub_story_url, pub_chap_url = run_publish_step(
            proj_path, args.wp_username, args.wp_password, args.wp_site_url
        )
        if pub_story_url:
            story_url = pub_story_url
        if pub_chap_url:
            first_chapter_url = pub_chap_url

    # Step 2: Immediately save story info into SQLite (video_trailer_path will be NULL if MP4 not yet on disk)
    db_res = story_db.save_story(
        project_path=proj_path,
        story_title=meta['title'],
        story_description=meta['description'],
        story_tags=meta['tags'],
        story_url=story_url,
        first_chapter_url=first_chapter_url,
        video_trailer_path=video_trailer_path,
        db_path=args.db_path
    )

    # Step 3: Veo3 Video Generation (and update video_trailer_path ONLY when file is downloaded to disk)
    if not args.skip_veo3 and args.veo3_project_url:
        v_url = run_veo3_step(proj_path, args.veo3_project_url, args.profile_name)
        if v_url and os.path.exists(local_mp4_path) and os.path.getsize(local_mp4_path) > 0:
            video_trailer_path = "trailer-video.mp4"
            db_res = story_db.update_trailer(proj_path, video_trailer_path, args.db_path)
        else:
            print(f"[!] Tạo video Veo3 bị lỗi hoặc tệp MP4 chưa có trên đĩa. video_trailer_path hiện giữ nguyên trong DB.")

    print(f"\n==================================================")
    print(f"[V] KẾT QUẢ CUỐI CÙNG TRONG SQLITE DATABASE:")
    print(json.dumps(db_res, ensure_ascii=False, indent=2))
    print(f"==================================================")

if __name__ == "__main__":
    main()
