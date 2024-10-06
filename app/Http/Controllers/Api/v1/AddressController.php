<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Province;
use Illuminate\Http\Request;
use App\Services\ProvinceService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Api\CommuneResource;
use App\Http\Resources\Api\DistrictResource;
use App\Http\Controllers\Api\v1\ParentApiController;
use App\Models\District;

class AddressController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(
    Province $province,
    ProvinceService $provinceService,
  )
  {
    $this->model = $province;
    $this->service = $provinceService;
  }

  public function dataTable(Request $request, $query = null)
  {
    $query = $this->service->getProvinceLists();
    return parent::dataTable($request, $query);
  }

  public function getProvince(Request $request)
  {
    $province = $this->service->getProvince();
    return $this->response_json($province, __('messages.successfully_retrieved'));
  }

  public function getDistrictByProvince(Request $request, $province_id)
  {
    $district = $this->service->getProvinceDistrict($province_id);
    return $this->response_json(DistrictResource::make($district), __('messages.successfully_retrieved'));
  }

  public function getCommunesByDistrict(Request $request, $district_id)
  {
    $this->model = new District();
    $communes = $this->service->getDistrictCommunes($district_id, $this->model);
    return $this->response_json(CommuneResource::make($communes), __('messages.successfully_retrieved'));
  }

  public function address(Request $request)
  {
    $provinces = Province::with('districts.communes')->get();
    return $provinces;
  }
}
