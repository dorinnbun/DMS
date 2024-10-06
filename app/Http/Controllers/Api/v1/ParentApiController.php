<?php

namespace App\Http\Controllers\Api\v1;


use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Traits\HasHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    in="header",
 *    name="bearerAuth",
 *    type="http",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 * ),
 * @OA\Info(
 *    title= "MIB Application API Documents",
 *    version="1.0.0",
 * )
 */

class ParentApiController extends Controller
{
  use ApiResponse;
  use HasHttpResponse;

  protected $service;
  protected $model;

  //NOTED: Sample method to override in child class

  public function dataTable(Request $request, $query = null)
  {
    $attributes = $request->all();
    $response_data = $this->service->getData($attributes, $query)->toArray(request());
    return $this->response_json($response_data, __('messages.successfully_retrieved'));
    // return $this->success($this->service->getData($attributes, $query));
  }

  public function all(): JsonResponse
  {
    return $this->success($this->service->getList());
  }

  public function allRequest(Request $request, $query = null): JsonResponse
  {
    return $this->success($this->service->getList());
  }

  public function show($uuid)
  {
    return $this->service->getByUuid($uuid);
  }

  /**
   * Generates a JSON response with the given data, message, code, and description.
   *
   * @param mixed $data The data to be included in the response.
   * @param string $msg The message to be included in the response.
   * @param string $code The code to be included in the response. Default is "200".
   * @param string $description The description to be included in the response. Default is an empty string.
   * @return \Illuminate\Http\JsonResponse The generated JSON response.
   */
  public function response_json($data, $msg, $code = Response::HTTP_CREATED, $description = "")
  {
    $rsp = $this->httpResponse();

    if (isset($data['data'])) {
      $rsp->setItems($data['data']);
    }else{
      $rsp->setItem($data);
    }

    if (isset($data['meta'])) {
      $rsp->setMeta($data['meta']);
    }

    return $rsp
      ->setStatus($code)
      ->setCode($code)
      ->setMessage(isset($msg) ? $msg : __('messages.successfully'))
      ->setStatusDescription(isset($description) ? $description : __('messages.successfully'))
      ->toApiResponse();
  }

  public function successBulkResponse($rsp)
  {
    return $this->httpResponse()
      ->setStatus(Response::HTTP_CREATED)
      ->setItems($rsp['data'])
      ->setMeta($rsp['meta'])
      ->toApiResponse();
  }

  public function errorResponse($error, $code = Response::HTTP_BAD_REQUEST)
  {
    log_emergency($error);
    return $this
      ->httpResponse()
      ->setError(true)
      ->setCode($code)
      ->setStatus($code)
      ->setMessage($error);
  }
}
