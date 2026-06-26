#!/bin/sh
set -e

# 1. Copy .env if not exists and update defaults for Docker
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    
    # Replace database config and APP_URL for Docker environment
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/g' .env
    sed -i 's/DB_DATABASE=laravel/DB_DATABASE=notification_delay_db/g' .env
    sed -i 's/DB_PASSWORD=/DB_PASSWORD=root/g' .env
    sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8027|g' .env
    
    # Add IAE_API_KEY if not exists
    if ! grep -q "^IAE_API_KEY=" .env; then
        echo "IAE_API_KEY=102022400227" >> .env
    fi
fi

# 2. Generate APP_KEY if it is empty/not set in .env
if ! grep -q "^APP_KEY=.\+" .env; then
    echo "Generating app key..."
    php artisan key:generate
fi

# 3. Wait for database connection
echo "Waiting for database connection..."
php -r '
$host = getenv("DB_HOST") ?: "mysql";
$port = getenv("DB_PORT") ?: 3306;
$db   = getenv("DB_DATABASE") ?: "notification_delay_db";
$user = getenv("DB_USERNAME") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "root";

$max_tries = 30;
$tries = 0;

while ($tries < $max_tries) {
    try {
        new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
        echo "Database is ready!\n";
        exit(0);
    } catch (PDOException $e) {
        echo "Waiting for MySQL at $host:$port... (" . ($tries + 1) . "/$max_tries)\n";
        sleep(2);
        $tries++;
    }
}
exit(1);
'

# 4. Run migrations
echo "Running migrations..."
php artisan migrate --force

# 5. Generate Swagger documentation
echo "Generating Swagger documentation..."
php artisan l5-swagger:generate

# 6. Clear caches
echo "Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 7. Execute the main container command
echo "Starting application..."
exec "$@"
