#!/bin/bash

# Configuration
IMAGE_NAME="saarsinistre"
DOCKER_HUB_USERNAME="xdcondor" 
VERSION="latest"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' 

echo -e "${BLUE}=== Construction de l'image Docker pour Saar Sinistre ===${NC}"

# Vérification que Docker est en cours d'exécution
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}✗ Erreur: Docker n'est pas en cours d'exécution${NC}"
    exit 1
fi

# Nettoyage préalable des liens symboliques problématiques
echo -e "${YELLOW}🧹 Nettoyage des fichiers problématiques...${NC}"
if [ -L "public/storage" ]; then
    echo "Suppression du lien symbolique public/storage"
    rm -f public/storage
fi

# Création des répertoires nécessaires
mkdir -p storage/app/public
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Vérification des fichiers essentiels
if [ ! -f "composer.json" ]; then
    echo -e "${RED}✗ Erreur: composer.json non trouvé${NC}"
    exit 1
fi

if [ ! -f "package.json" ]; then
    echo -e "${RED}✗ Erreur: package.json non trouvé${NC}"
    exit 1
fi

# Construction de l'image Docker
echo -e "${YELLOW}🔨 Construction de l'image Docker...${NC}"
docker build \
    --no-cache \
    --progress=plain \
    -t ${IMAGE_NAME}:${VERSION} \
    .

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Image construite avec succès${NC}"
    
    # Affichage de la taille de l'image
    IMAGE_SIZE=$(docker images ${IMAGE_NAME}:${VERSION} --format "table {{.Size}}" | tail -1)
    echo -e "${BLUE}📦 Taille de l'image: ${IMAGE_SIZE}${NC}"
else
    echo -e "${RED}✗ Erreur lors de la construction de l'image${NC}"
    echo -e "${YELLOW}💡 Suggestions de débogage:${NC}"
    echo "1. Vérifiez que tous les fichiers Docker existent (docker/apache/000-default.conf, docker/start.sh)"
    echo "2. Vérifiez le contenu du .dockerignore"
    echo "3. Essayez de construire avec --no-cache"
    exit 1
fi

# Demander confirmation avant publication
read -p "$(echo -e ${YELLOW}Voulez-vous publier l'image sur Docker Hub? [y/N]: ${NC})" -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    
    # Taggage de l'image pour Docker Hub
    echo -e "${YELLOW}🏷️  Taggage de l'image pour Docker Hub...${NC}"
    docker tag ${IMAGE_NAME}:${VERSION} ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:${VERSION}
    docker tag ${IMAGE_NAME}:${VERSION} ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:latest

    # Connexion à Docker Hub
    echo -e "${YELLOW}🔐 Connexion à Docker Hub...${NC}"
    docker login

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Connexion réussie${NC}"
    else
        echo -e "${RED}✗ Erreur de connexion à Docker Hub${NC}"
        exit 1
    fi

    # Publication de l'image sur Docker Hub
    echo -e "${YELLOW}📤 Publication de l'image sur Docker Hub...${NC}"
    docker push ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:${VERSION}
    docker push ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:latest

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Image publiée avec succès sur Docker Hub${NC}"
        echo -e "${GREEN}🌐 Votre image est disponible à: https://hub.docker.com/r/${DOCKER_HUB_USERNAME}/${IMAGE_NAME}${NC}"
    else
        echo -e "${RED}✗ Erreur lors de la publication${NC}"
        exit 1
    fi
else
    echo -e "${BLUE}ℹ️  Image créée localement seulement${NC}"
fi

# Test de l'image
echo -e "${YELLOW}🧪 Voulez-vous tester l'image localement? [y/N]: ${NC}"
read -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}🚀 Démarrage d'un conteneur de test...${NC}"
    docker run --rm -d -p 8080:80 --name saarsinistre-test ${IMAGE_NAME}:${VERSION}
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Conteneur de test démarré${NC}"
        echo -e "${BLUE}🌍 Accédez à votre application sur: http://localhost:8080${NC}"
        echo -e "${YELLOW}⏹️  Pour arrêter le test: docker stop saarsinistre-test${NC}"
    else
        echo -e "${RED}✗ Erreur lors du démarrage du conteneur de test${NC}"
    fi
fi

echo -e "${GREEN}🎉 Processus terminé avec succès${NC}"

# Affichage des commandes utiles
echo -e "${BLUE}📋 Commandes utiles:${NC}"
echo "• Lancer l'image: docker run -d -p 8080:80 ${IMAGE_NAME}:${VERSION}"
echo "• Voir les logs: docker logs <container_id>"
echo "• Entrer dans le conteneur: docker exec -it <container_id> bash"
echo "• Arrêter le conteneur de test: docker stop saarsinistre-test"