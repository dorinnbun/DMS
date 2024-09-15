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

  public function dataTable(Request $request, $query = null)
  {
    $query = $this->service->getRoleLists();
    return parent::dataTable($request, $query);
  }

  public function create(Request $request)
  {
    $role_create = $this->service->createRole($request->all());
    return $this->response_json($role_create, __("messages.successfully_created", ["attribute" => "role" ]));
  }

  public function getRole($id)
  {
    $role_create = $this->service->getRole($id);
    return $this->response_json($role_create, __("messages.successfully_retrieved", ["attribute" => "role" ]));
  }

  public function update(Request $request, $id)
  {
    $role_create = $this->service->updateRole($id, $request->all());
    return $this->response_json($role_create, __("messages.successfully_updated", ["attribute" => "role" ]));
  }

  public function delete($id)
  {
    $role_create = $this->service->destroyRole($id);
    return $this->response_json($role_create, __("messages.successfully_delete", ["attribute" => "role" ]));
  }
}
