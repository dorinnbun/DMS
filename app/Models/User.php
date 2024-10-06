<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use App\Models\Role;
use App\Models\Permission;

use Ramsey\Uuid\Nonstandard\Uuid;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  use HasFactory, Notifiable, SoftDeletes, HasRoles;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'phone_number',
    'role_id',
    'google2fa_secret',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'google2fa_secret'
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (!$model->uuid) {
        $model->uuid = (string) Uuid::uuid4();
      }
    });
  }

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }

  public function getRole()
  {
    return $this->belongsTo(Role::class, 'role_id');
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class, "role_user")->withPivot('role_id');
  }

  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }

  // protected function google2faSecret(): Attribute
  // {
  //   return new Attribute(
  //     get: fn($value) => decrypt($value),
  //     set: fn($value) => encrypt($value)
  //   );
  // }

  public function setGoogle2faSecretAttribute($value)
  {
    $this->attributes['google2fa_secret'] = encrypt($value);
  }

  public function getGoogle2faSecretAttribute($value)
  {
    return decrypt($value);
  }
}
