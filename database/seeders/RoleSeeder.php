<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // $role_admin_list = ['guard_name' => 'api', 'name' => 'admin'];
    // $role_user_list = ['guard_name' => 'api', 'name' => 'user'];
    // $adminRole = Role::create($role_admin_list);
    // $userRole = Role::create($role_user_list);

    // $permission_view   = Permission::create(['guard_name' => 'api', 'name' => 'view users']);
    // $permission_create = Permission::create(['guard_name' => 'api', 'name' => 'create users']);
    // $permission_edit   = Permission::create(['guard_name' => 'api', 'name' => 'edit users']);
    // $permission_delete = Permission::create(['guard_name' => 'api', 'name' => 'delete users']);

    // $adminRole->givePermissionTo($permission_view, $permission_create, $permission_edit, $permission_delete);
    // $userRole->givePermissionTo($permission_view, $permission_create, $permission_edit);

    // $adminRole = Role::where("name", "admin")->first();
    // $permission_view   = Permission::create(['guard_name' => 'api', 'name' => 'product-list']);
    // $permission_create = Permission::create(['guard_name' => 'api', 'name' => 'product-create']);
    // $permission_edit   = Permission::create(['guard_name' => 'api', 'name' => 'product-edit']);
    // $permission_delete = Permission::create(['guard_name' => 'api', 'name' => 'product-delete']);
    // $result = $adminRole->givePermissionTo($permission_view, $permission_create, $permission_edit, $permission_delete);
    $user = User::find(9);
    $user->assignRole("admin");
    // echo $result;
  }
}
