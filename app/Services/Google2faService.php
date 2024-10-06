<?php

namespace App\Services;


class Google2faService
{
  protected $role;

  public function generateSecretKey(){
    $google2fa = app('pragmarx.google2fa');

    return $google2fa->generateSecretKey();
  }
}