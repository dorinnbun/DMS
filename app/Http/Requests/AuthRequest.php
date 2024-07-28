<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends BaseRequest// FormRequest
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
        'required',
        'string',
        'email',
        'max:255'
      ],
      'password' => [
        'required',
        'string',
        'min:6'
      ]
    ];

    if ($this->routeIs('auth.register')) {
      $rules['name'] = [
        'required',
        'string',
        'max:255'
      ];
      $rules['email'] = [
        'required',
        'string',
        'email',
        'max:255',
        'unique:users'
      ];
    }

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
      'email.required'    => 'The email field is required.',
      'email.email'       => 'The email must be a valid email address.',
      'email.max'         => 'The email must not exceed :max characters.',
      'email.unique'      => 'The email has already been taken.',
      'password.required' => 'The password field is required.',
      'password.min'      => 'The password must be at least :min characters.',
      'name.required'     => 'The name field is required.',
      'name.max'          => 'The name must not exceed :max characters.',
    ];
  }
}
