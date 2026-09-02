const fs = require('fs');
const path = require('path');

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    const original = content;

    // Remove the redundant nested card-body wrapper classes
    content = content.replace(/class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden-body p-0"/g, 'class="p-0"');
    
    // Clean up the bloated and erroneous table classes
    content = content.replace(/class="w-full text-sm text-left divide-y divide-gray-200 align-middle\s+mb-6\s+table-mobile-bg-white rounded-xl shadow-sm border border-gray-200 overflow-hiddens"/g, 'class="w-full"');

    // Clean up any other weird table classes that might have been left behind
    content = content.replace(/class="([^"]*)overflow-hidden-body([^"]*)"/g, 'class="$1$2"');
    content = content.replace(/class="([^"]*)table-mobile-bg-white([^"]*)"/g, 'class="$1$2"');
    content = content.replace(/class="([^"]*)overflow-hiddens([^"]*)"/g, 'class="$1$2"');

    if (content !== original) {
        fs.writeFileSync(filePath, content, 'utf8');
        return true;
    }
    return false;
}

function walkDir(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walkDir(file));
        } else {
            if (file.endsWith('.blade.php')) {
                results.push(file);
            }
        }
    });
    return results;
}

const viewsDir = path.join(__dirname, 'resources', 'views');
const files = walkDir(viewsDir);
let changed = 0;

files.forEach(file => {
    if (processFile(file)) {
        changed++;
        console.log('Fixed tables in:', file);
    }
});

console.log(`\nCompleted! Fixed ${changed} files.`);
