
import os
import re

def minify_css(content):
    # Remove comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content)
    # Remove whitespace
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'\s*([{:;,])\s*', r'\1', content)
    content = re.sub(r';}', '}', content)
    return content.strip()

def minify_js(content):
    # Very basic JS minifier (removes comments and extra whitespace)
    # Note: A full JS minifier is complex, this is a safe approximation for "Asset optimization" without deps
    # Remove single line comments
    content = re.sub(r'//.*', '', content)
    # Remove multi-line comments
    content = re.sub(r'/\*[\s\S]*?\*/', '', content)
    # Replace multiple spaces with single space
    content = re.sub(r'\s+', ' ', content)
    # Fix spacing around common operators
    content = re.sub(r'\s*([=+\-*/{}();,])\s*', r'\1', content)
    return content.strip()

def process_file(file_path, type):
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_size = len(content)
        
        if type == 'css':
            minified = minify_css(content)
        else:
            minified = minify_js(content)
            
        new_size = len(minified)
        
        # Save as .min copy
        base, ext = os.path.splitext(file_path)
        min_path = f"{base}.min{ext}"
        
        with open(min_path, 'w', encoding='utf-8') as f:
            f.write(minified)
            
        print(f"Minified {os.path.basename(file_path)}: {original_size} -> {new_size} bytes (Saved as {os.path.basename(min_path)})")
        return min_path
    except Exception as e:
        print(f"Error processing {file_path}: {e}")
        return None

if __name__ == "__main__":
    base_dir = os.getcwd()
    files_to_minify = [
        (os.path.join(base_dir, 'public/css/custom.css'), 'css'),
        (os.path.join(base_dir, 'public/js/premium-app.js'), 'js')
    ]
    
    print("Starting asset minification...")
    for file_path, ftype in files_to_minify:
        if os.path.exists(file_path):
            process_file(file_path, ftype)
        else:
            print(f"File not found: {file_path}")
    print("Minification complete.")
