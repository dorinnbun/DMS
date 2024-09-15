<?php

namespace App\Http\Resources\Api;

use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
  }
  /**
   * Transform the resource into an array.
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    $user_list = [
      'id'                          => $this->id,
      'book_id'                     => $this->book_id,
      'uuid'                        => $this->uuid,
      'madeAt'                      => $this->madeAt,
      'formula'                     => $this->formula,
      'last_name'                   => $this->last_name,
      'first_name'                  => $this->first_name,
      'nickname'                    => $this->nickname,
      'dob'                         => $this->dob,
      'pob_province'                => $this->pob_province,
      'pob_district'                => $this->pob_district,
      'pob_commune'                 => $this->pob_commune,
      'ethnicity'                   => $this->ethnicity,
      'nationality'                 => $this->nationality,
      'religion'                    => $this->religion,
      'previous_occupation'         => $this->previous_occupation,
      'occupation'                  => $this->occupation,
      'current_address'             => $this->current_address,
      'province'                    => $this->province,
      'district'                    => $this->district,
      'commune'                     => $this->commune,
      'identity'                    => $this->identity,
      'height'                      => $this->height,
      'spouse'                      => $this->spouse,
      'spouse_address'              => $this->spouse_address,
      'father_name'                 => $this->father_name,
      'father_address'              => $this->father_address,
      'mother_name'                 => $this->mother_name,
      'mother_address'              => $this->mother_address,
      'private_certificate_officer' => $this->private_certificate_officer,
      'supervision_officer'         => $this->supervision_officer,
      'scheduling_research_officer' => $this->scheduling_research_officer,
      "identity_photo"            => $this->identity_photo,
      "right_thumb_print"           => $this->right_thumb_print,
      "right_index_print"           => $this->right_index_print,
      "right_middle_print"          => $this->right_middle_print,
      "right_ring_print"            => $this->right_ring_print,
      "right_pinky_print"           => $this->right_pinky_print,
      "left_thumb_print"            => $this->left_thumb_print,
      "left_index_print"            => $this->left_index_print,
      "left_middle_print"           => $this->left_middle_print,
      "left_ring_print"             => $this->left_ring_print,
      "left_pinky_print"            => $this->left_pinky_print,
      "front_body_photo"            => $this->front_body_photo,
      "right_profile_photo"         => $this->right_profile_photo,
      "left_profile_photo"          => $this->left_profile_photo,
      "four_left_fingers_print"     => $this->four_left_fingers_print,
      "left_thumb_print01"          => $this->left_thumb_print01,
      "right_thumb_print01"         => $this->right_thumb_print01,
      "four_right_fingers_print"    => $this->four_right_fingers_print,
      "left_palm_print"             => $this->left_palm_print,
      "right_palm_print"            => $this->right_palm_print,
      "special_mark1"               => $this->special_mark1,
      "special_mark2"               => $this->special_mark2,
      "special_mark3"               => $this->special_mark3,
      "form_template"               => $this->form_template,
      "number"                      => $this->number,
      "upload_by"                   => $this->upload_by,

    ];

    // if ( isset($this->getMedias) ){
    //   $user_list['medias'] = $this->getMedias->map(function ($media) {
    //     return env("APP_URL")."/storage/".$media->model_id."/".$media->file_name;
    //   });
    // }
    return $user_list;
  }
}
