<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

trait ApiResponse
{
  //Format return json
  private function json($code = null, $success = null, $message = null, $data = null): JsonResponse
  {
    return response()->json([
      'status'  => $code,
      'success' => $success,
      'message' => $message,
      'data'    => $data
      ]
    );
  }

  //Return Code 200 Success get data
  public function success($data, $message = 'OK'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_OK, true, $message, $data);
  }

  //Return Code 201 Success create
  public function created($data, $message = 'Created'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_CREATED, true, $message, $data);
  }

  //Return Code 202 Success update data
  public function updated($data, $message = 'Updated'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_ACCEPTED, true, $message, $data);
  }

  //Return Code 204 Success delete
  public function deleted($data = '', $message = 'Deleted'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_NO_CONTENT, true, $message, $data);
  }

  //Return Code 500 Serve error
  public function error($message = "Something went wrong!"): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_INTERNAL_SERVER_ERROR, false, $message);
  }

  //Return Code 400 Bad Request
  public function badRequest($message = 'Bad Request', $responseCode = null): JsonResponse
  {
    $responseCode = $responseCode ?? ResponseCode::HTTP_BAD_REQUEST;
    return $this->json($responseCode, false, $message);
  }

  //Return Code 422 unprocessable
  public function unprocessable($message = 'Unprocessable Entity Request'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_UNPROCESSABLE_ENTITY, false, $message);
  }

  //Return Code 404 data not found
  public function notFound($data = null, $message = 'not_found'): JsonResponse
  {
    return $this->json(ResponseCode::HTTP_NOT_FOUND, false, $message, $data);
  }

  public function unauthenticated($msg = 'Unauthorized')
  {
    return $this->json(ResponseCode::HTTP_UNAUTHORIZED, $msg);
  }

  public function response(bool $success, $code, $message, $data = null)
  {
    return [
      'success' => $success,
      'code'    => $code,
      'message' => $message,
      'data'    => $data,
    ];
  }
}
