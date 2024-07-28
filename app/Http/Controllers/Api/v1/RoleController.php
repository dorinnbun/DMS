<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\v1\ParentApiController;

class RoleController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(Role $role, RoleService $roleService)
  {
    $this->model = $role;
    $this->service = $roleService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {
    $query = $this->service->getRoleLists();
    return parent::dataTable($request, $query);
  }

  public function create(Request $request): JsonResponse
  {
    $role_create = $this->service->createRole($request->all());
    return $this->response_json($role_create, "successfully create role");
  }

  public function getRole($id): JsonResponse
  {
    $role_create = $this->service->getRole($id);
    return $this->response_json($role_create, "successfully create role");
  }

  public function update(Request $request, $id): JsonResponse
  {
    $role_create = $this->service->updateRole($id, $request->all());
    return $this->response_json($role_create, "successfully update role");
  }

  public function delete($id): JsonResponse
  {
    $role_create = $this->service->destroyRole($id);
    return $this->response_json($role_create, "successfully delete role");
  }
}
