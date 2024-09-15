<?php

namespace App\Services;

use App\Http\Resources\Api\ProductResource;
use App\Models\Product;

class ProductService extends BaseService
{
  protected $model;
  protected $resourceClass = ProductResource::class;

  public function __construct(Product $product)
  {
    $this->model = $product;
  }

  public function getProductLists()
  {
    return $this->queryBuilder();
  }

  public function getByUuid($uuid)
  {
    $product = $this->model->where('uuid', $uuid)->first();
    if (!$product)
      return $this->notFound();

    return new Product($product);
  }

  public function store(Array $product)
  {
    $product_model = $this->create($product);
    return $product_model;
  }
}
