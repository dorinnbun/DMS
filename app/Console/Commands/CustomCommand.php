<?php

namespace App\Console\Commands;

use App\Models\Province;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Console\Command;
use App\Services\PermissionService;

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
    protected $permission_service;
    protected $user_service;
    protected $model;

    public function __construct(
        RoleService $role_service,
        PermissionService $permission_service,
        UserService $user_service
        )
    {
        parent::__construct();
        $this->role_service = $role_service;
        $this->permission_service = $permission_service;
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
            "role" => [1,2,3,4]
        ];
        // $role_create = $this->user_service->addSingleUserRole($id, $user);
        // $role_create = $this->user_service->deleteUserRole($id, $user);
        // $role_create = $this->user_service->syncUserRole($id, $user);

        // $provinces = Province::with('districts.communes')->get();

    }
}
