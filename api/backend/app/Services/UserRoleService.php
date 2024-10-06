<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\RoleService;
use App\Http\Resources\Api\RoleResource;

class UserRoleService extends RoleService
{
  public $model;
  protected $resourceClass = RoleResource::class;

  public function __construct()
  {
    
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
  public function detachRole(User $user, $roleId, $extra_fields=[])
  {
    $attach = $user->roles()->detach($roleId);
    return $attach;
  }
  public function syncRole(User $user,$roleId, $extra_fields=[])
  {
    // sync append list of role, if id exists skip and add all not exists
    $attach = $user->roles()->sync($roleId);
    return $attach;
  }
  public function updateExistingPivotRole(User $user,$roleId, $extra_fields=[])
  {
    // sync append list of role, if id exists skip and add all not exists
    $update_pivot = $user->roles()->updateExistingPivot($roleId, $extra_fields);
    return $update_pivot;
  }

}
