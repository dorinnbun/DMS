
# composer require laravel/sanctum
# php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
# php artisan migrate

# composer require tymon/jwt-auth # Deprecate
# php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
# ref: https://jwt-auth.readthedocs.io/en/develop/quick-start/#update-your-user-model

composer require php-open-source-saver/jwt-auth
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"

composer require opcodesio/log-viewer
php artisan log-viewer:publish

composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

composer require ramsey/uuid

composer require laravel/ui
php artisan ui bootstrap
npm install && npm run dev

#Final
php artisan optimize:clear
