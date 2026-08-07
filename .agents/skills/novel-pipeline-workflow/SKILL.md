---
name: novel-pipeline-workflow
description: Complete end-to-end novel publishing, Veo3 trailer video generation, local MP4 downloading, and SQLite database workflow. Orchestrates awesome-novel, publish-noirvella-story, veo3-trailer-generator, downloads trailer-video.mp4 locally, and manages storage in stories.db.
---

# Novel Pipeline Workflow Skill

Skill này là bộ điều phối trung tâm (Workflow Orchestrator) cho quy trình tự động hóa xuất bản & sản xuất video trailer tiểu thuyết trong Google Antigravity (AGY):

> [!CRITICAL]
> **MANDATORY AGY SUBAGENT ORCHESTRATION PROTOCOL:**
> Mọi lệnh thực thi `novel-pipeline-workflow` trong Google Antigravity (AGY) **BẮT BUỘC** phải đi qua 6 pha subagent theo đúng thứ tự trước khi thực thi script xuất bản:
> 1. **Phase 1 (Setup & Settings):** Đăng ký & gọi `@updater` (`define_subagent` -> `.agent/task/setting-update-order.md` -> `invoke_subagent`).
> 2. **Phase 2 (Volume Outline):** Đăng ký & gọi `@volume-planner` (`define_subagent` -> `.agent/task/volume-plan-order.md` -> `invoke_subagent`).
> 3. **Phase 3 (Chapter Outline):** Đăng ký & gọi `@chapter-planner` (`define_subagent` -> `.agent/task/chapter-plan-order.md` -> `invoke_subagent`).
> 4. **Phase 4 (Scene Prompts):** Đăng ký & gọi `@prompt-crafter` (`define_subagent` -> `.agent/task/prompt-craft-order.md` -> `invoke_subagent`) để tạo các tệp `prompts/vol-{N}-ch-{M}-prompt.md`.
> 5. **Phase 5 (Manuscript Writing):** Đăng ký & gọi `@writer` (`define_subagent` -> `.agent/task/writing-order.md` -> `invoke_subagent`) tạo các tệp `archives/vol-{N}-ch-{M}.md`.
> 6. **Phase 6 (Trailer Prompts):** Đăng ký & gọi `@trailer-crafter` (`define_subagent` -> `.agent/task/trailer-order.md` -> `invoke_subagent`) để tạo các tệp `global-trailer-prompt.json` & `.md` và `vol-1-trailer-prompt.json` & `.md`.
> **NGHIÊM CẤM Agent chính tự viết trực tiếp các tệp trong `archives/` hay `prompts/` mà không gọi subagent qua `define_subagent` + `invoke_subagent`.**

```
1. awesome-novel (Tạo dự án qua 6 Subagent @updater, @volume-planner, @chapter-planner, @prompt-crafter, @writer, @trailer-crafter)
       │
       ▼
2. publish-noirvella-story (Xuất bản WordPress)
       │ (story_url & first_chapter_url)
       ▼
3. SQLite Database (Lưu dữ liệu truyện trước — video_trailer_url chấp nhận NULL)
       │
       ▼
4. veo3-trailer-generator (Tạo video qua Veo3 API)
       │
       ├─► Tải video trailer-video.mp4 về thư mục dự án cục bộ
       │
       ▼
5. SQLite Database (Cập nhật video_trailer_url cho bản ghi đã lưu)
```

## Các điểm lưu ý chính

1. **Lưu dữ liệu tức thì**: Sau khi đăng truyện lên WordPress, thông tin truyện (`story_title`, `story_description`, `story_tags`, `story_url`, `first_chapter_url`) lập tức được lưu vào `stories.db`. Lúc này cột `video_trailer_path` mang giá trị **`NULL`**.
2. **Cập nhật khi tệp MP4 có thực sự trên đĩa**: Chỉ khi bước Veo3 hoàn tất và tệp `trailer-video.mp4` thực sự tồn tại trên ổ đĩa (`os.path.exists`), cột `video_trailer_path` mới được cập nhật thành `"trailer-video.mp4"`.
3. **Đường dẫn tệp video local**: Ứng dụng đọc CSDL có thể lấy đường dẫn tuyệt đối tệp MP4 bằng công thức: `os.path.join(record["project_path"], record["video_trailer_path"])` (Ví dụ: `D:\1.Programing\Story theme\<dự-án>\trailer-video.mp4`).
4. **Cập nhật / Retry trailer khi bị lỗi**: Nếu bước Veo3 bị lỗi hoặc rỗng, có thể chạy lại riêng bước Veo3 bằng tham số `--update-trailer-only`.

## Giá trị mặc định (Default Configuration)
- **Default Veo3 Project URL:** `https://labs.google/fx/tools/flow/project/b401d61b-8cd7-40ad-a85f-c2335107e938`
- **Default Profile Name:** `VEO3`

## Các lệnh thực thi

### 1. Quy trình đầy đủ (Xuất bản + Veo3 + Tải MP4 + Lưu DB):

```bash
python .agents/skills/novel-pipeline-workflow/scripts/run_novel_pipeline.py \
  --project-path "<đường-dẫn-dự-án>" \
  --wp-username "<username>" \
  --wp-password "<application-password>" \
  [--veo3-project-url "https://labs.google/fx/tools/flow/project/b401d61b-8cd7-40ad-a85f-c2335107e938"] \
  [--profile-name "VEO3"]
```

### 2. Chỉ chạy lại bước Veo3 Trailer & Cập nhật video_trailer_url trong DB:

```bash
python .agents/skills/novel-pipeline-workflow/scripts/run_novel_pipeline.py \
  --project-path "<đường-dẫn-dự-án>" \
  --update-trailer-only
```

### 3. Tra cứu hoặc cập nhật DB bằng tay:

```bash
# Xem danh sách truyện trong DB
python .agents/skills/novel-pipeline-workflow/scripts/story_db.py list

# Cập nhật video_trailer_url thủ công
python .agents/skills/novel-pipeline-workflow/scripts/story_db.py update-trailer \
  --project-path "<đường-dẫn-dự-án>" \
  --video-trailer-url "<url-video-veo3>"
```
