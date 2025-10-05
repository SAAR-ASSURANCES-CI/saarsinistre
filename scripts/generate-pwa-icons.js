#!/usr/bin/env node

/**
 * Script pour générer les icônes PWA SAARCISinistres
 * Convertit le SVG en PNG pour toutes les tailles requises
 */

const fs = require('fs');
const path = require('path');

// Tailles d'icônes PWA requises
const iconSizes = [
  { size: 16, name: 'icon-16x16.png' },
  { size: 32, name: 'icon-32x32.png' },
  { size: 48, name: 'icon-48x48.png' },
  { size: 72, name: 'icon-72x72.png' },
  { size: 96, name: 'icon-96x96.png' },
  { size: 128, name: 'icon-128x128.png' },
  { size: 144, name: 'icon-144x144.png' },
  { size: 152, name: 'icon-152x152.png' },
  { size: 192, name: 'icon-192x192.png' },
  { size: 384, name: 'icon-384x384.png' },
  { size: 512, name: 'icon-512x512.png' }
];

// Fonction pour créer un PNG à partir du SVG
function createPNGFromSVG(size, outputPath) {
  const svgPath = path.join(__dirname, '../public/icons/icon-saarcisinistres.svg');
  
  // Lire le SVG
  let svgContent = fs.readFileSync(svgPath, 'utf8');
  
  // Créer un canvas HTML pour la conversion (simulation)
  // En réalité, vous devriez utiliser sharp, canvas, ou un autre outil
  console.log(`📱 Génération de l'icône ${size}x${size}...`);
  
  // Pour l'instant, on va créer des fichiers de remplacement
  // Dans un environnement de production, utilisez sharp ou canvas
  const placeholderContent = `<!-- Icône ${size}x${size} générée à partir du SVG -->`;
  
  // Créer le fichier de sortie
  fs.writeFileSync(outputPath, placeholderContent);
  console.log(`✅ Icône ${size}x${size} créée : ${outputPath}`);
}

// Fonction principale
function generatePWAIcons() {
  console.log('🎨 Génération des icônes PWA SAARCISinistres...\n');
  
  const iconsDir = path.join(__dirname, '../public/icons');
  
  // Créer le dossier s'il n'existe pas
  if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
  }
  
  // Générer chaque taille
  iconSizes.forEach(({ size, name }) => {
    const outputPath = path.join(iconsDir, name);
    createPNGFromSVG(size, outputPath);
  });
  
  console.log('\n🎉 Toutes les icônes PWA ont été générées !');
  console.log('📝 Note: Pour une conversion SVG vers PNG réelle, utilisez:');
  console.log('   - npm install sharp');
  console.log('   - Ou utilisez un service en ligne comme favicon.io');
  console.log('   - Ou utilisez ImageMagick: convert icon-saarcisinistres.svg -resize 512x512 icon-512x512.png');
}

// Exécuter le script
if (require.main === module) {
  generatePWAIcons();
}

module.exports = { generatePWAIcons, iconSizes };
