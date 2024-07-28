<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Controllers\Api\v1\ParentApiController;

class ProductController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(Product $product, ProductService $productService)
  {

    // $this->middleware(PermissionMiddleware::class . ':product-list|product-create|product-edit|product-delete', ['only' => ['dataTable']]);
    // $this->middleware(PermissionMiddleware::class . ':product-create', ['only' => ['create','store']]);
    // $this->middleware(PermissionMiddleware::class . ':product-edit', ['only' => ['edit','update']]);
    // $this->middleware(PermissionMiddleware::class . ':product-delete', ['only' => ['destroy']]);

    $this->model = $product;
    $this->service = $productService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {

    try {

      $user = auth()->user();
      if ($user->can('product-list')) {
        $query = $this->service->getProductLists();
        return parent::dataTable($request, $query);
      }
      
    } catch (\Throwable $e) {
      
      return $this->response_json([], $e->getMessage());
    }
  }

  public function create(Request $request)
  {
    $product_arr = [
      'name'     => $request->name,
    ];
    return $this->service->store($product_arr);
  }
}
