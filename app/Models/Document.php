<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
  ];
}
