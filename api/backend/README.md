# Laravel Project Installation Guide

## Prerequisites

Make sure the following are installed on your system:

- **PHP**: `>=8.2.22`  
- **Composer**: `>=2.7.7`
- **Node.js**: `>=18.19.0`
- **npm/yarn**: To manage frontend assets  
- **MySQL**: Or any other supported database server
- **Git**: Version control system  
- **[Optional] Docker and Docker Compose**: For containerized setup

## Installation Steps

### 1. Clone the Repository
```bash
git clone <repository_url> project-name
cd project-name

### 1. Clone the Repository
```bash
cp .env.example .env

### 2. Update Database Configuration
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=secure_password

### 3.Install PHP dependencies using Composer:
```bash
composer install

### 4.Install Javascript dependencies:
```bash
npm install

### 5.Generate Application Key:
```bash
php artisan key:generate

### 6.Run Migration (Optional) Can directly run migrations using mysql files:
```bash
php artisan migrate
#### Seed the database
```bash
php artisan db:seed # all seeder
php artisan db:seed --class=DocumentsTableSeeder #specific seeder


### 7.Set Directory Permissions:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache


## Command run in project

### 1. Run the Application
```bash
php artisan serve
#http://127.0.0.1:8000.

### 1. Start the Queue Worker
```bash
php artisan queue:work


