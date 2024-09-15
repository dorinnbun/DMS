<?php

namespace App\Http\Resources\Api;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
  public $token;

  public function __construct($resource, $token)
  {
      $this->token = $token;
      parent::__construct($resource);
  }
  /**
   * Transform the resource into an array.
   * login and register contain token
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    $product_list = [
      'id'    => $this->id,
      'name'  => $this->name,
    ];
    return $product_list;
  }
}
