<?php

namespace App\Services;

use App\Models\Document;
use App\Jobs\UploadImageJob;
use App\Services\MediaService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\DocumentResource;

class DocumentService extends BaseService
{
  protected $model;
  protected $media_service;
  protected $resourceClass = DocumentResource::class;

  public function __construct(Document $document, MediaService $media_service)
  {
    $this->model = $document;
    $this->media_service = $media_service;
  }

  public function getDocumentLists()
  {
    return $this->queryBuilder();
  }

  public function createDocuMedia($attribute, $media)
  {
    DB::beginTransaction();
    try {
      $attribute = array_merge(['upload_by' => (int)auth()->user()->id], $attribute);
      $created   = $this->create($attribute);
      $name      = $created->first_name . '_' . $created->last_name;
      // Create Media
      $created_media=[];
      $img_list = config("mediaKey");
      foreach ($img_list as $key => $value) {
        log_debug("media file",[$media->file($value)]);
        if ($media->file($value)) {
          $created_media[$key] = $this->media_service->uploadImage($media->file($value), $created->id, $name);
          // $created_media[$value] = $this->media->addMediaReturnUrl($media->file($value), "1234");
        }
      }

      UploadImageJob::dispatch($created->id, $created_media);
      DB::commit();
      return $created;
    } catch (\Throwable $th) {
      DB::rollBack();
      return $th->getMessage();
    }
  }

  public function updateDocMedia($id, $attribute, $media)
  {
    DB::beginTransaction();
    try {
      $this->updateById($id, $attribute); // Find UserID & Update Attributes EXCEPT Image 
      $doc     = $this->getById($id);
      $name    = $id."_".$doc->first_name . '_' . $doc->last_name.'_'.$doc->updated_at->format("Y-m-d:H-i-s");
      $url_img = $doc->identity_photo; // Get any image url. Purpose to get DIRECTORY of folder
      // Create Media
      $img_list = config("mediaKey");
      foreach ($img_list as $key => $value) {
        if ($media->file($value)) {
          $created_media[$key] = $this->media_service->uploadImage($media->file($value), $doc->id, $name);
          // $created_media[$value] = $this->media->addMediaReturnUrl($media->file($value), "1234");
        }
      }
      // Delete Old Document folder
      $delete_dir = getFileNameFromUrl($url_img);// Get DIRECTORY from URL. EX: storage/app/public/first_name_id/
      $is_delete = $this->media_service->deleteDirectory(file_dir($delete_dir));// Delete DIRECTORY

      UploadImageJob::dispatch($doc->id, $created_media);
      DB::commit();
      return $doc;
    } catch (\Throwable $th) {
      DB::rollBack();

      return $th->getMessage();
    }
  }

  public function getByUuid($uuid)
  {
    $document = $this->model->where('uuid', $uuid)->first();
    if (!$document)
      return $this->notFound();

    return $document;
  }

  public function hardDelete($id)
  {
    $selected = [
      "all_left_fingers",
      "all_right_fingers",
      "right_thumb_print",
      // "right_index_print",
      // "right_middle_print",
      // "right_ring_print",
      // "right_pinky_print",
      // "left_thumb_print",
      // "left_index_print",
      // "left_middle_print",
      // "left_ring_print",
      // "left_pinky_print",
      // "front_body_photo",
      // "right_profile_photo",
      // "left_profile_photo",
      // "four_left_fingers_print",
      // "left_thumb_print01",
      // "right_thumb_print01",
      // "four_right_fingers_print",
      // "left_palm_print",
      // "right_palm_print",
      // "special_mark1",
      // "special_mark2",
      // "special_mark3"
    ];
    try {

      DB::beginTransaction();

      $image_list = $this->getOnlySoftDeleteSelectFieldById($id, $selected);

      if ( !$image_list ) throw_exception("Record not able to delete", 401);

      $permanentDelete = $this->permanentDelete($id);
      
      if ( $this->getOnlySoftDeleteById($id) ) throw_exception("Unable to Delete", 401);
      
      foreach ($image_list->toArray() as $key => $value) {
        $this->media_service->disk = "space";
        $deleted[] = $this->media_service->deleteImage($value);
      }

      if ( count($deleted) > 0 ) DB::commit();
      
    } catch (\Throwable $th) {

      DB::rollBack();
      throw_exception($th->getMessage(), $th->getCode());

    }
  }
}
