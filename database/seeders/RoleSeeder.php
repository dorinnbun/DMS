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
    $role_admin_list   = ['guard_name' => 'api', 'name' => 'admin'];
    $role_manager_list = ['guard_name' => 'api', 'name' => 'manager'];
    $role_user_list    = ['guard_name' => 'api', 'name' => 'user'];
    
    $adminRole   = Role::create($role_admin_list);
    $managerRole = Role::create($role_manager_list);
    $userRole    = Role::create($role_user_list);

    $permission_user_view   = Permission::create(['guard_name' => 'api', 'name' => 'view users']);
    $permission_user_create = Permission::create(['guard_name' => 'api', 'name' => 'create users']);
    $permission_user_edit   = Permission::create(['guard_name' => 'api', 'name' => 'edit users']);
    $permission_user_delete = Permission::create(['guard_name' => 'api', 'name' => 'delete users']);
    $permission_user_restore = Permission::create(['guard_name' => 'api', 'name' => 'restore users']);

    $permission_docu_view   = Permission::create(['guard_name' => 'api', 'name' => 'view document']);
    $permission_docu_create = Permission::create(['guard_name' => 'api', 'name' => 'create document']);
    $permission_docu_edit   = Permission::create(['guard_name' => 'api', 'name' => 'edit document']);
    $permission_docu_delete = Permission::create(['guard_name' => 'api', 'name' => 'delete document']);
    $permission_docu_restore = Permission::create(['guard_name' => 'api', 'name' => 'restore document']);

    $adminRole->givePermissionTo($permission_user_view, $permission_user_create, $permission_user_edit, $permission_user_delete, $permission_user_restore);
    $managerRole->givePermissionTo($permission_user_view, $permission_user_create, $permission_user_edit, $permission_user_delete, $permission_user_restore);
    $userRole->givePermissionTo($permission_user_view, $permission_user_create);
    
    $adminRole->givePermissionTo($permission_user_view, $permission_user_create, $permission_user_edit, $permission_user_delete, $permission_user_restore);
    $managerRole->givePermissionTo($permission_user_view, $permission_user_create, $permission_user_edit, $permission_user_delete, $permission_user_restore);
    $userRole->givePermissionTo($permission_user_view, $permission_user_create);

    // $adminRole = Role::where("name", "admin")->first();
    // $permission_view   = Permission::create(['guard_name' => 'api', 'name' => 'product-list']);
    // $permission_create = Permission::create(['guard_name' => 'api', 'name' => 'product-create']);
    // $permission_edit   = Permission::create(['guard_name' => 'api', 'name' => 'product-edit']);
    // $permission_delete = Permission::create(['guard_name' => 'api', 'name' => 'product-delete']);
    // $result = $adminRole->givePermissionTo($permission_view, $permission_create, $permission_edit, $permission_delete);
    // $user = User::find(9);
    // $user->assignRole("admin");
    // echo $result;
  }
}
