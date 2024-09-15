<?php

namespace App\Http\Resources\Api;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   * login and register contain token
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    $role_list = [
      'id'         => $this->id,
      'name'       => $this->name,
      'guard_name' => $this->email,
    ];
    return $role_list;
  }
}
