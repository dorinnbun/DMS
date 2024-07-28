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

  public function createPermission($role_admin_list)
  {
    $permission = $this->create($role_admin_list);
    return $this->make_resource($permission);
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
}
