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
      $name      = $created->id."_".$created->first_name . '_' . $created->last_name;
      
      // Create Media
      $created_media=[];
      $img_list = config("mediaKey");
      foreach ($img_list as $key => $value) {
        log_debug("media file",[$media->file($value)]);
        if ($media->file($value)) {
          log_info("key ==>" . $key);
          $created_media[$key] = $this->media_service->uploadImage($media->file($value), $created->id, $name);
          // $created_media[$value] = $this->media->addMediaReturnUrl($media->file($value), "1234");
        }
      }
      // Update column dir_name
      $update_dir_name = $this->updateById($created->id, ['dir_name' => $name, 'dir_name_updated' => $name]);

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

      $attribute = array_merge(['updated_by' => (int)auth()->user()->id], $attribute);
      $this->updateById($id, $attribute); // Find UserID & Update Attributes EXCEPT Image 
      
      $doc      = $this->getById($id);
      // generate updated_directory
      // $new_name = $this->media_service->getDirectory($doc->dir_name).'_'.$doc->updated_at->format("Y-m-d:His");
      $new_name = $doc->dir_name;
      // Rename current directory to updated_directory
      // $updated_directory = $this->media_service->renameFile($doc->dir_name_updated, $new_name);
      
      // Create Media
      $img_list = config("mediaKey");
      $created_media = [];
      $list_to_remove = [];
      
      foreach ($img_list as $key => $value) {
        if ( isset($attribute[$value]) ){
          if ( empty($attribute[$value]) || $attribute[$value] === "undefined"  ){
            log_info("media ==>", [$attribute[$value]]);
            log_info("Key removal without uploading ==>" . $key);
            $list_to_remove[] = $key;
            $created_media[$key] = null;
            continue;
          }
        }
        if ($media->file($value)) {
          log_info("key ==>" . $key);
          $created_media[$key] = $this->media_service->uploadImage($media->file($value), $doc->id, $new_name);
          $list_to_remove[] = $key;
        }
      }
      log_debug("List to remove --> ", $list_to_remove);

      $remove_names = $doc->only($list_to_remove);
      log_debug("list colum to remove -->", $remove_names);
      // Delete Old Document folder
      foreach ($remove_names as $remove_value) {
        $path = $new_name."/".$remove_value;
        log_debug("path --> ", $path);
        $is_delete = $this->media_service->deleteImage($path);// Delete file
        log_debug("is_delete --> ", $is_delete);
      }
      // $is_delete = $this->media_service->deleteDirectory(file_dir($delete_dir));// Delete DIRECTORY

      UploadImageJob::dispatch($doc->id, $created_media, "update");
      $this->updateById($id, ['dir_name_updated' => $new_name]); // update dir_name_update
      DB::commit();
      return $doc;

    } catch (\Throwable $th) {

      DB::rollBack();
      return $th->getMessage();
    }
  }

  public function bulkImageDelete($id)
  {
    $this->media_service->disk = "space";

    $will_delete_document = $this->getIncludeSoftDeleteById($id)->toArray();
    log_debug("Document will be delete -->", $will_delete_document);
    $img_list = config("mediaKey");
    
    $dir_name = $will_delete_document['dir_name'];
    // Delete Old Document folder
    foreach ($img_list as $key => $img) {
      if ( !$will_delete_document[$key] ){
        continue;
      }
      $path = "{$dir_name}/{$will_delete_document[$key]}";
      log_debug("path --> ", $path);
      
      $is_delete = $this->media_service->deleteImage($path);// Delete file
      log_debug("is_delete --> ", $is_delete);
    }
  }

  public function getByUuid($uuid)
  {
    $document = $this->model->where('uuid', $uuid)->first();
    if (!$document)
      throw_exception(__('messages.not_found', ['attribute' => 'document']), 401);

    return $document;
  }

  public function hardDelete($id)
  {
    $selected = config("mediaKey");
    try {

      DB::beginTransaction();

      $image_list = $this->getOnlySoftDeleteSelectFieldById((int)$id, array_keys($selected));

      if ( !$image_list ) throw_exception("Record not able to delete", 401);

      $permanentDelete = $this->permanentDelete($id);
      
      if ( $this->getOnlySoftDeleteById($id) ) throw_exception("Unable to Delete", 401);
      
      // foreach ($image_list->toArray() as $key => $value) {
      //   $this->media_service->disk = "space";
      //   $deleted[] = $this->media_service->deleteImage($value);
      // }

      // if ( count($deleted) > 0 ) DB::commit();
      DB::commit();
      
    } catch (\Throwable $th) {

      DB::rollBack();
      throw_exception($th->getMessage(), 401);

    }
  }
}
