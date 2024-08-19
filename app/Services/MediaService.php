<?php

namespace App\Services;

use App\Models\User;
use App\Models\Document;

class MediaService extends BaseService
{
  protected $model;
  protected $role;
  public $request;

  public function __construct(Document $document)
  {
    $this->model = $document;
  }

  public function addMedia($pathToFile)
  {
    return $this->model->addMedia($pathToFile)->toMediaCollection();
  }

  public function addCopyMedia($pathToFile)
  {
    // If you want to not move, but copy, the original file you can call preservingOriginal
    return $this->model->addMedia($pathToFile)->preservingOriginal()->toMediaCollection();
  }

}