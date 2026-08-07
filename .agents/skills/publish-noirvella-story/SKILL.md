---
name: publish-noirvella-story
description: Automates publishing stories and chapters to a Noirvella Stories WordPress site via the custom noirvella/v1 REST API. Use when the user requests uploading, posting, or publishing a novel or story project to the web application.
---

# Publish Noirvella Story Skill

Skill này tự động hóa quy trình xuất bản các dự án tiểu thuyết (gồm bài giới thiệu truyện và tất cả các chương) lên nền tảng Noirvella Stories WordPress thông qua custom REST API `noirvella/v1`.

## Quy trình thực hiện

1. **Xác định đường dẫn dự án:**
   - Đảm bảo dự án có tệp `story.md` (chứa tên truyện & tóm tắt) và thư mục `archives/` chứa các tệp chương `.md` (ví dụ: `vol-1-ch-1.md`, `vol-1-ch-2.md`...).
   - Script tự chuyển toàn bộ Markdown (heading `#`/`##`/`###`, danh sách gạch đầu dòng `-`/`*`/`–`, `**đậm**`/`*nghiêng*`, `[link](url)`, `---`) sang HTML thật trước khi gửi lên WordPress — theme không còn hiển thị ký tự Markdown thô nữa.
   - Để có chất lượng chuyển đổi tốt nhất (đầy đủ bảng, blockquote, v.v.), cài gói `markdown2` một lần: `pip install markdown2`. Nếu chưa cài, script vẫn chạy được nhờ bộ chuyển đổi dự phòng tích hợp sẵn (hỗ trợ heading, danh sách, `---`, đậm/nghiêng, link, đoạn văn) — chỉ là kém đầy đủ hơn `markdown2`.

2. **Thu thập thông tin xác thực (Basic Auth):**
   - Tài khoản WordPress username (ví dụ: `fortool`).
   - Mật khẩu ứng dụng Application Password (ví dụ: `PnLU s2Xb Gqkl U34f QkPL 0MWT`).
   - Địa chỉ URL trang web (mặc định: `https://story.icestech.info`).

3. **Chạy script tự động hóa `publish_story.py`:**
   ```bash
   python .agents/skills/publish-noirvella-story/scripts/publish_story.py --project-path "<đường-dẫn-dự-án>" --username "<username>" --password "<application-password>" [--site-url "<url>"] [--status "publish"]
   ```

4. **Kiểm tra kết quả:**
   - Script trả về JSON chứa `story_id`, đường dẫn `link` trang chính của truyện và danh sách URL từng chương.
   - Báo cáo kết quả với đường dẫn nhấp được cho người dùng.
