<?php

namespace App\Http\Resources\Api;

use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
      'uuid'         => $this->uuid,
      'role'         => $this->getRole->name,
    ];

    if (is_JWT_token($this->token)) {
      $user_list['authorisation'] = [
        'token' => $this->token,
        'type' => 'bearer',
      ];
    }
    return $user_list;
  }
}
