<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\RoleService;
use App\Http\Resources\Api\RoleResource;

class UserRoleService extends RoleService
{
  protected $model;
  protected $resourceClass = RoleResource::class;

  public function __construct()
  {
    
  }

  public function assign_role($user_mdoel)
  {
    return $user_mdoel->assignRole($this->getRoleEnumValue($user_mdoel['role']));
  }

  public function attachMultipleRole(User $user,$roleIds, $extra_fields=[])
  {
    $existingRoles = $user->roles->pluck('id')->toArray();
    $newRoles = array_diff($roleIds, $existingRoles);

    if (!empty($newRoles)) {
      $attach = $user->roles()->attach($newRoles);
      return $attach;
    }
  }
  public function attachSingleRole(User $user,$roleId, $extra_fields=[])
  {
    if (!$user->roles->contains($roleId)) {
      $attach = $user->roles()->attach($roleId);
      return $attach;
    }
  }
  public function detachRole(User $user, $role_id, $extra_fields=[])
  {
    $attach = $user->roles()->detach($role_id);
    return $attach;
  }
  public function syncRole(User $user, $extra_fields=[])
  {
    $attach = $user->roles()->sync($user->role->id);
    return $attach;
  }

}
