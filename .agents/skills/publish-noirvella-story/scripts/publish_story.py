#!/usr/bin/env python3
"""
Noirvella Stories Publisher CLI Script (Local Workspace Skill)
Tự động đăng truyện và tất cả các chương từ dự án tiểu thuyết lên website WordPress Noirvella.
"""

import os
import sys
import json
import re
import argparse
import urllib.request
import urllib.parse
import base64

# Đảm bảo UTF-8 cho Windows console
for s in (sys.stdin, sys.stdout, sys.stderr):
    try:
        s.reconfigure(encoding="utf-8")
    except AttributeError:
        pass

# Full Markdown -> HTML conversion via markdown2 (headings, lists, blockquotes,
# links, tables, hr, bold/italic...) when the package is installed. Falls back
# to a small hand-written converter that still covers the constructs these
# story/chapter files actually use, so the script keeps working on a machine
# where `pip install markdown2` hasn't been run.
try:
    import markdown2
    _HAS_MARKDOWN2 = True
except ImportError:
    markdown2 = None
    _HAS_MARKDOWN2 = False

_MARKDOWN2_EXTRAS = ["fenced-code-blocks", "tables", "cuddled-lists", "strike"]

# Story/chapter files often use an en dash or em dash ("– Genre: ...") as a
# bullet marker instead of the "-" Markdown actually requires -- neither
# markdown2 nor any CommonMark parser treats "–" as list syntax, so without
# this normalization those lines fall through as plain paragraph text.
# Rewriting them to a real "-" bullet before conversion is what turns them
# into an actual <ul><li> list instead of literal dashes on the page.
_DASH_BULLET_RE = re.compile(r'(?m)^[–—]\s+')


def _normalize_dash_bullets(text):
    return _DASH_BULLET_RE.sub('- ', text)


def _inline_md(text):
    text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', text)
    text = re.sub(r'\*(.+?)\*', r'<em>\1</em>', text)
    text = re.sub(r'\[(.+?)\]\((.+?)\)', r'<a href="\2">\1</a>', text)
    return text


def _fallback_md_to_html_body(body_md):
    """Used only when markdown2 isn't installed. Not a full CommonMark
    parser -- covers exactly what shows up in these files: #.. headings,
    -/*/– bullet lists, --- rules, **bold**/*italic*/[links](url), and
    plain paragraphs."""
    html_parts = []
    para_buf = []
    list_open = False

    def flush_para():
        if para_buf:
            text = " ".join(para_buf).strip()
            if text:
                html_parts.append("<p>{}</p>".format(_inline_md(text)))
            para_buf.clear()

    def close_list():
        nonlocal list_open
        if list_open:
            html_parts.append("</ul>")
            list_open = False

    for raw_line in body_md.split("\n"):
        line = raw_line.strip()

        if not line:
            flush_para()
            close_list()
            continue

        m_heading = re.match(r'^(#{1,6})\s+(.*)$', line)
        if m_heading:
            flush_para()
            close_list()
            level = len(m_heading.group(1))
            html_parts.append("<h{0}>{1}</h{0}>".format(level, _inline_md(m_heading.group(2).strip())))
            continue

        if re.match(r'^(-{3,}|\*{3,}|_{3,})$', line):
            flush_para()
            close_list()
            html_parts.append("<hr>")
            continue

        m_item = re.match(r'^[-*]\s+(.*)$', line)
        if m_item:
            flush_para()
            if not list_open:
                html_parts.append("<ul>")
                list_open = True
            html_parts.append("<li>{}</li>".format(_inline_md(m_item.group(1).strip())))
            continue

        close_list()
        para_buf.append(line)

    flush_para()
    close_list()
    return "".join(html_parts)


def md_to_html(md_text):
    lines = md_text.strip().split("\n")
    title = ""
    body_lines = []

    for line in lines:
        if line.startswith("# ") and not title:
            title = line[2:].strip()
        else:
            body_lines.append(line)

    body_md = _normalize_dash_bullets("\n".join(body_lines).strip())
    if not body_md:
        return title, ""

    if _HAS_MARKDOWN2:
        html_body = markdown2.markdown(body_md, extras=_MARKDOWN2_EXTRAS).strip()
    else:
        html_body = _fallback_md_to_html_body(body_md)

    return title, html_body

def extract_chapter_sort_key(filename):
    # Parse vol-X-ch-Y.md -> (X, Y)
    m = re.search(r'vol-(\d+)-ch-(\d+)', filename, re.IGNORECASE)
    if m:
        return (int(m.group(1)), int(m.group(2)))
    # Parse ch-Y.md -> (1, Y)
    m2 = re.search(r'ch-(\d+)', filename, re.IGNORECASE)
    if m2:
        return (1, int(m2.group(1)))
    return (999, 999)

def parse_clean_story_info(st_text, fallback_title):
    lines = st_text.splitlines()
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
        # Fallback if no explicit section header found: collect all non-header non-meta paragraphs
        fallback_paras = []
        for line in lines:
            sline = line.strip()
            if sline and not sline.startswith("#") and not sline.startswith("- **") and "skill_version" not in sline:
                fallback_paras.append(sline)
        synopsis_text = "\n\n".join(fallback_paras)

    clean_title = title if title else fallback_title
    return clean_title, genre, tags, synopsis_text


def main():
    parser = argparse.ArgumentParser(description="Publish Noirvella Story and Chapters to WordPress site")
    parser.add_argument("--project-path", required=True, help="Path to novel project directory")
    parser.add_argument("--username", required=True, help="WordPress username")
    parser.add_argument("--password", required=True, help="WordPress Application Password")
    parser.add_argument("--site-url", default="https://story.icestech.info", help="WordPress site URL")
    parser.add_argument("--status", default="publish", help="Publish status (publish, draft, pending)")
    
    args = parser.parse_args()
    
    project_dir = os.path.abspath(args.project_path)
    archives_dir = os.path.join(project_dir, "archives")
    story_md_path = os.path.join(project_dir, "story.md")
    
    if not os.path.exists(project_dir):
        print(json.dumps({"error": f"Project directory not found: {project_dir}"}))
        sys.exit(1)
        
    # Read story metadata
    fallback_title = os.path.basename(project_dir).replace("-", " ").title()
    story_title = fallback_title
    story_excerpt = ""
    story_content = ""
    genre = ""
    tags = ""
    
    if os.path.exists(story_md_path):
        with open(story_md_path, "r", encoding="utf-8") as f:
            st_text = f.read()
            
        story_title, genre, tags, synopsis_text = parse_clean_story_info(st_text, fallback_title)
        
        # Convert synopsis Markdown to HTML
        _, syn_html = md_to_html(synopsis_text)
        
        # Construct attractive, reader-facing HTML story content
        meta_badges = []
        if genre:
            meta_badges.append(f"<p><strong>Genre:</strong> {genre}</p>")
        if tags:
            meta_badges.append(f"<p><strong>Tags:</strong> {tags}</p>")
            
        header_html = "\n".join(meta_badges)
        if header_html:
            story_content = f"{header_html}\n<hr>\n{syn_html}"
        else:
            story_content = syn_html
            
        # Clean excerpt for search cards and listings
        plain_synopsis = re.sub(r'<[^>]+>', '', syn_html)
        story_excerpt = plain_synopsis[:300].strip() + ("..." if len(plain_synopsis) > 300 else "")
            
    # Read chapters
    if not os.path.exists(archives_dir):
        print(json.dumps({"error": f"Archives directory not found: {archives_dir}"}))
        sys.exit(1)
        
    chapter_files = [f for f in os.listdir(archives_dir) if f.endswith(".md") and not f.endswith(".draft.md")]
    chapter_files.sort(key=extract_chapter_sort_key)
    
    if not chapter_files:
        print(json.dumps({"error": "No chapter markdown files found in archives/"}))
        sys.exit(1)
        
    chapters_payload = []
    for idx, fname in enumerate(chapter_files, start=1):
        fpath = os.path.join(archives_dir, fname)
        with open(fpath, "r", encoding="utf-8") as f:
            c_text = f.read()
        
        ch_title, ch_html = md_to_html(c_text)
        if not ch_title:
            ch_title = f"Chương {idx}"
            
        chapters_payload.append({
            "title": ch_title,
            "content": ch_html,
            "status": args.status,
            "menu_order": idx
        })
        
    payload = {
        "title": story_title,
        "content": story_content,
        "excerpt": story_excerpt,
        "status": args.status,
        "chapters": chapters_payload
    }
    
    auth_str = f"{args.username}:{args.password}"
    auth_b64 = base64.b64encode(auth_str.encode("utf-8")).decode("utf-8")
    
    endpoint = f"{args.site_url.rstrip('/')}/wp-json/noirvella/v1/stories"
    
    req = urllib.request.Request(endpoint, data=json.dumps(payload).encode("utf-8"), headers={
        "Content-Type": "application/json",
        "Authorization": f"Basic {auth_b64}"
    })
    
    try:
        with urllib.request.urlopen(req) as response:
            res_data = json.loads(response.read().decode("utf-8"))
            print(json.dumps(res_data, indent=2, ensure_ascii=False))
    except urllib.error.HTTPError as e:
        err_body = e.read().decode("utf-8")
        print(json.dumps({"error": f"HTTPError {e.code}", "details": err_body}))
        sys.exit(1)
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    main()
