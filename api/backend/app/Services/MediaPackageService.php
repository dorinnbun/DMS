<?php

namespace App\Services;

use App\Models\User;
use App\Models\Document;

class MediaPackageService extends BaseService
{
  public $model;
  protected $role;
  public $request;

  public function __construct($document)
  {
    $this->model = $document;
  }

  /**
   * Add a media file to a MediaLibrary collection.
   *
   * @param string $pathToFile
   * $doc = $this->getById($created->id);
   * $created_media = $doc->addMedia($media->file('allLeftFingers'))->toMediaCollection('allLeftFingers');
   * @return MediaCollections\Models\Media
   */
  public function addMedia($pathToFile, $collection_name="default")
  {
    return $this->model->addMediaWithCustomPath($pathToFile)->toMediaCollection($collection_name);
  }

  /**
   * Add a media file to a MediaLibrary collection and return the added media.
   *
   * @param string $pathToFile
   * Both addMedia and addMediaReturnUrl are the same but with a different return.
   * @return MediaCollections\Models\Media
   */
  public function addMediaReturnUrl($pathToFile, $collection_name="default")
  {
    // return $this->addMedia($pathToFile, $collection_name)->getUrl();
    return $this->addMedia($pathToFile, $collection_name)->getUrl();
  }

  public function addCopyMedia($pathToFile)
  {
    // If you want to not move, but copy, the original file you can call preservingOriginal
    return $this->model->addMedia($pathToFile)->preservingOriginal()->toMediaCollection();
  }

  public function addRemoteFile($url)
  {
    return $this->model->addMediaFromUrl($url)->toMediaCollection();
  }

  public function deleteAllFiles()
  {
    return $this->model->all()->each->delete();
  }

  public function deleteFile($model_id)
  {
    return $this->model->findOrFail($model_id)->each->delete();
  }

  public function addMediaOptional($pathToFile, $name, $file_name, $custom_properties=[], $collection="default")
  {
    return $this->model->addMedia($pathToFile)
            ->usingName($name)
            ->usingFileName($file_name)
            ->withCustomProperties($custom_properties)
            ->toMediaCollection($collection);
  }


  /**
   * Add a media file from a disk to the media library.
   *
   * @param string $pathToFile
   * @param string $disk .Ex: 's3'
   */
  public function addMediaFileToDisk($pathToFile, $disk)
  {
    // from Disk to media library
    return $this->model->addMediaFromDisk($pathToFile, $disk)->toMediaCollection();
  }

}