#!/usr/bin/env python3
"""
story_db.py
SQLite Database Manager for saving and querying published stories and their local trailer video paths.
Managed by the novel-pipeline-workflow skill.
"""

import os
import sys
import sqlite3
import json
import argparse
from datetime import datetime

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# DB file placed at root of workspace (d:\1.Programing\Story theme\stories.db)
DEFAULT_DB_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", "..", "..", "stories.db"))

def get_connection(db_path=None):
    if not db_path:
        db_path = DEFAULT_DB_PATH
    conn = sqlite3.connect(db_path, timeout=30.0)
    conn.row_factory = sqlite3.Row
    return conn

def init_db(db_path=None):
    conn = get_connection(db_path)
    cursor = conn.cursor()
    cursor.execute("PRAGMA journal_mode=WAL;")
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS published_stories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_path TEXT UNIQUE,
            story_title TEXT NOT NULL,
            story_description TEXT,
            story_tags TEXT,
            story_url TEXT,
            first_chapter_url TEXT,
            video_trailer_path TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    """)
    
    # Auto-migration: Check if video_trailer_path column exists in existing table
    cursor.execute("PRAGMA table_info(published_stories);")
    columns = [row['name'] for row in cursor.fetchall()]
    
    if 'video_trailer_path' not in columns:
        cursor.execute("ALTER TABLE published_stories ADD COLUMN video_trailer_path TEXT DEFAULT NULL;")
        
    conn.commit()
    conn.close()

def check_local_trailer_exists(project_path, trailer_name=None):
    norm_path = os.path.abspath(project_path)
    if trailer_name:
        full_mp4 = os.path.join(norm_path, trailer_name)
        if os.path.exists(full_mp4) and os.path.getsize(full_mp4) > 0:
            return trailer_name

    rel_new = os.path.join("trailer-videos", "trailer-video.mp4")
    full_new = os.path.join(norm_path, rel_new)
    if os.path.exists(full_new) and os.path.getsize(full_new) > 0:
        return rel_new

    # Check for any .mp4 files inside trailer-videos/ directory
    tv_dir = os.path.join(norm_path, "trailer-videos")
    if os.path.exists(tv_dir) and os.path.isdir(tv_dir):
        mp4_files = [f for f in os.listdir(tv_dir) if f.endswith(".mp4") and os.path.getsize(os.path.join(tv_dir, f)) > 0]
        if mp4_files:
            mp4_files.sort(key=lambda x: os.path.getmtime(os.path.join(tv_dir, x)), reverse=True)
            return os.path.join("trailer-videos", mp4_files[0])

    full_old = os.path.join(norm_path, "trailer-video.mp4")
    if os.path.exists(full_old) and os.path.getsize(full_old) > 0:
        return "trailer-video.mp4"

    return None

def list_local_trailers(project_path):
    """Returns a list of relative paths for all trailer mp4 files in project_path/trailer-videos/"""
    norm_path = os.path.abspath(project_path)
    tv_dir = os.path.join(norm_path, "trailer-videos")
    trailers = []
    if os.path.exists(tv_dir) and os.path.isdir(tv_dir):
        for f in os.listdir(tv_dir):
            if f.endswith(".mp4") and os.path.getsize(os.path.join(tv_dir, f)) > 0:
                trailers.append(os.path.join("trailer-videos", f))
    return trailers

def save_story(project_path, story_title, story_description="", story_tags="", story_url="", first_chapter_url="", video_trailer_path=None, db_path=None):
    init_db(db_path)
    conn = get_connection(db_path)
    cursor = conn.cursor()
    
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    norm_path = os.path.abspath(project_path)
    
    # Only assign trailer-video.mp4 if the file actually exists on disk
    if not video_trailer_path:
        video_trailer_path = check_local_trailer_exists(norm_path)

    cursor.execute("""
        INSERT INTO published_stories (
            project_path, story_title, story_description, story_tags, 
            story_url, first_chapter_url, video_trailer_path, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT(project_path) DO UPDATE SET
            story_title = excluded.story_title,
            story_description = COALESCE(NULLIF(excluded.story_description, ''), published_stories.story_description),
            story_tags = COALESCE(NULLIF(excluded.story_tags, ''), published_stories.story_tags),
            story_url = COALESCE(NULLIF(excluded.story_url, ''), published_stories.story_url),
            first_chapter_url = COALESCE(NULLIF(excluded.first_chapter_url, ''), published_stories.first_chapter_url),
            video_trailer_path = COALESCE(excluded.video_trailer_path, published_stories.video_trailer_path),
            updated_at = excluded.updated_at
    """, (norm_path, story_title, story_description, story_tags, story_url, first_chapter_url, video_trailer_path, now, now))
    
    conn.commit()

    cursor.execute("SELECT id, project_path, story_title, story_description, story_tags, story_url, first_chapter_url, video_trailer_path, created_at, updated_at FROM published_stories WHERE project_path = ?", (norm_path,))
    row = cursor.fetchone()
    conn.close()

    row_dict = dict(row) if row else {}
    print(f"[V] Đã lưu/cập nhật thông tin truyện '{story_title}' vào SQLite thành công!")
    return row_dict

def update_trailer(project_path, video_trailer_path=None, db_path=None):
    init_db(db_path)
    conn = get_connection(db_path)
    cursor = conn.cursor()
    
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    norm_path = os.path.abspath(project_path)

    # Verify if file actually exists on disk before updating
    verified_path = check_local_trailer_exists(norm_path, video_trailer_path)
    if not verified_path and video_trailer_path:
        # User explicitly passed a path: check if that specific path exists
        if os.path.exists(os.path.join(norm_path, video_trailer_path)):
            verified_path = video_trailer_path

    cursor.execute("""
        UPDATE published_stories
        SET video_trailer_path = ?, updated_at = ?
        WHERE project_path = ?
    """, (verified_path, now, norm_path))
    
    conn.commit()
    cursor.execute("SELECT id, project_path, story_title, story_description, story_tags, story_url, first_chapter_url, video_trailer_path, created_at, updated_at FROM published_stories WHERE project_path = ?", (norm_path,))
    row = cursor.fetchone()
    conn.close()

    row_dict = dict(row) if row else {}
    if row_dict:
        if verified_path:
            print(f"[V] Đã cập nhật video_trailer_path ('{verified_path}') cho dự án: {norm_path}")
        else:
            print(f"[!] Chưa thấy file video MP4 trên đĩa, video_trailer_path hiện để NULL cho dự án: {norm_path}")
    else:
        print(f"[!] Dự án chưa có trong SQLite, hãy chạy save_story trước.")
    return row_dict

def get_story(project_path, db_path=None):
    init_db(db_path)
    conn = get_connection(db_path)
    cursor = conn.cursor()
    norm_path = os.path.abspath(project_path)
    cursor.execute("SELECT id, project_path, story_title, story_description, story_tags, story_url, first_chapter_url, video_trailer_path, created_at, updated_at FROM published_stories WHERE project_path = ?", (norm_path,))
    row = cursor.fetchone()
    conn.close()
    return dict(row) if row else None

def list_all_stories(db_path=None):
    init_db(db_path)
    conn = get_connection(db_path)
    cursor = conn.cursor()
    cursor.execute("SELECT id, project_path, story_title, story_description, story_tags, story_url, first_chapter_url, video_trailer_path, created_at, updated_at FROM published_stories ORDER BY id DESC")
    rows = cursor.fetchall()
    conn.close()
    return [dict(r) for r in rows]

def main():
    parser = argparse.ArgumentParser(description="Quản lý SQLite Database lưu thông tin truyện và đường dẫn tệp video trailer local")
    subparsers = parser.add_subparsers(dest="command", help="Lệnh thực thi")

    init_parser = subparsers.add_parser("init", help="Khởi tạo cơ sở dữ liệu")
    init_parser.add_argument("--db-path", default=None)

    save_parser = subparsers.add_parser("save", help="Lưu hoặc cập nhật truyện")
    save_parser.add_argument("--project-path", required=True)
    save_parser.add_argument("--story-title", required=True)
    save_parser.add_argument("--story-description", default="")
    save_parser.add_argument("--story-tags", default="")
    save_parser.add_argument("--story-url", default="")
    save_parser.add_argument("--first-chapter-url", default="")
    save_parser.add_argument("--video-trailer-path", default=None)
    save_parser.add_argument("--db-path", default=None)

    update_trailer_parser = subparsers.add_parser("update-trailer", help="Cập nhật video_trailer_path khi đã có file mp4 thực tế")
    update_trailer_parser.add_argument("--project-path", required=True)
    update_trailer_parser.add_argument("--video-trailer-path", default=None)
    update_trailer_parser.add_argument("--db-path", default=None)

    list_parser = subparsers.add_parser("list", help="Danh sách tất cả truyện đã lưu")
    list_parser.add_argument("--db-path", default=None)

    args = parser.parse_args()

    if args.command == "init":
        init_db(args.db_path)
    elif args.command == "save":
        res = save_story(
            project_path=args.project_path,
            story_title=args.story_title,
            story_description=args.story_description,
            story_tags=args.story_tags,
            story_url=args.story_url,
            first_chapter_url=args.first_chapter_url,
            video_trailer_path=args.video_trailer_path,
            db_path=args.db_path
        )
        print(json.dumps(res, ensure_ascii=False, indent=2))
    elif args.command == "update-trailer":
        res = update_trailer(
            project_path=args.project_path,
            video_trailer_path=args.video_trailer_path,
            db_path=args.db_path
        )
        print(json.dumps(res, ensure_ascii=False, indent=2))
    elif args.command == "list":
        stories = list_all_stories(args.db_path)
        print(json.dumps(stories, ensure_ascii=False, indent=2))
    else:
        init_db()

if __name__ == "__main__":
    main()
