const fs = require("fs");
const path = require("path");
const archiver = require("archiver");

const PLUGIN_SLUG = "order-delivery-date-for-woocommerce";
const DEST_BASE = "dist";
const DEST = path.join(DEST_BASE, PLUGIN_SLUG);
const ZIP_PATH = path.join(DEST_BASE, `${PLUGIN_SLUG}.zip`);

// Files/folders to copy into the distribution.
const INCLUDE = [
    "build",
    "blocks",
    "css",
    "emails",
    "images",
    "includes",
    "js",
    "languages",
    "templates",
    "order_delivery_date.php",
    "block.json",
    "wpml-config.xml",
    "uninstall.php",
    "readme.txt",
    "changelog.txt",
];

if (!fs.existsSync("build")) {
    throw new Error("Missing /build — run `npm run build` first.");
}

// Clean and recreate the dist folder.
rmSync(DEST_BASE);
fs.mkdirSync(DEST, { recursive: true });

console.log(`\nPackaging ${PLUGIN_SLUG}…`);

// Copy all included files.
INCLUDE.forEach((item) => {
    const src = path.resolve(item);
    const dest = path.join(DEST, item);
    if (fs.existsSync(src)) {
        copySync(src, dest);
    } else {
        console.warn(`  ⚠  Skipped missing: ${item}`);
    }
});

// Create ZIP.
const output = fs.createWriteStream(ZIP_PATH);
const archive = archiver("zip", { zlib: { level: 9 } });

archive.on("error", (err) => { throw err; });

archive.pipe(output);
archive.directory(DEST, PLUGIN_SLUG);
archive.finalize();

output.on("close", () => {
    const kb = Math.round(archive.pointer() / 1024);
    console.log(`  ✓ ZIP created: ${ZIP_PATH} (${kb} KB)\n`);
});


// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function copySync(src, dest) {
    const stat = fs.statSync(src);
    if (stat.isDirectory()) {
        fs.mkdirSync(dest, { recursive: true });
        fs.readdirSync(src).forEach((child) => {
            copySync(path.join(src, child), path.join(dest, child));
        });
    } else {
        fs.mkdirSync(path.dirname(dest), { recursive: true });
        fs.copyFileSync(src, dest);
    }
}

function rmSync(dir) {
    if (fs.existsSync(dir)) {
        fs.rmSync(dir, { recursive: true, force: true });
    }
}