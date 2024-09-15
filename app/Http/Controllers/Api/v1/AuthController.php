<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Notifications\OtpNotify;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AuthUpdateRequest;
use App\Http\Resources\Api\AuthResource;
use App\Http\Controllers\Api\v1\ParentApiController;

class AuthController extends ParentApiController
{
  protected AuthService $auth_service;
  
  protected $service;
  protected $model;
  protected $otp;

  public function __construct(AuthService $auth_service,OtpService $otp)
  {
    $this->middleware('auth:api', ['except' => ['login','verifyOtp']]);
    $this->auth_service = $auth_service;
    $this->otp = $otp;
  }

  public function login(AuthRequest $request)
  {
    try {
      $this->auth_service->request = $request;

      $user = $this->auth_service->getByEmail($request->input("email",''));
      $token = $this->auth_service->login();
      
      if ( !$user ) throw_exception(__('messages.not_found', ['attribute' => 'USER']), 404);

      $this->otp->setUser($user);
      $otp = $this->otp->sendOtp();
      $enrollmentData = [
        "body" => "You received OTP for recovery",
        "enrollmentText" => $otp,
        "url" => url('/'),
        "thankyou" => "You have 5 minutes."
      ];
      log_debug("enrollmentData", $enrollmentData);
      $user->notify(new OtpNotify($enrollmentData));

      // if (!$token) return $this->errorResponse(__('messages.unauthorized'), 401);

      // $user = AuthResource::make($this->auth_service->auth_user(), $token);

      return $this->response_json([], __('messages.successfully_otp'));

    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function verifyOtp(Request $request)
  {
    try {

      $this->auth_service->request = $request;

      $user = $this->auth_service->getByEmail($request->input("email",'')); //Get User FOR UPDATE
      
      if ( !$user ) throw_exception(__('messages.not_found', ['attribute' => 'USER']), 404);
      
      $token = $this->auth_service->login();
      
      $this->otp->setUser($user);

      if ( !$this->otp->getUserOtp() ) throw_exception(__("messages.expire_otp", ["attribute" => "OTP" ]), 401);

      if ( $this->otp->getUserOtp() != $request->otp ) throw_exception(__("messages.invalide", ["attribute" => "OTP" ]), 401);

      if (!$token) return $this->errorResponse(__('messages.unauthorized'), 401);

      $user = AuthResource::make($this->auth_service->auth_user(), $token);

      return $this->response_json($user, __('messages.successfully_login'));

    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function register(AuthRequest $request)
  {
    try {

      $user = auth()->user();
      if ( !$user->can("create user") ){
        throw_exception(__('messages.forbidden_action'), 401);
      }

      $user_arr = [
        'name'         => $request->name,
        'email'        => $request->email,
        'phone_number' => $request->phone_number,
        'password'     => Hash::make($request->password),
        'role_id'      => $request->role ?? 3
      ];
      $user = $this->auth_service->register($user_arr, $request->role??3);
  
      $token = $this->auth_service->get_token($user);
  
      $user = AuthResource::make($user, $token);
  
      return $this->response_json($user, __('messages.successfully_created', ['attribute' => 'user']));
      
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function logout()
  {
    try {
      $this->auth_service->logout();
      return $this->httpResponse()
      ->setStatus(200)
      ->setMessage(__('messages.successfully_logout'))
      ->toApiResponse();
      
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function refresh()
  {
    try {
      $token = $this->auth_service->refresh();
  
      $user = AuthResource::make($this->auth_service->auth_user(), $token);
  
      return $this->response_json($user, __('messages.successfully_refreshed_token'));
      
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }

  }

}
