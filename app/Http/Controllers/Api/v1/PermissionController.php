<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\v1\ParentApiController;

class PermissionController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(Permission $permission, PermissionService $permissionService)
  {
    $this->model = $permission;
    $this->service = $permissionService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {
    $query = $this->service->getPermissionLists();
    return parent::dataTable($request, $query);
  }

  public function create(Request $request): JsonResponse
  {
    $permission_create = $this->service->createPermission($request->all());
    return $this->response_json($permission_create, "successfully create permission");
  }

  public function update(Request $request, $id): JsonResponse
  {
    $permission_update = $this->service->updatePermission($id, $request->all());
    return $this->response_json($permission_update, "successfully update permission");
  }

  public function delete($id): JsonResponse
  {
    $permission_delete = $this->service->destroyPermission($id);
    return $this->response_json($permission_delete, "successfully delete permission");
  }
}
