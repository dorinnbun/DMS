<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\v1\ParentApiController;
use App\Http\Resources\Api\UserResource;

class AuthController extends ParentApiController
{
  protected AuthService $auth_service;
  
  protected $service;
  protected $model;

  public function __construct(AuthService $auth_service)
  {
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
    $this->auth_service = $auth_service;
  }

  public function login(AuthRequest $request)
  {
    try {
      $this->auth_service->request = $request;
      $token = $this->auth_service->login();

      if (!$token) return $this->errorResponse('Unauthorized', 401);

      $user = UserResource::make($this->auth_service->auth_user(), $token);

      return $this->response_json($user, "Successfully login");

    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function register(AuthRequest $request)
  {
    $user_arr = [
      'name'     => $request->name,
      'email'    => $request->email,
      'password' => Hash::make($request->password),
      'role'     => $request->role
    ];
    $user = $this->auth_service->register($user_arr);

    $token = $this->auth_service->get_token($user);

    $user = UserResource::make($user, $token);

    return $this->response_json($user, "Successfully register");
  }

  public function logout()
  {
    $this->auth_service->logout();
    return $this->response_json(null, "Successfully logged out");
    return $this->httpResponse()
    ->setStatus(200)
    ->setMessage("Successfully logged out")
    ->toApiResponse();
  }

  public function refresh()
  {
    $token = $this->auth_service->refresh();

    $user = UserResource::make($this->auth_service->auth_user(), $token);

    return $this->response_json($user, "Successfully refreshed token");

  }

}
