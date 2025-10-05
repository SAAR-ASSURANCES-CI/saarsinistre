#!/bin/bash

# Script pour renommer les icônes générées par favicon.io
# Usage: ./scripts/rename-icons.sh

echo "🔄 Renommage des icônes PWA..."

# Dossier source (où vous avez extrait le ZIP de favicon.io)
SOURCE_DIR="~/Desktop/favicon_package"  # Ajustez selon votre emplacement
TARGET_DIR="public/icons"

# Mapping des noms de fichiers
declare -A icon_mapping=(
    ["favicon-16x16.png"]="icon-16x16.png"
    ["favicon-32x32.png"]="icon-32x32.png"
    ["favicon-48x48.png"]="icon-48x48.png"
    ["favicon-72x72.png"]="icon-72x72.png"
    ["favicon-96x96.png"]="icon-96x96.png"
    ["favicon-128x128.png"]="icon-128x128.png"
    ["favicon-144x144.png"]="icon-144x144.png"
    ["favicon-152x152.png"]="icon-152x152.png"
    ["favicon-192x192.png"]="icon-192x192.png"
    ["favicon-384x384.png"]="icon-384x384.png"
    ["favicon-512x512.png"]="icon-512x512.png"
)

# Renommer chaque fichier
for old_name in "${!icon_mapping[@]}"; do
    new_name="${icon_mapping[$old_name]}"
    
    if [ -f "$SOURCE_DIR/$old_name" ]; then
        cp "$SOURCE_DIR/$old_name" "$TARGET_DIR/$new_name"
        echo "✅ $old_name → $new_name"
    else
        echo "❌ Fichier non trouvé: $old_name"
    fi
done

echo "🎉 Renommage terminé !"
echo "📁 Vérifiez le dossier: $TARGET_DIR"
