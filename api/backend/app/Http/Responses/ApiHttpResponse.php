<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class ApiHttpResponse extends Response implements Responsable
{
  protected bool $error = false;

  protected int $status = 200;

  protected ?string $status_description = '';

  protected ?string $code = '';

  protected ?string $message = '';

  protected mixed $item = null;

  protected mixed $items = null;

  protected mixed $meta = null;

  protected mixed $data = null || [];

  public static function make(): static
  {
    return app(static::class);
  }

  public function setError(bool $error = true): static
  {
    $this->error = $error;

    return $this;
  }

  public function setStatus(int $code): static
  {
    if ($code < 100 || $code >= 600) {
      return $this;
    }

    $this->status = $code;

    return $this;
  }

  public function setStatusDescription(string $description): static
  {
    $this->status_description = $description;

    return $this;
  }

  public function setCode(string $code): static
  {
    $this->code = $code;

    return $this;
  }

  public function setMessage(?string $message): static
  {
    $this->message = clean($message);

    return $this;
  }

  public function setItem(mixed $item): static
  {
    $this->item = $item;

    return $this;
  }

  public function setItems(mixed $items): static
  {
    $this->items = $items;

    return $this;
  }

  public function setMeta(mixed $meta): static
  {
    $this->meta = $meta;

    return $this;
  }

    /**
   * Generates an API response based on the data provided JsonResource.
   *
   * @return ApiHttpResponse|JsonResponse|JsonResource|RedirectResponse
   */
  public function toApiResponse(): ApiHttpResponse|JsonResponse|JsonResource|RedirectResponse
  {
    if ($this->data instanceof JsonResource) {
      log_info("Response Success => ", json_encode([
        'item'  => $this->item,
        'items' => $this->items,
        'meta'  => $this->meta,
      ]));

      return $this->data->additional(array_merge([
        'status'             => $this->status,
        'status_description' => $this->status_description,
        'success_code'       => $this->code,
        'success_message'    => $this->message,
        'data' => $this->error ? null : [
          'item'  => $this->item,
          'items' => $this->items,
          'meta'  => $this->meta,
        ]
      ]));
    }

    return $this->toResponse(request());
  }

  public function toResponse($request): JsonResponse|RedirectResponse
  {
    log_error("================================================");
    log_error(" toResponse Log => ", [
      'status'             => $this->status,
      'status_description' => $this->status_description,
      'code'               => $this->code,
      'message'            => $this->message,
    ]);

    $is_data = $this->error ? null : array_filter([
      'item'  => $this->item,
      'items' => $this->items,
      'meta'  => $this->meta,
    ]);

    $data = [
      'status'             => $this->status,
      'status_description' => $this->status_description,
      'code'               => $this->code,
      'message'            => $this->message,
      'data'               => $is_data
    ];

    return response()
      ->json($data, $this->status);
  }
}
