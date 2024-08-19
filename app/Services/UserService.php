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

  public function restore($id)
  {
    // todo: restore user record deleted
  }

  public function getByUuid($uuid)
  {
    $user = $this->model->where('uuid', $uuid)->first();
    if (!$user)
      return $this->notFound();

    return new User($user);
  }

  public function addSingleUserRole($user_id, $attr)
  {
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

}
