import fs from 'fs';
import path from 'path';
import sharp from 'sharp';

const iconsDir = path.resolve('public/icons');

/**
 * Return all SVG icon files in public/icons
 */
function listSvgIcons() {
  if (!fs.existsSync(iconsDir)) {
    throw new Error(`Répertoire introuvable: ${iconsDir}`);
  }
  const files = fs.readdirSync(iconsDir).filter(f => f.toLowerCase().endsWith('.svg'));
  return files.map(f => path.join(iconsDir, f));
}

/**
 * Extract size from filename like icon-192x192.svg → {width:192,height:192}
 */
function parseSizeFromFilename(filename) {
  const base = path.basename(filename).toLowerCase();
  const match = base.match(/(\d+)x(\d+)/);
  if (!match) return null;
  const width = parseInt(match[1], 10);
  const height = parseInt(match[2], 10);
  if (!Number.isFinite(width) || !Number.isFinite(height)) return null;
  return { width, height };
}

async function convertOne(svgPath) {
  const size = parseSizeFromFilename(svgPath);
  const outPath = svgPath.replace(/\.svg$/i, '.png');

  const pipeline = sharp(svgPath, { density: 384 });
  if (size) {
    await pipeline.resize(size.width, size.height, { fit: 'contain' }).png().toFile(outPath);
  } else {
    // Fallback: rasterize at 512x512 if no size in filename
    await pipeline.resize(512, 512, { fit: 'contain' }).png().toFile(outPath);
  }
  return outPath;
}

async function ensureSizes(baseSvg, sizes) {
  const src = path.join(iconsDir, baseSvg);
  if (!fs.existsSync(src)) return;
  for (const dim of sizes) {
    const target = path.join(iconsDir, `icon-${dim}x${dim}.png`);
    const pipeline = sharp(src, { density: 512 });
    await pipeline.resize(dim, dim, { fit: 'contain' }).png().toFile(target);
    // eslint-disable-next-line no-console
    console.log(`Généré: ${path.relative(process.cwd(), target)}`);
  }
}

async function main() {
  // eslint-disable-next-line no-console
  console.log('Conversion SVG → PNG dans public/icons ...');
  const svgs = listSvgIcons();
  if (svgs.length === 0) {
    // eslint-disable-next-line no-console
    console.log('Aucun SVG trouvé.');
    return;
  }

  for (const svg of svgs) {
    const out = await convertOne(svg);
    // eslint-disable-next-line no-console
    console.log(`Converti: ${path.relative(process.cwd(), out)}`);
  }

  // Génère explicitement 192 et 512 si besoin
  await ensureSizes('icon-512x512.svg', [192, 512]);
}

main().catch(err => {
  // eslint-disable-next-line no-console
  console.error(err);
  process.exit(1);
});


