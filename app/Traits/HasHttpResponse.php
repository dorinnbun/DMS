<?php
namespace App\Traits;

use App\Http\Responses\ApiHttpResponse;


trait HasHttpResponse
{
  public function httpResponse(): ApiHttpResponse
  {
    return ApiHttpResponse::make();
  }
}
