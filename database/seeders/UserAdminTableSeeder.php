<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use App\Services\AuthService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserAdminTableSeeder extends Seeder
{

    public $auth_service;
    public function __construct(AuthService $auth_service) {
        $this->auth_service = $auth_service;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $record = [
            "name" => "Admin",
            "email" => "admin@gmail.com",
            "password" => "admin@admin",
        ];
        $role = 2;

        $this->auth_service->register($record, $role);
    }
}
