<?php

namespace App\Http\Resources\Api;

use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class ProvinceResource extends JsonResource
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
    $user_list = [
      'id'    => $this->id,
      'id'    => $this->code,
      'name'  => $this->name,
      'name_in_english'  => $this->name_in_english,
      'district' => $this->districts->map(function ($district) {
        return [
          'id'   => $district->id,
          'name' => $district->name,
          'name_in_english' => $district->name_in_english,
          'communes' => $district->communes->map(function ($communes) {
            return [
              'id'   => $communes->id,
              'name' => $communes->name,
            ];
          })
        ];
      })

    ];
    return $user_list;
  }
}
