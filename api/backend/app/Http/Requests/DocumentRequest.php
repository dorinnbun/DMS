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
      'book_id'                     => 'required|string',
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
      'current_address'             => 'nullable|string',
      'province'                    => 'required|string',
      'district'                    => 'required|string',
      'commune'                     => 'required|string',
      'identity'                    => 'required|string',
      'height'                      => 'required|string',
      'spouse'                      => 'nullable|string',
      'spouse_address'              => 'nullable|string',
      'father_name'                 => 'required|string',
      'father_address'              => 'required|string',
      'mother_name'                 => 'required|string',
      'mother_address'              => 'required|string',
      'private_certificate_officer' => 'required|string',
      'supervision_officer'         => 'required|string',
      'scheduling_research_officer' => 'required|string',
      "identityPhoto"               => "mimes:jpeg,png,jpg,pdf|max:2048",
      "rightThumbPrint"             => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightIndexPrint"             => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightMiddlePrint"            => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightRingPrint"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightPinkyPrint"             => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftThumbPrint"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftIndexPrint"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftMiddlePrint"             => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftRingPrint"               => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftPinkyPrint"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "frontBodyPhoto"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightProfilePhoto"           => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftProfilePhoto"            => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "fourLeftFingersPrint"        => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftThumbPrint01"            => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightThumbPrint01"           => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "fourRightFingersPrint"       => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "leftPalmPrint"               => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "rightPalmPrint"              => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "specialMark1"                => "required|mimes:jpeg,png,jpg,pdf|max:2048",
      "specialMark2"                => "nullable|mimes:jpeg,png,jpg,pdf|max:2048",
      "specialMark3"                => "nullable|mimes:jpeg,png,jpg,pdf|max:2048",
      "formTemplate"                => "nullable|mimes:jpeg,png,jpg,pdf|max:2048",
    ];

    return $rules;
  }

}
