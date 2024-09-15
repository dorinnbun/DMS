<?php

namespace App\Services;

use Opcodes\LogViewer\Facades\Cache;

class OtpService
{

  public $user;
  public function __construct()
  {
  }

  public function setUser($user)
  {
    $this->user = $user;
  }

  public function getOtp()
  {
    return rand(100000,999999);
  }


  protected function storeOtp($duration=6)
  {
    $otp = $this->getOtp();
    Cache::forget('OTP_for_' . $this->user->id);
    Cache::put(['OTP_for_'.$this->user->id => $otp],now()->addMinutes($duration));
    return $otp;
  }

  public function removeOtp()
  {
    return Cache::forget('OTP_for_' . $this->user->id);
  }

  public function getUserOtp()
  {
    return cache('OTP_for_' . $this->user->id);
  }

  public function sendOtp()
  {
    $this->getOtp();
    return $this->storeOtp();
  }
}