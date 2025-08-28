# Configuration
IMAGE_NAME="saarsinistre"
DOCKER_HUB_USERNAME="xdcondor" 
VERSION="latest"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' 

echo -e "${GREEN}=== Construction de l'image Docker pour Saar Sinistre ===${NC}"

if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Erreur: Docker n'est pas en cours d'exécution${NC}"
    exit 1
fi

echo -e "${YELLOW}Construction de l'image Docker...${NC}"
docker build -t ${IMAGE_NAME}:${VERSION} .

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Image construite avec succès${NC}"
else
    echo -e "${RED}✗ Erreur lors de la construction de l'image${NC}"
    exit 1
fi

echo -e "${YELLOW}Taggage de l'image pour Docker Hub...${NC}"
docker tag ${IMAGE_NAME}:${VERSION} ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:${VERSION}
docker tag ${IMAGE_NAME}:${VERSION} ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:latest

echo -e "${YELLOW}Connexion à Docker Hub...${NC}"
echo "Veuillez vous connecter à Docker Hub:"
docker login

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Connexion réussie${NC}"
else
    echo -e "${RED}✗ Erreur de connexion à Docker Hub${NC}"
    exit 1
fi

echo -e "${YELLOW}Publication de l'image sur Docker Hub...${NC}"
docker push ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:${VERSION}
docker push ${DOCKER_HUB_USERNAME}/${IMAGE_NAME}:latest

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Image publiée avec succès sur Docker Hub${NC}"
    echo -e "${GREEN}Votre image est disponible à: https://hub.docker.com/r/${DOCKER_HUB_USERNAME}/${IMAGE_NAME}${NC}"
else
    echo -e "${RED}✗ Erreur lors de la publication${NC}"
    exit 1
fi

echo -e "${GREEN}=== Processus terminé avec succès ===${NC}"
