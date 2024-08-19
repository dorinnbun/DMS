<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\UserRoleService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\v1\ParentApiController;

class UserController extends ParentApiController
{
  protected $service;
  protected $userRoleService;
  protected $model;

  public function __construct(
    User $user,
    UserService $userService,
    UserRoleService $userRoleService,
  )
  {
    $this->model = $user;
    $this->service = $userService;
    $this->userRoleService = $userRoleService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {
    $query = $this->service->getUserLists();
    return parent::dataTable($request, $query);
  }

  public function update(Request $request, $id): JsonResponse
  {
    try {
      // User update
      $user = $this->service->getById($id);
  
      // Role update
      $role_remove = $this->userRoleService->remove_role($user);
      $role_assign = $this->userRoleService->assign_role($user,$request->all()['role']);
      return $this->response_json($role_assign, "successfully update user");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function delete(Request $request, $id)
  {
    try {
      $user = $this->service->delete($id);
      return $this->response_json($user, "Successfully delete user");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function restore(Request $request, $id)
  {
    try {
      $user = $this->service->restore($id);
      return $this->response_json($user, "Successfully restore user");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }
}
