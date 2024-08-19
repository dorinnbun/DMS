<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends BaseRequest // FormRequest
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
      'bookID'                      => 'required|string',
      'madeAt'                      => 'required|string',
      'formula'                     => 'required|string',
      'last_name'                   => 'required|string',
      'first_name'                  => 'required|string',
      'nickname'                    => 'nullable|string',
      'dob'                         => 'required|string',
      'pob_province'                => 'required|string',
      'pob_district'                => 'required|string',
      'pob_commune'                 => 'required|string',
      'ethnicity'                   => 'required|string',
      'nationality'                 => 'required|string',
      'religion'                    => 'required|string',
      'previous_occupation'         => 'nullable|string',
      'occupation'                  => 'required|string',
      'current_address'             => 'required|string',
      'province'                    => 'required|string',
      'district'                    => 'required|string',
      'commune'                     => 'required|string',
      'identity'                    => 'required|string',
      'height'                      => 'nullable|string',
      'spouse'                      => 'nullable|string',
      'spouse_address'              => 'nullable|string',
      'father_name'                 => 'nullable|string',
      'father_address'              => 'nullable|string',
      'mother_name'                 => 'nullable|string',
      'mother_address'              => 'nullable|string',
      'private_certificate_officer' => 'nullable|string',
      'supervision_officer'         => 'nullable|string',
      'scheduling_research_officer' => 'nullable|string',
    ];

    return $rules;
  }

}
