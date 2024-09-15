<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AuthUpdateRequest extends BaseRequest// FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    $rules = [
      'email' => [
        'string',
        'email',
        'max:255'
      ],
      'password' => [
        'string',
        'min:6'
      ],
      'name' => [
        'string',
        'max:255'
      ]
    ];

    return $rules;
  }

  /**
   * Get custom error messages for validator errors.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'email.email'       => 'The email must be a valid email address.',
      'email.max'         => 'The email must not exceed :max characters.',
      'password.min'      => 'The password must be at least :min characters.',
      'name.max'          => 'The name must not exceed :max characters.',
    ];
  }
}
