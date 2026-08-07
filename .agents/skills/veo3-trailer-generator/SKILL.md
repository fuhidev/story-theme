---
name: veo3-trailer-generator
description: Generates a Veo3 trailer video directly from a novel project's JSON prompt (prompts/trailers/global-trailer-prompt.json), downloads trailer-video.mp4, and automatically applies remove_watermark.py to overwrite trailer-video.mp4 without logo.
---

# Veo3 Trailer Generator Skill

Skill này tự động hóa việc gọi API local `ghmautomate` (`http://127.0.0.1:1408`) để kích hoạt ứng dụng `Veo3CreateTrailerStory`, tự động đọc prompt trực tiếp từ tệp JSON `prompts/trailers/global-trailer-prompt.json` trong thư mục dự án tiểu thuyết, lấy về `videoUrl`, **tải file video (`trailer-video.mp4`) về máy**, sau đó **tự động gọi script `remove_watermark.py` (OpenCV + FFmpeg) để xóa watermark logo Veo3 và ghi đè trực tiếp lên tệp `trailer-video.mp4`**.

## Cú pháp lệnh

```bash
python .agents/skills/veo3-trailer-generator/scripts/generate_veo3_trailer.py \
  --project-path "<đường-dẫn-dự-án>" \
  --project-url "<veo3-project-url>" \
  [--profile-name "via-01"] \
  [--timeout 780]
```

## Kết quả trả về

Script trả về kết quả dạng JSON chứa `videoUrl` và đường dẫn file video local, đồng thời ghi vào tệp `trailer-result.json` trong thư mục dự án:

```json
{
  "success": true,
  "videoUrl": "https://...",
  "localVideoPath": "D:\\1.Programing\\Story theme\\<project>\\trailer-video.mp4",
  "jobId": "..."
}
```
