<?php

use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

if (!function_exists('getFileNameFromUrl')) {
  function getFileNameFromUrl($url)
  {
    if ( !$url ) return throw_exception("url is empty", 400);
    // $url = "https://carrier-wrapper-label.sgp1.digitaloceanspaces.com/hoken_harley_1/imx_logo.png";
    $parsedUrl = parse_url($url, PHP_URL_PATH);
    $pathParts = explode('/', $parsedUrl);
    $index = $pathParts[count($pathParts) - 2];
    return $index;
  }
}
if (!function_exists('clean')) {
  function clean(array|string|null $dirty): array|string|null
  {

    if (!$dirty && $dirty !== null) return $dirty;

    if (!is_numeric($dirty)) $dirty = (string) $dirty;

    return $dirty;
  }
}
if (!function_exists('is_JWT_token')) {
  function is_JWT_token($token): bool
  {
    if (!empty($token) && $token !== '0') {
      try {
        // Create a Token instance from the string token
        $tokenInstance = new Token($token);
        // Decode the token
        JWTAuth::manager()->decode($tokenInstance);
        return true;
      } catch (\Throwable $th) {
        return false;
      }
    }
    return false;
  }
}
if (!function_exists('throw_exception')) {
  function throw_exception($msg, $code)
  {
    throw new \Exception($msg, $code);
  }
}

if (!function_exists('file_dir')) {
  function file_dir($dir)
  {
    return storage_path("app/public/".$dir); 
  }
}

/* Mark LOG SECTION */
if (!function_exists('log_warning')) {
  function log_warning($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->warning($msg, $context);
  }
}
if (!function_exists('log_debug')) {

  function log_debug($msg, $context = [], $channel = "api_log")
  {
    if (is_array($context)) {
      Log::channel($channel)->debug($msg, $context);
    } else {
      Log::channel($channel)->debug($msg . " " . $context);
    }
  }
}
if (!function_exists('log_emergency')) {

  function log_emergency($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->emergency($msg, $context);
  }
}
if (!function_exists('log_error')) {

  function log_error($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->error($msg, $context);
  }
}
if (!function_exists('log_info')) {

  function log_info($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->info("===== " . $msg . " =====", $context);
  }
}
if (!function_exists('log_infos')) {

  function log_infos($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->info($msg, $context);
  }
}
if (!function_exists('log_alert')) {

  function log_alert($msg, $context = [], $channel = "api_log")
  {
    Log::channel($channel)->alert($msg, $context);
  }
}
/* Mark LOG SECTION */