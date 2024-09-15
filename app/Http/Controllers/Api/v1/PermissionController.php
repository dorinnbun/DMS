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

  public function dataTable(Request $request, $query = null)
  {
    $query = $this->service->getPermissionLists();
    return parent::dataTable($request, $query);
  }

  public function create(Request $request)
  {
    $permission_create = $this->service->createPermission($request->all()['permission']);
    return $this->response_json($permission_create, __('messages.successfully_created', ["attribute" => ['permission']]));
  }

  public function update(Request $request, $id)
  {
    $permission_update = $this->service->updatePermission($id, $request->all());
    return $this->response_json($permission_update, __('messages.successfully_updated', ["attribute" => ['permission']]));
  }

  public function delete($id)
  {
    $permission_delete = $this->service->destroyPermission($id);
    return $this->response_json($permission_delete, __('messages.successfully_delete', ["attribute" => ['permission']]));
  }
}