/**
 * Script to fix absolute URLs in compiled CSS files
 * This fixes CORS issues with Material Design Icons fonts
 * Run: node fix-css-fonts.js
 */

const fs = require('fs');
const path = require('path');

const cssDir = path.join(__dirname, 'public', 'build', 'assets');

// Find all CSS files
const cssFiles = fs.readdirSync(cssDir).filter(file => file.endsWith('.css'));

cssFiles.forEach(file => {
    const filePath = path.join(cssDir, file);
    let content = fs.readFileSync(filePath, 'utf8');
    
    // Replace absolute URLs with relative paths for fonts
    content = content.replace(
        /url\(['"]?https?:\/\/[^'"]+\/build\/assets\/([^'"]+\.(woff2?|eot|ttf|otf))[^'"]*['"]?\)/gi,
        (match, filename) => {
            return `url('./${filename}')`;
        }
    );
    
    // Also handle case where URL might not have /build/ in it
    content = content.replace(
        /url\(['"]?https?:\/\/[^'"]+\/assets\/([^'"]+\.(woff2?|eot|ttf|otf))[^'"]*['"]?\)/gi,
        (match, filename) => {
            return `url('./${filename}')`;
        }
    );
    
    // Write the fixed content back
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Fixed: ${file}`);
});

console.log(`\nFixed ${cssFiles.length} CSS file(s). Font URLs are now relative.`);
console.log('Note: For a permanent fix, rebuild assets with: npm run build');
