#!/bin/bash

# Configuration des couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Démarrage de l'application Saar Sinistre${NC}"

# Fonction pour vérifier la connectivité à un service
wait_for_service() {
    local host=$1
    local port=$2
    local service_name=$3
    
    echo -e "${YELLOW}⏳ Attente de $service_name ($host:$port)...${NC}"
    
    while ! nc -z "$host" "$port" >/dev/null 2>&1; do
        sleep 1
    done
    
    echo -e "${GREEN}✓ $service_name est prêt !${NC}"
}

# Attendre les services requis
if [ "$DB_HOST" != "localhost" ] && [ -n "$DB_HOST" ]; then
    wait_for_service "$DB_HOST" "${DB_PORT:-3306}" "Base de données"
fi

if [ "$REDIS_HOST" != "localhost" ] && [ -n "$REDIS_HOST" ]; then
    wait_for_service "$REDIS_HOST" "${REDIS_PORT:-6379}" "Redis"
fi

# Test de connectivité base de données
echo -e "${YELLOW}🔍 Test de connectivité à la base de données...${NC}"
if php artisan db:show >/dev/null 2>&1; then
    echo -e "${GREEN}✓ Connexion base de données OK${NC}"
else
    echo -e "${RED}✗ Erreur de connexion à la base de données${NC}"
    # Continuer quand même, les migrations pourraient résoudre le problème
fi

# Exécution des migrations
echo -e "${YELLOW}🔧 Exécution des migrations...${NC}"
if php artisan migrate --force; then
    echo -e "${GREEN}✓ Migrations terminées${NC}"
else
    echo -e "${RED}✗ Erreur lors des migrations${NC}"
    exit 1
fi

# Création du lien symbolique si nécessaire
if [ ! -L "public/storage" ]; then
    echo -e "${YELLOW}🔗 Création du lien symbolique storage...${NC}"
    php artisan storage:link
    echo -e "${GREEN}✓ Lien symbolique créé${NC}"
fi

# Optimisation du cache en production
if [ "$APP_ENV" = "production" ]; then
    echo -e "${YELLOW}⚡ Optimisation pour la production...${NC}"
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo -e "${GREEN}✓ Cache optimisé${NC}"
else
    echo -e "${YELLOW}🔧 Mode développement - nettoyage du cache...${NC}"
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

# Vérification des permissions
echo -e "${YELLOW}🔐 Vérification des permissions...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions configurées${NC}"

# Démarrage de Supervisor pour les tâches en arrière-plan
if [ -f /etc/supervisor/conf.d/supervisord.conf ]; then
    echo -e "${YELLOW}👥 Démarrage de Supervisor...${NC}"
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &
    echo -e "${GREEN}✓ Supervisor démarré${NC}"
fi

# Affichage des informations système
echo -e "${BLUE}📋 Informations système :${NC}"
echo -e "  • Version PHP: $(php -v | head -n 1)"
echo -e "  • Environnement: $APP_ENV"
echo -e "  • URL: $APP_URL"
echo -e "  • Base de données: $DB_CONNECTION sur $DB_HOST:$DB_PORT"
echo -e "  • Cache: Redis sur $REDIS_HOST:$REDIS_PORT"

# Test de l'application
echo -e "${YELLOW}🧪 Test de l'application...${NC}"
if php artisan about >/dev/null 2>&1; then
    echo -e "${GREEN}✓ Application prête !${NC}"
else
    echo -e "${YELLOW}⚠ L'application semble avoir des problèmes mais continue le démarrage${NC}"
fi

echo -e "${GREEN}🎉 Démarrage d'Apache...${NC}"
exec apache2-foreground