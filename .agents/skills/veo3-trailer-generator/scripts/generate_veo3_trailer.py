#!/usr/bin/env python3
"""
generate_veo3_trailer.py
Generates a Veo3 trailer video by communicating with the ghmautomate local API,
extracts videoUrl, and downloads the video file directly into the novel project folder.
"""

import os
import sys
import json
import time
import argparse
import urllib.request
import urllib.error

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

API_BASE_URL = "http://127.0.0.1:1408"
APP_ID = "Veo3CreateTrailerStory"

def read_prompt_file(project_path, prompt_file=None):
    if prompt_file and os.path.exists(prompt_file):
        with open(prompt_file, 'r', encoding='utf-8') as f:
            return f.read().strip()

    # Search in order of priority: global-trailer-prompt.json -> vol-1-trailer-prompt.json -> trailer-prompt.json
    possible_paths = [
        os.path.join(project_path, "prompts", "trailers", "global-trailer-prompt.json"),
        os.path.join(project_path, "prompts", "trailers", "vol-1-trailer-prompt.json"),
    ]

    trailers_dir = os.path.join(project_path, "prompts", "trailers")
    if os.path.exists(trailers_dir):
        for fname in sorted(os.listdir(trailers_dir), reverse=True):
            if fname.endswith(".json"):
                candidate = os.path.join(trailers_dir, fname)
                if candidate not in possible_paths:
                    possible_paths.insert(0, candidate)

    possible_paths.append(os.path.join(project_path, "trailer-prompt.json"))

    for p in possible_paths:
        if os.path.exists(p):
            print(f"[*] Đã tìm thấy file trailer prompt JSON: {p}")
            with open(p, 'r', encoding='utf-8') as f:
                return f.read().strip()

    raise FileNotFoundError(f"Không tìm thấy tệp trailer prompt JSON tại: {project_path} (đã kiểm tra prompts/trailers/global-trailer-prompt.json)")



def make_http_request(url, method="GET", payload=None, headers=None, timeout=800):
    if headers is None:
        headers = {}
    
    data = None
    if payload is not None:
        data = json.dumps(payload).encode('utf-8')
        headers["Content-Type"] = "application/json"

    req = urllib.request.Request(url, data=data, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=timeout) as response:
            res_body = response.read().decode('utf-8')
            return response.status, json.loads(res_body)
    except urllib.error.HTTPError as e:
        err_body = e.read().decode('utf-8')
        try:
            return e.code, json.loads(err_body)
        except Exception:
            return e.code, {"success": False, "error": err_body}
    except Exception as e:
        return 500, {"success": False, "error": str(e)}

def download_video(video_url, output_path):
    print(f"[*] Đang tải video trailer từ: {video_url}...")
    print(f"[*] Đường dẫn lưu file: {output_path}")
    try:
        headers = {'User-Agent': 'Mozilla/5.0'}
        req = urllib.request.Request(video_url, headers=headers)
        with urllib.request.urlopen(req, timeout=300) as response, open(output_path, 'wb') as out_file:
            chunk_size = 64 * 1024
            while True:
                chunk = response.read(chunk_size)
                if not chunk:
                    break
                out_file.write(chunk)
        print(f"[V] Tải video trailer về thư mục dự án thành công! ({os.path.getsize(output_path)} bytes)")
        return True, None
    except Exception as e:
        err_msg = f"Lỗi khi tải video trailer: {str(e)}"
        print(f"[X] {err_msg}")
        return False, err_msg

def extract_video_url_from_job_data(job_data):
    profiles = job_data.get("profiles", [])
    if not profiles:
        return None, "No profiles found in job response"
    
    profile = profiles[0]
    prof_status = profile.get("status")
    message_raw = profile.get("message", "")
    
    if prof_status == "failed":
        return None, f"Profile status failed: {message_raw}"
    
    try:
        msg_obj = json.loads(message_raw)
        if msg_obj.get("success") and "videoUrl" in msg_obj:
            return msg_obj["videoUrl"], None
        elif "error" in msg_obj:
            return None, msg_obj["error"]
        elif "videoUrl" in msg_obj:
            return msg_obj["videoUrl"], None
    except Exception:
        if message_raw.startswith("http://") or message_raw.startswith("https://"):
            return message_raw, None

    return None, f"Could not parse videoUrl from message: {message_raw}"

def poll_job(base_url, job_id, max_wait_sec=900, poll_interval=10):
    print(f"[*] Đang theo dõi Job: {job_id}...")
    start_time = time.time()
    
    while time.time() - start_time < max_wait_sec:
        job_url = f"{base_url}/api/v1/jobs/{job_id}"
        status_code, response = make_http_request(job_url, method="GET")
        
        if status_code != 200 or not response.get("success"):
            print(f"[!] Lỗi khi lấy trạng thái Job: {response}")
            time.sleep(poll_interval)
            continue
        
        job_data = response.get("data", {})
        job_status = job_data.get("status")
        print(f"    - Trạng thái Job: {job_status} ({int(time.time() - start_time)}s)")
        
        if job_status == "completed":
            video_url, err = extract_video_url_from_job_data(job_data)
            if video_url:
                return True, video_url, None
            return False, None, err or "Không tìm thấy videoUrl trong kết quả hoàn tất"
            
        elif job_status == "failed":
            job_err = job_data.get("error") or "Job bị lỗi"
            return False, None, f"Job failed: {job_err}"
            
        time.sleep(poll_interval)
        
    return False, None, f"Quá thời gian chờ ({max_wait_sec}s)"

REMOVE_WATERMARK_SCRIPT = os.path.join(os.path.dirname(__file__), "remove_watermark.py")

import subprocess

def apply_remove_watermark(mp4_path):
    if not os.path.exists(REMOVE_WATERMARK_SCRIPT):
        print(f"[!] Không tìm thấy script xóa watermark tại: {REMOVE_WATERMARK_SCRIPT}")
        return False
    
    if not os.path.exists(mp4_path) or os.path.getsize(mp4_path) == 0:
        print(f"[!] Tệp video không tồn tại để xóa watermark: {mp4_path}")
        return False
        
    cleaned_tmp = mp4_path.replace(".mp4", "_cleaned_tmp.mp4")
    print(f"[*] Đang thực thi xóa watermark logo Veo3 bằng script remove_watermark.py...")
    
    env = dict(os.environ)
    env["PYTHONIOENCODING"] = "utf-8"
    cmd = [sys.executable, REMOVE_WATERMARK_SCRIPT, "-i", mp4_path, "-o", cleaned_tmp]
    
    try:
        proc = subprocess.run(cmd, env=env, capture_output=True, text=True, encoding="utf-8", errors="ignore")
        if proc.returncode == 0 and os.path.exists(cleaned_tmp) and os.path.getsize(cleaned_tmp) > 0:
            if os.path.exists(mp4_path):
                os.remove(mp4_path)
            os.rename(cleaned_tmp, mp4_path)
            print(f"[V] Đã xóa watermark logo thành công và ghi đè vào: {mp4_path}")
            return True
        else:
            print(f"[!] Quá trình xóa watermark logo thất bại. Out: {proc.stdout} | Err: {proc.stderr}")
            if os.path.exists(cleaned_tmp):
                try: os.remove(cleaned_tmp)
                except: pass
    except Exception as e:
        print(f"[X] Lỗi khi thực thi xóa logo watermark: {str(e)}")
        if os.path.exists(cleaned_tmp):
            try: os.remove(cleaned_tmp)
            except: pass
    return False

def generate_trailer(project_path, project_url, profile_name="via-01", base_url=API_BASE_URL, timeout_sec=780, prompt_file=None, output_name="trailer-video.mp4"):
    prompt_text = read_prompt_file(project_path, prompt_file)
    print(f"[*] Đã đọc prompt ({len(prompt_text)} ký tự) từ dự án: {project_path}")

    start_url = f"{base_url}/api/v1/apps/{APP_ID}/start"
    payload = {
        "profileNames": [profile_name],
        "payload": {
            "projectUrl": project_url,
            "prompt": prompt_text
        },
        "wait": True,
        "waitTimeoutMs": timeout_sec * 1000
    }

    print(f"[*] Đang gửi yêu cầu tạo video đến {start_url} (profileNames: ['{profile_name}'])...")

    status_code, response = make_http_request(start_url, method="POST", payload=payload, timeout=timeout_sec + 30)

    video_url = None
    job_id = None
    err = None

    if response.get("success"):
        if "data" in response and response["data"].get("status") == "completed":
            video_url, err = extract_video_url_from_job_data(response["data"])
            job_id = response["data"].get("jobId")
        else:
            job_id = response.get("jobId") or (response.get("data", {}).get("jobId"))
            if job_id:
                print(f"[*] Yêu cầu đang được xử lý ở nền. Job ID: {job_id}")
                ok, video_url, err = poll_job(base_url, job_id, max_wait_sec=timeout_sec)
    else:
        err = response.get("error") or "Gửi yêu cầu API thất bại"

    if not video_url:
        print(f"[X] Không thể lấy videoUrl: {err}")
        return {"success": False, "error": err, "jobId": job_id}

    # Tải video về thư mục trailer-videos trong dự án
    output_dir = os.path.join(project_path, "trailer-videos")
    os.makedirs(output_dir, exist_ok=True)

    if not output_name.endswith(".mp4"):
        output_name += ".mp4"

    local_mp4_path = os.path.join(output_dir, output_name)
    dl_ok, dl_err = download_video(video_url, local_mp4_path)

    # Tự động xóa watermark logo và ghi đè file MP4
    if dl_ok:
        apply_remove_watermark(local_mp4_path)

    res_data = {
        "success": True,
        "videoUrl": video_url,
        "localVideoPath": local_mp4_path if dl_ok else None,
        "downloadError": dl_err if not dl_ok else None,
        "jobId": job_id
    }
    save_result(project_path, res_data, output_name)
    return res_data

def save_result(project_path, res_data, output_name="trailer-video.mp4"):
    output_dir = os.path.join(project_path, "trailer-videos")
    os.makedirs(output_dir, exist_ok=True)
    base_name = os.path.splitext(output_name)[0]
    res_path = os.path.join(output_dir, f"{base_name}-result.json")
    res_data["updatedAt"] = time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime())
    with open(res_path, 'w', encoding='utf-8') as f:
        json.dump(res_data, f, ensure_ascii=False, indent=2)
    print(f"[*] Đã lưu kết quả vào: {res_path}")

DEFAULT_PROJECT_URL = "https://labs.google/fx/tools/flow/project/b401d61b-8cd7-40ad-a85f-c2335107e938"
DEFAULT_PROFILE_NAME = "VEO3"

def main():
    parser = argparse.ArgumentParser(description="Tạo video Veo3 trailer từ prompt trong dự án tiểu thuyết và tải video về máy")
    parser.add_argument("--project-path", required=True, help="Đường dẫn thư mục dự án tiểu thuyết")
    parser.add_argument("--project-url", default=DEFAULT_PROJECT_URL, help="URL project Google Labs Veo3 (mặc định b401d61b-8cd7-40ad-a85f-c2335107e938)")
    parser.add_argument("--profile-name", default=DEFAULT_PROFILE_NAME, help="Tên profile anti-detect (mặc định VEO3)")
    parser.add_argument("--api-url", default=API_BASE_URL, help="Base URL của ghmautomate API")
    parser.add_argument("--timeout", type=int, default=780, help="Thời gian chờ tối đa (giây)")
    parser.add_argument("--prompt-file", default=None, help="Tệp prompt tùy chỉnh")
    parser.add_argument("--output-name", default="trailer-video.mp4", help="Tên tệp video trailer lưu trong trailer-videos/ (mặc định trailer-video.mp4)")

    args = parser.parse_args()

    res = generate_trailer(
        project_path=os.path.abspath(args.project_path),
        project_url=args.project_url,
        profile_name=args.profile_name,
        base_url=args.api_url,
        timeout_sec=args.timeout,
        prompt_file=args.prompt_file,
        output_name=args.output_name
    )

    print(json.dumps(res, ensure_ascii=False, indent=2))
    if not res.get("success"):
        sys.exit(1)

if __name__ == "__main__":
    main()
