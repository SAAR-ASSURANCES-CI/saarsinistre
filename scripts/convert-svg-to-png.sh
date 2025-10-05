#!/bin/bash

# Script pour convertir le SVG SAARCISinistres en icônes PNG
# Utilise ImageMagick ou Inkscape si disponible

echo "🎨 Conversion des icônes PWA SAARCISinistres..."

SVG_FILE="public/icons/icon-saarcisinistres.svg"
ICONS_DIR="public/icons"

# Vérifier si ImageMagick est disponible
if command -v convert &> /dev/null; then
    echo "✅ ImageMagick détecté - Conversion en cours..."
    
    # Tailles d'icônes PWA
    sizes=(16 32 48 72 96 128 144 152 192 384 512)
    
    for size in "${sizes[@]}"; do
        echo "📱 Génération de l'icône ${size}x${size}..."
        convert "$SVG_FILE" -resize "${size}x${size}" "$ICONS_DIR/icon-${size}x${size}.png"
        
        if [ $? -eq 0 ]; then
            echo "✅ Icône ${size}x${size} créée"
        else
            echo "❌ Erreur lors de la création de l'icône ${size}x${size}"
        fi
    done
    
elif command -v inkscape &> /dev/null; then
    echo "✅ Inkscape détecté - Conversion en cours..."
    
    sizes=(16 32 48 72 96 128 144 152 192 384 512)
    
    for size in "${sizes[@]}"; do
        echo "📱 Génération de l'icône ${size}x${size}..."
        inkscape --export-type=png --export-filename="$ICONS_DIR/icon-${size}x${size}.png" --export-width=$size --export-height=$size "$SVG_FILE"
        
        if [ $? -eq 0 ]; then
            echo "✅ Icône ${size}x${size} créée"
        else
            echo "❌ Erreur lors de la création de l'icône ${size}x${size}"
        fi
    done
    
else
    echo "❌ Aucun outil de conversion détecté (ImageMagick ou Inkscape)"
    echo "📝 Installation recommandée:"
    echo "   - ImageMagick: apt-get install imagemagick (Linux) ou brew install imagemagick (macOS)"
    echo "   - Inkscape: apt-get install inkscape (Linux) ou brew install inkscape (macOS)"
    echo ""
    echo "🔄 Alternative: Utilisez un service en ligne comme:"
    echo "   - https://favicon.io/favicon-converter/"
    echo "   - https://realfavicongenerator.net/"
    echo "   - https://www.favicon-generator.org/"
    exit 1
fi

echo ""
echo "🎉 Conversion terminée !"
echo "📱 Toutes les icônes PWA SAARCISinistres ont été générées"
echo "🔍 Vérifiez le dossier: $ICONS_DIR"
