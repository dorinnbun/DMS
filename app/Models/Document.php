<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Medias;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Nonstandard\Uuid;


class Document extends Model implements HasMedia
{
  use HasFactory, SoftDeletes, InteractsWithMedia;

  protected $fillable = [
    "bookID",
    "madeAt",
    "formula",
    "last_name",
    "first_name",
    "nickname",
    "dob",
    "pob_province",
    "pob_district",
    "pob_commune",
    "ethnicity",
    "nationality",
    "religion",
    "previous_occupation",
    "occupation",
    "current_address",
    "province",
    "district",
    "commune",
    "identity",
    "height",
    "spouse",
    "spouse_address",
    "father_name",
    "father_address",
    "mother_name",
    "mother_address",
    "private_certificate_officer",
    "supervision_officer",
    "scheduling_research_officer",
    "upload_by",
    "identity_photo",
    "right_thumb_print",
    "right_index_print",
    "right_middle_print",
    "right_ring_print",
    "right_pinky_print",
    "left_thumb_print",
    "left_index_print",
    "left_middle_print",
    "left_ring_print",
    "left_pinky_print",
    "front_body_photo",
    "right_profile_photo",
    "left_profile_photo",
    "four_left_fingers_print",
    "left_thumb_print01",
    "right_thumb_print01",
    "four_right_fingers_print",
    "left_palm_print",
    "right_palm_print",
    "special_mark1",
    "special_mark2",
    "special_mark3",
    "number"
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (!$model->uuid) {
        $model->uuid = (string) Uuid::uuid4();
      }
    });
  }

  public static function last()
  {
    return static::all()->last();
  }

  public function getMedias()
  {
    $medias = $this->morphMany(Medias::class, 'model');
    return $medias;
    // dd($medias->toSql(), $medias->getBindings());
  }
}
