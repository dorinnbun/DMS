<?php

namespace App\Http\Resources\Api;

use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
      'name'  => $this->name,
      'email' => $this->email,
    ];

    if ( isset($this->roles) ){
      $user_list['roles'] = $this->roles->map(function ($role) {
        return [
          'id'   => $role->id,
          'name' => $role->name,
        ];
      });
    }
    return $user_list;
  }
}
