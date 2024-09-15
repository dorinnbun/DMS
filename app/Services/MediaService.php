<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaService
{
  public $disk="public";
  public function __construct() {}

  public function upload_to_do($do_file_dir, $file_content)
  {
    $do_path = Storage::disk($this->disk)->put($do_file_dir, $file_content);
    if (!$do_path) return false;
    return Storage::disk($this->disk)->url($do_file_dir) ?? false;
  }

  public function uploadImage($file, $id, $name)
  {
    $file_content      = file_get_contents($file);
    $original_filename = $file->getClientOriginalName();
    $extension         = $file->getClientOriginalExtension();
    $do_file_dir       = $id."_".$name."/".$original_filename;

    log_info("img directory ==>" . $do_file_dir);
    log_info("img extension ==>" . $extension);
    log_info("img original_filename ==>" . $original_filename);

    /* Mark: Check label's path exists or not */
    if ($do_file_dir) {

      $do_pic_url = $this->upload_to_do($do_file_dir, $file_content);
      if ($do_pic_url) return $this->file_response($do_file_dir, true);
      // if ($do_pic_url) return $this->file_response($do_pic_url, true);
    }

    return $this->file_response("", false);
  }

  public function deleteImage($file)
  {
    return Storage::disk($this->disk)->delete($file);
  }

  public function deleteDirectory($dir)
  {
    if ( File::exists($dir) ){
      return File::deleteDirectory($dir);
    }

    // return Storage::disk($this->disk)->deleteDirectory($dir);
  }

  public function renameDirecotry($dir)
  {

    $dir_string = explode("_", $dir); // Split string via underscore Ex: file_name_tmp.jpg
    $index      = count($dir_string)-1; // Get last element "tmp"
    unset($dir_string[$index]); // Unset tmp
    $newPath = file_dir(implode("_", $dir_string)); // Rebuild string Ex: file_name.jpg

    $old_dir = file_dir($dir);// Old directory
    if (File::exists($old_dir)) {
      return File::move($old_dir, $newPath);
    }
  }

  public function file_response($directory_file, $status)
  {
    return $directory_file;
    // return [
    //   "status"    => $status,
    //   "directory" => $directory_file
    // ];
  }
}
