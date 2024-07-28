<?php

namespace App\Http\Requests;

use App\Traits\HasHttpResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;


class BaseRequest extends FormRequest
{
  use HasHttpResponse;

  /**
   * Handle a failed validation attempt.
   *
   * @param Validator $validator The validator instance
   * @throws HttpResponseException
   */
  protected function failedValidation(Validator $validator)
  {
    $error = $validator->errors()->first();

    $response = $this
      ->httpResponse()
      ->setError(true)
      ->setStatus(400)
      ->setMessage($error)
      ->toApiResponse();

    throw new HttpResponseException($response);
  }

}
