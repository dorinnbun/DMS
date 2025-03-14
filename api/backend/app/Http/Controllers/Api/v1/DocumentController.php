<?php
namespace App\Http\Controllers\Api\v1;

use Exception;
use Google2FA ;
use App\Models\User;
use App\Models\Document;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Notifications\OtpNotify;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Api\DocumentResource;
use App\Http\Controllers\Api\v1\ParentApiController;
use Symfony\Component\HttpFoundation\Response;

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

  public function dataTable(Request $request, $query = null)
  {
    try {
      $user = auth()->user();
      if ( $user->can("view document") ){
        $query = $this->service->getDocumentLists();
        
        $orderBy = $request->input('orderBy');
        if (isset($orderBy['full_name'])) {
            $orderBy['last_name'] = $orderBy['full_name'];
            unset($orderBy['full_name']);
        }
        $request->merge(['orderBy' => $orderBy]);

        return parent::dataTable($request, $query);
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function getByUuid($uuid)
  {
    // $docu = $this->service->getByIdWithRelation($id, ['getMedias']);
    try {
      $user = auth()->user();
      if ( $user->can("view document") ){
        $docu = $this->service->getByUuid($uuid);
        return $this->response_json(DocumentResource::make($docu), __('messages.successfully_retrieved', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function test(Request $request)
  {
    // $user = User::first();

    // $enrollmentData = [
    //   "body" => "You received OTP for recovery",
    //   "enrollmentText" => "123456",
    //   "url" => url('/'),
    //   "thankyou" => "You have 5 minutes."
    // ];

    // $user->notify(new OtpNotify($enrollmentData));
    // return $user;
    return Google2FA::generateSecretKey();


  }

  public function update(Request $request, $id)
  {
    try {
      $user = auth()->user();
      if ( $user->can("edit document") ){
        $document = $this->service->updateDocMedia($id, $request->all(), $request);
        return $this->response_json($document, __('messages.successfully_updated', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function create(DocumentRequest $request)
  {
    try {
      $user = auth()->user();
      if ( $user->can("create document") ){

        $document = $this->service->createDocuMedia($request->all(), $request);
        return $this->response_json($document, __('messages.successfully_created', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function delete(Request $request, $id)
  {
    try {
      DB::beginTransaction();
      
      $user = auth()->user();
      if ( $user->can("delete document") ){

        $updated = $this->service->updateById($id, ['deleted_by' => $user->id]);
        $document = $this->service->delete($id);
        
        if ( !$updated ) throw new Exception(__('messages.not_found', ['attribute'=>'document']), Response::HTTP_BAD_REQUEST);
        
        DB::commit();
        return $this->response_json($document, __('messages.successfully_move_to_trash', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function hardDelete(Request $request, $id)
  {
    try {

      DB::beginTransaction();

      $user = auth()->user();
      if ( $user->can("delete document") ){
        
        $this->service->bulkImageDelete($id);
        $permanentDelete = $this->service->hardDelete($id);
        
        if ( $permanentDelete ) {
          DB::commit();
        }
        return $this->response_json($permanentDelete, __('messages.successfully_delete', ['attribute' => 'document']));
      }
      
    } catch (\Throwable $th) {
      DB::rollBack();
      return $this->errorResponse($th->getMessage(), $th->getCode());
      // return $this->errorResponse(__('messages.internal_server_error'), $th->getCode());
    }
  }

  public function restore(Request $request, $id)
  {
    try {
      $user = auth()->user();
      if ( $user->can("restore document") ){
        $document = $this->service->restore($id);
        return $this->response_json($document, __('messages.successfully_restored', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

  public function getTrashList(Request $request)
  {
    try {
      $user = auth()->user();
      if ( $user->can("delete document") ){
        $document = $this->service->getTrashData($request->all());
        return $this->response_json(DocumentResource::collection($document), __('messages.successfully_retrieved', ['attribute' => 'document']));
      }
    } catch (\Throwable $th) {
      return $this->errorResponse($th->getMessage(), $th->getCode());
    }
  }

}
