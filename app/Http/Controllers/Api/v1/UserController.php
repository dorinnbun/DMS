<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\v1\ParentApiController;

class UserController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(User $user, UserService $userService)
  {
    $this->model = $user;
    $this->service = $userService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {
    $query = $this->service->getUserLists();
    return parent::dataTable($request, $query);
  }

  public function update(Request $request, $id): JsonResponse
  {
    $role_create = $this->service->updateUserRole($id, $request->all());
    return $this->response_json($role_create, "successfully update role");
  }
}
