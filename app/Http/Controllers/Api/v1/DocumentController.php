<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Document;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\DocumentRequest;
use App\Http\Controllers\Api\v1\ParentApiController;

class DocumentController extends ParentApiController
{
  protected $service;
  protected $model;

  public function __construct(
    Document $document,
    DocumentService $documentService,
  )
  {
    $this->model = $document;
    $this->service = $documentService;
  }

  public function dataTable(Request $request, $query = null): JsonResponse
  {
    $query = $this->service->getDocumentLists();
    return parent::dataTable($request, $query);
  }

  public function update(DocumentRequest $request, $id)
  {
    try {
      $document = $this->service->update($id, $request->all());
      return $this->response_json($document, "Successfully updated");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function create(DocumentRequest $request)
  {
    try {
      $document = $this->service->create($request->all());
      return $this->response_json($document, "Successfully create document");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function delete(Request $request, $id)
  {
    try {
      $document = $this->service->delete($id);
      return $this->response_json($document, "Successfully delete document");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function restore(Request $request, $id)
  {
    try {
      $document = $this->service->restore($id);
      return $this->response_json($document, "Successfully restore document");
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

}
