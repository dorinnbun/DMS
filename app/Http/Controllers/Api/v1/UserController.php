<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Role;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Notifications\OtpNotify;
use App\Services\UserRoleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Api\UserResource;
use App\Http\Controllers\Api\v1\ParentApiController;

class UserController extends ParentApiController
{
  protected $service;
  protected $userRoleService;
  protected $model;
  protected $otp;

  public function __construct(
    User $user,
    UserService $userService,
    UserRoleService $userRoleService,
    OtpService $otp,
  )
  {
    $this->middleware('auth:api', ['except' => ['forgetPassword', 'verifyOtp', 'resetPassword']]);
    $this->model = $user;
    $this->service = $userService;
    $this->userRoleService = $userRoleService;
    $this->otp = $otp;
  }

  public function dataTable(Request $request, $query = null)
  {
    try {
      $user = auth()->user();
      if ( $user->can("view user") ){
        $query = $this->service->getUserLists();
        return parent::dataTable($request, $query);
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function getUser($id)
  {
    $docu = $this->service->getById($id);
    return $this->response_json(UserResource::make($docu,""), __("messages.successfully_updated", ["attribute" => "user" ]));
  }

  public function update(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      $user = auth()->user();
      if ( $user->can("edit user") ){
        // User update
        $user = $this->service->getById($id);
        if ( !$user ) throw_exception(__("messages.not_found", ["attribute" => "user" ]), 403);

        $user_arr = [
          'name'     => $request->name,
          'email'    => $request->email,
          'password' => Hash::make($request->password),
          'role_id'  => $request->role ?? 3
        ];
        $user->fill($user_arr);
        $updated_user = $user->update();
        if ( !$updated_user ) throw_exception(__("messages.not_able", ["attribute" => "user" ]), 401);

        // Role update
        $this->userRoleService->model=new Role();
        $role_remove = $this->userRoleService->remove_role($user);
        $role_assign = $this->userRoleService->assign_role($user,$request->all()['role']);
        DB::commit();
        return $this->response_json($role_assign, __("messages.successfully_updated", ["attribute" => "user" ]));

      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function getOtp()
  {
    // https://medium.com/@maulanayusupp/how-to-create-two-factor-authentication-with-laravel-a44e58f69319
  }

  public function forgetPassword(Request $request)
  {
    try {

      $user = $this->service->getByEmail($request->input('email','')); //Get User FOR UPDATE
      
      if ( $user->email != $request->email ) throw_exception(__("messages.invalide", ["attribute" => "EMAIL" ]), 401);
      
      $this->otp->setUser($user);

      // For furthur use need to check or validate not able to request multiple which is cost pricing  
      // Cache::forget('OTP_for_' . $user->id); // Remove Cache OTP
      // Cache::put(['OTP_for_'.$user->id => $otp],now()->addMinutes(6));

      $otp = $this->otp->sendOtp();
      $enrollmentData = [
        "body" => "You received OTP for recovery",
        "enrollmentText" => $otp,
        "url" => url('/'),
        "thankyou" => "You have 5 minutes."
      ];
      log_debug("enrollmentData", $enrollmentData);
      $user->notify(new OtpNotify($enrollmentData));

      return $this->response_json($user, __("messages.successfully_otp"));

    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }

  }

  public function verifyOtp(Request $request, $uuid)
  {
    try {
      DB::beginTransaction();

      $user = $this->service->getByUuid($uuid); //Get User FOR UPDATE

      if ( !$user ) throw_exception(__('messages.not_found', ['attribute' => 'USER']), 404);

      $this->otp->setUser($user);
      if ( !$this->otp->getUserOtp() ) throw_exception(__("messages.expire_otp", ["attribute" => "OTP" ]), 401);

      if ( $this->otp->getUserOtp() != $request->otp ) throw_exception(__("messages.invalide", ["attribute" => "OTP" ]), 401);

      DB::commit();
      return $this->response_json([], __("messages.successfully_updated", ["attribute" => "user" ]));

    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function resetPassword(Request $request, $uuid)
  {
    try {
      DB::beginTransaction();

      $user = $this->service->getByUuid($uuid); //Get User FOR UPDATE

      if ( !$user ) throw_exception(__('messages.not_found', ['attribute' => 'USER']), 404);

      $this->otp->setUser($user);
      if ( !$this->otp->getUserOtp() ) throw_exception(__("messages.expire_otp", ["attribute" => "OTP" ]), 401);

      if ( $request->password != $request->confirm_password ) throw_exception(__("messages.invalide", ["attribute" => "CONFIRM PASSWORD" ]), 401);

      $user_arr = [
        'password' => Hash::make($request->password),
      ];
      $user->fill($user_arr);
      $updated_user = $user->update();
      if ( !$updated_user ) throw_exception(__("messages.not_able", ["attribute" => "user" ]), 401);
      
      Auth::logout();
      // Cache::forget('OTP_for_' . $user->id); // Remove Cache OTP
      $this->otp->removeOtp();
      DB::commit();
      return $this->response_json([], __("messages.successfully_updated", ["attribute" => "user" ]));

    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function delete($id)
  {
    try {
      $user = auth()->user();
      if ( $user->can("delete user") ){
        DB::beginTransaction();
        $role_service = new UserRoleService();
        $user         = $this->service->getById($id); // Get User
        $role_id      = $user->roles->pluck('pivot.role_id')[0]; // Get User Role ID
        $detach_role  = $role_service->detachRole($user, $role_id); // Detach Role From role_user table
        $user         = $this->service->delete($id); // Soft Delete user
        DB::commit();
        return $this->response_json($user, __("messages.successfully_move_to_trash", ["attribute" => "user" ]));
      }
    } catch (\Throwable $th) {
      // throw_exception("This user not able to performance action", 401);
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function restore(Request $request, $id)
  {
    try {
      $user = auth()->user();
      if ( $user->can("restore user") ){
        DB::beginTransaction();
        $user = $this->service->restore($id); // Restore
        
        $this->userRoleService->assign_role($user, 3);
        
        DB::commit();
        return $this->response_json($user, __("messages.successfully_restored", ["attribute" => "user" ]));
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }
}
