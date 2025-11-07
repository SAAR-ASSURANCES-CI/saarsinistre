import { readFileSync, writeFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import { createHash } from 'crypto';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const projectRoot = join(__dirname, '..');


const generateVersion = () => {
    const now = new Date();
    const timestamp = now.getTime();
    // Format: YYYYMMDDHHMMSS
    const dateStr = now.toISOString().replace(/[-:T.]/g, '').slice(0, 14);
    return `${dateStr}-${timestamp}`;
};

// Mettre à jour le service worker
const updateServiceWorker = () => {
    const swPath = join(projectRoot, 'public', 'sw.js');
    const swTemplatePath = join(projectRoot, 'public', 'sw.template.js');
    
    try {
        let swContent;
        try {
            swContent = readFileSync(swTemplatePath, 'utf-8');
            console.log('✓ Utilisation du template sw.template.js');
        } catch {
            swContent = readFileSync(swPath, 'utf-8');
            console.log('✓ Utilisation du fichier sw.js existant');
        }
        
        // Générer la nouvelle version
        const version = generateVersion();
        
        // Remplacer le placeholder
        const updatedContent = swContent.replace(/__SW_VERSION__/g, version);
        
        // Écrire le fichier mis à jour
        writeFileSync(swPath, updatedContent, 'utf-8');
        
        console.log(`✓ Service Worker mis à jour avec la version: ${version}`);
        
        updateEnvVersion(version);
        
        return version;
    } catch (error) {
        console.error('✗ Erreur lors de la mise à jour du Service Worker:', error.message);
        process.exit(1);
    }
};

// Mettre à jour la version dans .env
const updateEnvVersion = (version) => {
    const envPath = join(projectRoot, '.env');
    
    try {
        let envContent = readFileSync(envPath, 'utf-8');
        
        // Vérifier si ASSET_VERSION existe déjà
        if (envContent.includes('ASSET_VERSION=')) {
            envContent = envContent.replace(/ASSET_VERSION=.*/g, `ASSET_VERSION=${version}`);
        } else {
            // Ajouter ASSET_VERSION à la fin du fichier
            envContent += `\n# Asset versioning for cache busting\nASSET_VERSION=${version}\n`;
        }
        
        writeFileSync(envPath, envContent, 'utf-8');
        console.log(`✓ Fichier .env mis à jour avec ASSET_VERSION=${version}`);
    } catch (error) {
        console.warn('⚠ Impossible de mettre à jour .env:', error.message);
        console.warn('  Assurez-vous de définir ASSET_VERSION manuellement dans .env');
    }
};

// Exécuter le script
console.log('\n🔄 Mise à jour de la version du cache...\n');
const version = updateServiceWorker();
console.log('\n✅ Mise à jour terminée!\n');
console.log(`   Version générée: ${version}`);
console.log('   Pensez à exécuter "php artisan config:clear" en production\n');

