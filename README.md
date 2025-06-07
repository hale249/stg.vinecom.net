# Vinecom

## Setup development 
````angular2html
copy .env.example .env  # setting config
docker compose up -d

docker exec -it laravel-app bash
composer install
php artisan key:generate
php artisan migrate


# Run seeders file 
seeder.sql


=> Open http://localhost:802
````
