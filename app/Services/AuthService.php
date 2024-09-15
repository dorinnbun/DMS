<?php

namespace App\Services;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\AuthResource;

class AuthService extends BaseService
{
  protected $model;
  protected $role;
  protected $resourceClass = AuthResource::class;
  public $request;

  public function __construct(User $user, RoleService $role_service)
  {
    $this->model = $user;
    $this->role = $role_service;
  }

  public function login()
  {
    $credentials = $this->request->only('email', 'password');
    $token = Auth::attempt($credentials);
    return $token;
  }

  public function getByEmail($email)
  {
    $user = $this->model->where('email', $email)->first();
    if (!$user)
      return throw_exception(__('messages.not_found', ['attribute' => 'USER']), 404);
    return $user;
  }

  public function register(Array $user, $role_id)
  {
    $user_model = $this->create($user);
    $user_model = $this->role->assign_role($user_model, $role_id);
    // $user_model->assignRole($this->role->getRoleEnumValue($user['role']));
    return $user_model;
  }

  public function logout()
  {
    Auth::logout();
  }

  public function refresh()
  {
    return Auth::refresh();
  }

  public function auth_user()
  {
    return Auth::user();
  }

  public function get_token(User $user)
  {
    return Auth::login($user);
  }
}