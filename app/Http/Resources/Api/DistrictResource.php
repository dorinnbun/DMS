<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
  }
  /**
   * Transform the resource into an array.
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    $district_list =$this->districts->map(function ($district) {
        return [
          'id'   => $district->id,
          'name' => $district->name,
          'name_in_english' => $district->name_in_english
        ];
      });
    return $district_list;
  }
}
