<?php

namespace App\Console\Commands;

use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Console\Command;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run custom code';

    protected $role_service;
    protected $user_service;
    protected $model;

    public function __construct(RoleService $role_service, UserService $user_service)
    {
        parent::__construct();
        $this->role_service = $role_service;
        $this->user_service = $user_service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = 9;
        $user = [
            "name" => "superadmin",
            "email" => "superadmin@gmal.com",
            "password" => "123456",
            "role" => 4
        ];
        $role_create = $this->user_service->deleteUserRole($id, $user);

    }
}
