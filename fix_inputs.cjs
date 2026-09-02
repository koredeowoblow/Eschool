const fs = require('fs');
const path = require('path');

function processDir(dir) {
    fs.readdirSync(dir).forEach(file => {
        let fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            processDir(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let original = content;
            
            // Remove legacy Bootstrap input group classes that ruin Tailwind search bars
            content = content.replace(/border-start-0\s+ps-0/g, '');
            content = content.replace(/ps-0\s+border-start-0/g, '');
            content = content.replace(/\s+ps-0\s+/g, ' ');
            
            if (content !== original) {
                fs.writeFileSync(fullPath, content);
                console.log(`Fixed inputs in: ${fullPath}`);
            }
        }
    });
}
processDir('C:\\Users\\pc\\Videos\\eschool1\\resources\\views');
