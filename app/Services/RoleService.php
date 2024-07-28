<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Http\Resources\Api\RoleResource;
use Symfony\Component\CssSelector\Node\FunctionNode;

class RoleService extends BaseService
{
  protected $model;
  protected $resourceClass = RoleResource::class;

  public function __construct(Role $role)
  {
    $this->model = $this->make_resource($role);
  }

  public function getRoleLists()
  {
    return $this->queryBuilder();
  }

  public function createRole($role_admin_list)
  {
    // $role_admin_list = ['guard_name' => 'api', 'name' => 'admin'];
    $role = $this->create($role_admin_list);
    return $this->make_resource($role);
  }

  public function getRole($role_id)
  {
    $role = $this->getById($role_id);
    return $this->make_resource($role);
  }

  public function updateRole($role_id, $attr)
  {
    $role = $this->updateById($role_id, $attr);
    return $this->make_resource($role);
  }

  public function destroyRole($role_id)
  {
    $role = $this->delete($role_id);
    return $this->make_resource($role);
  }

  public function assign_role($user_mdoel)
  {
    return $user_mdoel->assignRole($this->getRoleEnumValue($user_mdoel['role']));
  }

/*   public function attachMultipleRole(User $user,$roleIds, $extra_fields=[])
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
  } */

  public function getRoleEnumValue(string $role): ?string
  {
    // return RoleEnum::hasValue($role) ? RoleEnum::fromValue($role) : RoleEnum::GUEST;
    return array_key_exists($role, RoleEnum::toArray()) ? $role : RoleEnum::GUEST->value;
  }

}
