<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Services\PermissionService;
use App\Http\Resources\Api\RoleResource;
use Symfony\Component\CssSelector\Node\FunctionNode;

class RoleService extends BaseService
{
  protected $model;
  protected $resourceClass = RoleResource::class;
  protected $permissionService;
  public function __construct(Role $role, PermissionService $permissionService)
  {
    $this->model = $this->make_resource($role);
    $this->permissionService = $permissionService;
  }

  public function getRoleLists()
  {
    return $this->queryBuilder();
  }

  public function createRole($request)
  {
    try {

      DB::beginTransaction();
      $role = [
        "name" => $request['name'],
      ];
      $role = $this->create($role);
      
      DB::commit();
      return $this->make_resource($role);
      
    } catch (\Throwable $th) {
      DB::rollBack();
    }
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

  public function assign_role($user_mdoel, $role_id)
  {
    $role = $this->getById($role_id);
    return $user_mdoel->assignRole($role->name);
  }

  public function remove_role($user_mdoel)
  {
    $role_id = $user_mdoel->roles()->pluck('id')->first();
    return $user_mdoel->removeRole($role_id);
  }
  

  public function getRoleEnumValue(string $role): ?string
  {
    // return RoleEnum::hasValue($role) ? RoleEnum::fromValue($role) : RoleEnum::GUEST;
    return array_key_exists($role, RoleEnum::toArray()) ? $role : RoleEnum::GUEST->value;
  }

}
