<?php

namespace App\Services;


use App\Traits\ApiResponse;
use App\Http\Resources\Api\PaginateResource;


class BaseService
{
  use ApiResponse;

  protected $model;
  protected $resourceClass;
  protected $limit;

  public function queryBuilder()
  {
    return $this->model;
  }

  public function getList()
  {
    return $this->model->all()->toArray();
  }

  public function getListByAuth($column_user_id)
  {
    return $this->model->where($column_user_id, auth()->user()->getAuthIdentifier())
      ->orderBy('id', 'DESC')->get();
  }

  public function getById($id)
  {
    return $this->model->find($id);
  }

  public function getIncludeSoftDeleteById($id)
  {
    return $this->model->withTrashed()->find($id);
  }

  public function getOnlySoftDeleteById($id)
  {
    return $this->model->onlyTrashed()->find($id);
  }

  public function getByUuid($uuid)
  {
    return $this->model->where('uuid', $uuid)->first();
  }

  public function getByIdWithRelation($id, $relations = [])
  {
    return $this->model->with($relations)->find($id);
  }

  public function create($attributes)
  {
    return $this->model->create($attributes);
  }

  public function updateOrCreate($attributes)
  {
    if (!$attributes) {
      return false;
    }
    return $this->model->updateOrCreate($attributes);
  }

  public function updateById($id, $attribute)
  {
    $modelObj = $this->getById($id);
    if (!$modelObj) {
      return false;
    }
    $result = $modelObj->fill($attribute);
    $result->update();
    return $result;
  }

  public function delete($id)
  {
    $result = $this->model->find($id);
    if ($result instanceof $this->model) {
      $result->delete();
      return $result;
    }
    return false;
  }

  public function restore($id)
  {
    $result = $this->model->withTrashed()->find($id);
    $result->restore();
    return $result;
  }

  public function permanentDelete($id)
  {
    $result = $this->model->withTrashed()->find($id);
    $result->forceDelete();
    return $result;
  }

  public function filter($filters, $query)
  {
    if (!$filters) $filters = [];
    if (count($filters) > 0) {
      foreach ($filters as $filter) {
        if ($filter) {
          $query = $query->where(key($filters), $filter);
          next($filters);
        }
      }
    }
    return $query;
  }

  public function search($keys, $value, $query)
  {

    if (!$keys) $keys = [];
    if (count($keys) > 0 && $value) {
      $query = $query->where(function ($query) use ($value, $keys) {
        foreach ($keys as $key) {
          $query->orWhere($key, 'like', '%' . $value . '%');
        }
      });
    }
    return $query;
  }

  public function between($between, $min, $max, $query)
  {
    return $query->whereBetween($between, [$min, $max]);
  }

  public function orderBy($orders, $query)
  {
    if (!$orders) $orders = [];
    if (count($orders) > 0) {
      foreach ($orders as $order) {
        if ($order) {
          $query = $query->orderBy(key($orders), $order);
          next($orders);
        }
      }
    }
    return $query;
  }


  /**
   * Get data with search by search key, filters and paginate
   * $attributes = [
   *      limit: 10 default 50
   *      filters: [
   *          key: value,
   *          ...
   *      ]
   *      keySearch: [
   *          name,
   *          ...
   *      ]
   *      search: hello,
   *      between   : "price",
   *      min       : 0,
   *      max       : 20,
   *      orderBy   : [
   *          key : ASC or DESC
   *          ...
   *      ]
   * ]
   * @param $attributes
   * @param null $dbq
   * @return mixed
   */
  public function getData($attributes, $dbq = null)
  {
    $between = $attributes['between'] ?? '';
    $query   = $dbq ?? $this->model;
    // Check if get limit
    $this->limit = $attributes['limit'] ?? env('LIMIT', 15);
    // Check if get filters
    $filters = $attributes['filters'] ?? [];
    // Check if get keySearch
    $keySearch = $attributes['keySearch'] ?? [];
    // Check if get search
    $search = $attributes['search'] ?? null;
    $query  = $this->filter($filters, $query);
    $query  = $this->search($keySearch, $search, $query);
    //Between
    if ($between) {
      $min   = $attributes['min'];
      $max   = $attributes['max'];
      $query = $this->between($between, $min, $max, $query);
    }
    // OrderBy
    $orders = $attributes['orderBy'] ?? [];
    $query  = $this->orderBy($orders, $query);
    return PaginateResource::make($query->paginate($this->limit), $this->resourceClass);
  }

  public function make_resource($model, $list=[])
  {
    $resourceClassInstance = new $this->resourceClass($model);
    return $resourceClassInstance->make($model);
  }
}
