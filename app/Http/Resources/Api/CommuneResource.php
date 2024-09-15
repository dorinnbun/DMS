<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CommuneResource extends JsonResource
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
    $commune_list =$this->communes->map(function ($commune) {
        return [
          'id'   => $commune->id,
          'name' => $commune->name
        ];
      });
    return $commune_list;
  }
}
