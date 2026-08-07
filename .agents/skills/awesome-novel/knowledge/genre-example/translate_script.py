import os
import time
from deep_translator import GoogleTranslator

files = [
    "derivative.md", "ancient-politics.md",
    "historical.md", "scifi-apocalypse.md", "suspense-crime.md", "urban-romance.md",
    "urban.md", "western-fantasy.md", "xianxia.md", "xuanhuan.md"
]

translator = GoogleTranslator(source='zh-CN', target='en')

def translate_large_text(text):
    paragraphs = text.split('\n\n')
    translated_text = []
    current_chunk = ""
    
    for p in paragraphs:
        if len(current_chunk) + len(p) < 2000:
            current_chunk += p + "\n\n"
        else:
            time.sleep(2)  # avoid rate limiting
            try:
                translated_text.append(translator.translate(current_chunk))
            except:
                time.sleep(5)
                translated_text.append(translator.translate(current_chunk))
            current_chunk = p + "\n\n"
            
    if current_chunk.strip():
        time.sleep(2)
        try:
            translated_text.append(translator.translate(current_chunk))
        except:
            pass
        
    return '\n\n'.join(translated_text).replace('  ', ' ')

for filename in files:
    if os.path.exists(filename):
        with open(filename, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # skip if already translated (very basic check)
        if "Theme Configuration" in content or "Type Profile:" in content or "Genre Profile:" in content:
            print(f"Skipping {filename}")
            continue
            
        print(f"Translating {filename}...")
        try:
            en_content = translate_large_text(content)
            
            with open(filename, 'w', encoding='utf-8') as f:
                f.write(en_content)
            print(f"Done {filename}")
        except Exception as e:
            print(f"Error translating {filename}: {e}")
