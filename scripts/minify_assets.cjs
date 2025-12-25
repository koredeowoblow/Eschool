
const fs = require('fs');
const path = require('path');

function minifyCss(content) {
    // Remove comments
    content = content.replace(/\/\*[\s\S]*?\*\//g, '');
    // Remove whitespace
    content = content.replace(/\s+/g, ' ');
    content = content.replace(/\s*([{:;,])\s*/g, '$1');
    content = content.replace(/;}/g, '}');
    return content.trim();
}

function minifyJs(content) {
    // Remove single line comments
    content = content.replace(/\/\/.*$/gm, '');
    // Remove multi-line comments
    content = content.replace(/\/\*[\s\S]*?\*\//g, '');
    // Replace multiple spaces with single space
    content = content.replace(/\s+/g, ' ');
    // Fix spacing around common operators
    content = content.replace(/\s*([=+\-*/{}();,])\s*/g, '$1');
    return content.trim();
}

function processFile(filePath, type) {
    try {
        const content = fs.readFileSync(filePath, 'utf8');
        const originalSize = content.length;

        let minified;
        if (type === 'css') {
            minified = minifyCss(content);
        } else {
            minified = minifyJs(content);
        }

        const newSize = minified.length;
        const info = path.parse(filePath);
        const minPath = path.join(info.dir, `${info.name}.min${info.ext}`);

        fs.writeFileSync(minPath, minified, 'utf8');
        console.log(`Minified ${info.base}: ${originalSize} -> ${newSize} bytes (Saved as ${path.basename(minPath)})`);
    } catch (e) {
        console.error(`Error processing ${filePath}: ${e.message}`);
    }
}

const files = [
    { path: 'public/css/custom.css', type: 'css' },
    { path: 'public/js/premium-app.js', type: 'js' }
];

console.log("Starting asset minification...");
files.forEach(file => {
    const fullPath = path.join(process.cwd(), file.path);
    if (fs.existsSync(fullPath)) {
        processFile(fullPath, file.type);
    } else {
        console.log(`File not found: ${fullPath}`);
    }
});
console.log("Minification complete.");
