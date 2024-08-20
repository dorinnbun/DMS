<?php

namespace App\Services;

use App\Models\Province;
use App\Http\Resources\Api\ProvinceResource;

class ProvinceService extends BaseService
{
  protected $model;
  protected $resourceClass = ProvinceResource::class;

  public function __construct(Province $province)
  {
    $this->model = $province;
  }

  public function getProvinceLists()
  {
    return $this->queryBuilder();
  }

  public function getProvince()
  {
    return $this->getList();
  }

  public function getProvinceDistrict($province_id)
  {
    return $this->getByIdWithRelation($province_id, ['districts']);
  }

  public function getDistrictCommunes($district_id, $override_model= null)
  {
    if($override_model) $this->model = $override_model;
    return $this->getByIdWithRelation($district_id, ['communes']);
  }

  // public function restore()
  // {
  //   // todo: restore province record deleted
  // }

  public function getByUuid($uuid)
  {
    $province = $this->model->where('uuid', $uuid)->first();
    if (!$province)
      return $this->notFound();

    return new Province($province);
  }

}
