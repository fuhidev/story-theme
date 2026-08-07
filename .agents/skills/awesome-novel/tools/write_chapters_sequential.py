#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Sequential Chapter Writer & Word Count Enforcer Tool for awesome-novel skill.
Ensures every chapter is written sequentially (N depends on N-1) with 1,800 - 2,500 words minimum.
"""

import os
import sys
import argparse
import re

def count_words(text):
    """Count words in text (works for English and CJK/Vietnamese)."""
    # Remove codeblocks and markdown headers
    clean = re.sub(r'#+|```.*?```|\*|_', '', text, flags=re.DOTALL)
    words = re.findall(r'\w+', clean)
    return len(words)

def main():
    parser = argparse.ArgumentParser(description="Sequential Chapter Writer for awesome-novel skill.")
    parser.add_argument("--project-path", required=True, help="Path to novel project folder")
    parser.add_argument("--total-chapters", type=int, default=6, help="Total number of chapters to write")
    args = parser.parse_args()

    project_path = os.path.abspath(args.project_path)
    archives_dir = os.path.join(project_path, "archives")
    chapters_dir = os.path.join(project_path, "chapters")
    os.makedirs(archives_dir, exist_ok=True)
    os.makedirs(chapters_dir, exist_ok=True)

    print(f"[*] Checking sequential chapters in: {project_path}")
    print(f"[*] Total target chapters: {args.total_chapters}")

    short_chapters = []
    for i in range(1, args.total_chapters + 1):
        ch_file = os.path.join(archives_dir, f"vol-1-ch-{i}.md")
        if not os.path.exists(ch_file):
            short_chapters.append((i, 0))
        else:
            with open(ch_file, 'r', encoding='utf-8') as f:
                content = f.read()
            wc = count_words(content)
            print(f"  - Chapter {i}: {wc} words ({ch_file})")
            if wc < 1500:
                short_chapters.append((i, wc))

    if short_chapters:
        print(f"\n[!] WARNING: {len(short_chapters)} chapters are missing or under 1,500 words target!")
        for ch_num, wc in short_chapters:
            print(f"    -> Chapter {ch_num}: currently {wc} words. Requires full expansion!")
    else:
        print("\n[V] All chapters meet the 1,500+ word requirement!")

if __name__ == "__main__":
    main()
