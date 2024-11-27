<?php

namespace App\Http\Resources\Api;

use Exception;
use App\Models\User;
use App\Models\Communce;
use App\Models\District;
use App\Models\Province;
use PHPOpenSourceSaver\JWTAuth\Token;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{

  public function __construct($resource)
  {
    parent::__construct($resource);
  }

  protected function prefix_image($img)
  {
    if ( $img == null ) return asset('img/default.jpg');
    return env("APP_URL") . "/storage/" .$this->dir_name_updated. "/" .$img;
  }
  /**
   * Transform the resource into an array.
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    $doc_list = [
      'id'                          => $this->id,
      'book_id'                     => $this->book_id,
      'uuid'                        => $this->uuid,
      'madeAt'                      => $this->madeAt,
      'formula'                     => $this->formula,
      'last_name'                   => $this->last_name,
      'first_name'                  => $this->first_name,
      'nickname'                    => $this->nickname,
      'dob'                         => $this->dob,
      'pob_province'                => $this->pob_province ?? 0,
      'pob_district'                => $this->pob_district ?? 0,
      'pob_commune'                 => $this->pob_commune ?? 0,
      'ethnicity'                   => $this->ethnicity,
      'nationality'                 => $this->nationality,
      'religion'                    => $this->religion,
      'previous_occupation'         => $this->previous_occupation,
      'occupation'                  => $this->occupation,
      'current_address'             => $this->current_address,
      'province'                    => $this->province ?? 0,
      'district'                    => $this->district ?? 0,
      'commune'                     => $this->commune ?? 0,
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
      "identity_photo"              => $this->prefix_image($this->identity_photo),
      "right_thumb_print"           => $this->prefix_image($this->right_thumb_print),
      "right_index_print"           => $this->prefix_image($this->right_index_print),
      "right_middle_print"          => $this->prefix_image($this->right_middle_print),
      "right_ring_print"            => $this->prefix_image($this->right_ring_print),
      "right_pinky_print"           => $this->prefix_image($this->right_pinky_print),
      "left_thumb_print"            => $this->prefix_image($this->left_thumb_print),
      "left_index_print"            => $this->prefix_image($this->left_index_print),
      "left_middle_print"           => $this->prefix_image($this->left_middle_print),
      "left_ring_print"             => $this->prefix_image($this->left_ring_print),
      "left_pinky_print"            => $this->prefix_image($this->left_pinky_print),
      "front_body_photo"            => $this->prefix_image($this->front_body_photo),
      "right_profile_photo"         => $this->prefix_image($this->right_profile_photo),
      "left_profile_photo"          => $this->prefix_image($this->left_profile_photo),
      "four_left_fingers_print"     => $this->prefix_image($this->four_left_fingers_print),
      "left_thumb_print01"          => $this->prefix_image($this->left_thumb_print01),
      "right_thumb_print01"         => $this->prefix_image($this->right_thumb_print01),
      "four_right_fingers_print"    => $this->prefix_image($this->four_right_fingers_print),
      "left_palm_print"             => $this->prefix_image($this->left_palm_print),
      "right_palm_print"            => $this->prefix_image($this->right_palm_print),
      "special_mark1"               => $this->prefix_image($this->special_mark1),
      "special_mark2"               => $this->prefix_image($this->special_mark2),
      "special_mark3"               => $this->prefix_image($this->special_mark3),
      "form_template"               => $this->prefix_image($this->form_template),
      "form_template1"               => $this->prefix_image($this->form_template_1),
      "form_template2"               => $this->prefix_image($this->form_template_2),
      "number"                      => $this->number,
      "created_at"                  => $this->created_at,
      "updated_at"                  => $this->updated_at,
      "deleted_at"                  => $this->deleted_at,
      "updated_by"                  => $this->updated_by
    ];

    try {

      $doc_list['created_by'] = User::find($this->upload_by)?->only("id","name");
      $doc_list['created_by']['role'] = User::find($this->upload_by)->roles->first()->value('name');
      
      $doc_list['updated_by'] = User::find($this->updated_by)?->only("id","name") ?? ["id" => null, "name" => null];
      $doc_list['updated_by']['role'] = User::find($this->updated_by)?->roles->first()->value('name') ?? null;
  
      if ( isset($this->pob_province) || $this->pob_province != 0  ){
        $doc_list['pob_province'] = Province::find($this->pob_province)?->only('id', 'name');
      }
      if ( isset($this->pob_district) || $this->pob_district != 0  ){
        $doc_list['pob_district'] = District::find($this->pob_district)?->only("id","name");
      }
      if ( isset($this->pob_commune) || $this->pob_commune != 0  ){
        $doc_list['pob_commune'] = Communce::find($this->pob_commune)?->only("id","name") ?? "";
      }
      if ( isset($this->province) || $this->province != 0  ){
        $doc_list['province'] = Province::find($this->province)?->only('id', 'name');
      }
      if ( isset($this->district) || $this->district != 0  ){
        $doc_list['district'] = District::find($this->district)?->only("id","name");
      }
      if ( isset($this->commune) || $this->commune != 0  ){
        $doc_list['commune'] = Communce::find($this->commune)?->only("id","name");
      }
      if ( isset($this->deleted_by) ){
        $doc_list['deleted_by'] = User::find($this->deleted_by)?->only("id","name");
        $doc_list['deleted_by']['role'] = User::find($this->deleted_by)->roles->first()->value('name');
      }
      // if ( isset($this->getMedias) ){
      //   $doc_list['medias'] = $this->getMedias->map(function ($media) {
      //     return env("APP_URL")."/storage/".$media->model_id."/".$media->file_name;
      //   });
      // }
      return $doc_list;
      
    } catch (\Throwable $th) {
      log_error("DocumentResource --> ", ["message"=>$th->getMessage(),"line"=> $th->getLine(), "doc_id"=>$this->id]);
      throw new Exception($th->getMessage(), 403);
    }


  }
}
