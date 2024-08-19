<?php

namespace App\Services;

use App\Models\Document;
use App\Http\Resources\Api\DocumentResource;

class DocumentService extends BaseService
{
  protected $model;
  protected $resourceClass = DocumentResource::class;

  public function __construct(Document $document)
  {
    $this->model = $document;
  }

  public function getDocumentLists()
  {
    return $this->queryBuilder();
  }

  public function update($id, $attribute)
  {
    return $this->updateById($id, $attribute);
  }

  public function getByUuid($uuid)
  {
    $document = $this->model->where('uuid', $uuid)->first();
    if (!$document)
      return $this->notFound();

    return new Document($document);
  }

}
