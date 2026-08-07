---
name: veo3-trailer-generator
description: Generates a Veo3 trailer video directly from a novel project's JSON prompt (prompts/trailers/global-trailer-prompt.json), downloads video into trailer-videos/, and automatically applies remove_watermark.py to overwrite video without logo.
---

# Veo3 Trailer Generator Skill

Skill này tự động hóa việc gọi API local `ghmautomate` (`http://127.0.0.1:1408`) để kích hoạt ứng dụng `Veo3CreateTrailerStory`, tự động đọc prompt trực tiếp từ tệp JSON trong thư mục dự án tiểu thuyết (`prompts/trailers/`), lấy về `videoUrl`, **tải file video về thư mục `trailer-videos/` trong dự án (hỗ trợ lưu nhiều video trailer như `global-trailer.mp4`, `vol-1-trailer.mp4`, `trailer-video.mp4`)**, sau đó **tự động gọi script `remove_watermark.py` (OpenCV + FFmpeg) để xóa watermark logo Veo3 và ghi đè trực tiếp lên tệp video**.

## Cú pháp lệnh

### 1. Tạo trailer đơn lẻ cho 1 dự án:

```bash
# Tạo trailer mặc định (trailer-video.mp4)
python .agents/skills/veo3-trailer-generator/scripts/generate_veo3_trailer.py \
  --project-path "<đường-dẫn-dự-án>" \
  --project-url "<veo3-project-url>" \
  [--profile-name "VEO3"] \
  [--timeout 780]

# Tạo trailer riêng theo tập/tập prompt tùy chỉnh (lưu nhiều video trong trailer-videos/)
python .agents/skills/veo3-trailer-generator/scripts/generate_veo3_trailer.py \
  --project-path "<đường-dẫn-dự-án>" \
  --prompt-file "<đường-dẫn-dự-án>/prompts/trailers/vol-1-trailer-prompt.json" \
  --output-name "vol-1-trailer.mp4"
```

### 2. Tự động sinh thêm trailer công bằng cho toàn bộ dự án (Chạy ngầm / Lịch trình `/schedule`):

Script `generate_extra_trailers.py` áp dụng thuật toán **Least-Trailers-First**: ưu tiên tuyệt đối các dự án chưa có video trailer nào trước, phân bổ tối đa 1 video/dự án trong 1 lượt chạy ngầm.

```bash
# Chạy quét tất cả dự án và tự động bổ sung trailer còn thiếu (ưu tiên truyện có 0 video)
python .agents/skills/veo3-trailer-generator/scripts/generate_extra_trailers.py \
  --max-per-story 1 \
  --max-total-batch 3

# Kiểm tra thứ tự hàng đợi không thực thi (Dry run)
python .agents/skills/veo3-trailer-generator/scripts/generate_extra_trailers.py \
  --max-total-batch 0
```

## Kết quả trả về

Script trả về kết quả dạng JSON chứa `videoUrl` và đường dẫn tệp video local, đồng thời ghi tệp log tương ứng (ví dụ: `vol-1-trailer-result.json` hoặc `trailer-result.json`) trong thư mục `trailer-videos/` của dự án:

```json
{
  "success": true,
  "videoUrl": "https://...",
  "localVideoPath": "D:\\1.Programing\\Story theme\\<project>\\trailer-videos\\vol-1-trailer.mp4",
  "jobId": "..."
}
```
