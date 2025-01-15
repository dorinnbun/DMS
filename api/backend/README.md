# Laravel Project Installation Guide

## Prerequisites

Make sure the following are installed on your system:

# Laravel Project Installation Guide

## Prerequisites

Make sure the following are installed on your system:

- **PHP**: `>=8.2.27`  
- **Composer**: `>= 2.8.4`
- **MySQL**: `8.0.37 for Linux on x86_64`
- **Nginx**: `1.18.0`

## Installation Steps

### 1. Clone the Repository
```bash
git@github.com:dorinnbun/DMS.git
cd DMS/api/backend
```


### 1. Clone the Repository
```bash
cp .env.example .env
```

### 2. Update Database Configuration
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=secure_password
```

### 3.Install PHP dependencies using Composer:
```bash
composer install
```
### 4.Install Javascript dependencies:
```bash
npm install
```
### 5.Generate Application Key:
```bash
php artisan key:generate
```
### 6.Run Migration (Optional) Can directly run migrations using mysql files:
```bash
php artisan migrate
```
#### Seed the database
```bash
php artisan db:seed # all seeder
php artisan db:seed --class=DocumentsTableSeeder #specific seeder
```

### 7.Set Directory Permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Command run in project

### 1. Run the Application
```bash
php artisan serve
#http://127.0.0.1:8000.
```
### 1. Start the Queue Worker
```bash
php artisan queue:work
```

# Mysql Server Command

### Mysql execute file
```bash
mysql -u root -p upload_image_backend_db < .upload_image_backend_db.sql
```

