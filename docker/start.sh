echo "Attente de la base de données..."
while ! mysqladmin ping -h"db" -u"root" -p"root_password" --silent; do
    sleep 1
done

echo "Base de données prête!"

# Exécuter les migrations
echo "Exécution des migrations..."
php artisan migrate --force

# Nettoyer et optimiser le cache
echo "Optimisation du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer Apache
echo "Démarrage d'Apache..."
exec apache2-foreground
