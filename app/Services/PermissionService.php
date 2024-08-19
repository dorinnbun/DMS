<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Http\Resources\Api\PermissionResource;
use App\Models\Permission;
use Symfony\Component\CssSelector\Node\FunctionNode;

class PermissionService extends BaseService
{
  protected $model;
  protected $resourceClass = PermissionResource::class;

  public function __construct(Permission $role)
  {
    $this->model = $role;
  }

  public function getPermissionLists()
  {
    return $this->queryBuilder();
  }

  public function createPermission($permission_list)
  {
    foreach ($permission_list as $name) {
      if (is_array($name)) {
        foreach ($name as $value) {
          $permission[] = $this->create(['guard_name' => 'api', 'name' => $value]);
        }
      }
    }
    // return $this->make_resource($permission);
    return $permission;
  }

  public function updatePermission($role_id, $attr)
  {
    $permission = $this->updateById($role_id, $attr);
    return $this->make_resource($permission);
  }

  public function destroyPermission($role_id)
  {
    $permission = $this->delete($role_id);
    return $this->make_resource($permission);
  }

  public function give_permission($user_mdoel, $permission_list)
  {
    return $user_mdoel->givePermissionTo($permission_list);
  }

  public function revoke_permission($user_mdoel, $permission_list)
  {
    return $user_mdoel->revokePermissionTo($permission_list);
  }

  public function destroy_role($user_mdoel)
  {
    return $user_mdoel->delete();
  }
}
