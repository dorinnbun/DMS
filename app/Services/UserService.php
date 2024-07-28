<?php

namespace App\Services;

use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\UserResource;

class UserService extends BaseService
{
  protected $model;
  protected $role;
  protected $resourceClass = UserResource::class;

  public function __construct(User $user, UserRoleService $role)
  {
    $this->model = $user;
    $this->role = $role;
  }

  public function getUserLists()
  {
    return $this->queryBuilder();
  }

  public function getByUuid($uuid)
  {
    $user = $this->model->where('uuid', $uuid)->first();
    if (!$user)
      return $this->notFound();

    return new User($user);
  }

  public function updateUserRole($user_id, $attr)
  {
    // if (count($attr) > 0) {
    // }
    $user = $this->getById($user_id);
    $this->role->attachSingleRole($user, $attr['role']);
    // $this->role->attachRole($user, $attr->input('role'));
  }

  public function deleteUserRole($user_id, $attr)
  {
    $user = $this->getById($user_id);
    $this->role->detachRole($user, $attr['role']);
  }

  public function syncUserRole($user_id, $attr)
  {
    $user = $this->getById($user_id);
    $this->role->syncRole($user, $attr['role']);
  }

  public function updatePivotIdByParentId($user_id, $role_id, $attributes)
  {
    $modelObj = $this->getById($user_id);
    if (!$modelObj) {
      return false;
    }
    $currentRoleId = $modelObj->role->id;
    if (count($attributes) < 0) {
      DB::transaction(function () use ($modelObj, $currentRoleId, $role_id) {
        // Detach the current role
        $modelObj->roles()->detach($currentRoleId);

        // Attach the new role
        $modelObj->roles()->attach($role_id);
      });
      // $modelObj->roles()->sync([$role_id]);
    }
  }
}
