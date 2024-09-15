<?php

namespace App\Http\Resources\Api;

use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
    $user_list = [
      'id'           => $this->id,
      'name'         => $this->name,
      'email'        => $this->email,
      'phone_number' => $this->phone_number,
      'role'         => $this->getRole->name,
    ];

    // if ( isset($this->roles) ){
    //   $user_list['roles'] = $this->roles->map(function ($role) {
    //     return [
    //       'id'   => $role->id,
    //       'name' => $role->name,
    //     ];
    //   });
    // }

    if (is_JWT_token($this->token)) {
      $user_list['authorisation'] = [
        'token' => $this->token,
        'type' => 'bearer',
      ];
    }
    return $user_list;
  }
}
