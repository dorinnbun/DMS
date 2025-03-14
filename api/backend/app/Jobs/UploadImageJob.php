<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\BaseService;
use Illuminate\Bus\Queueable;
use App\Services\MediaService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $images;
    protected $id;
    protected $status;
    /**
     * Create a new job instance.
     */
    public function __construct($id, $images, $status="create")
    {
        $this->images = $images;
        $this->id = $id;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        try {

            log_debug("Start UploadImageJob", "queue_log");
            $media_service       = new MediaService();
            $media_service->disk = "space";
            // $media_service->disk = "public";
            $document_model      = Document::find($this->id);
    
            $url=[];
            foreach ($this->images as $key => $imgs) {
                log_debug("imgs -->", $imgs, "queue_log");
                if ( empty($imgs) ) {
                    $url[$key] = null;
                    continue;
                }
                $file_content     = Storage::disk('public')->get($imgs);
                $url[$key] = $media_service->upload_to_do($imgs, $file_content);
                if ($this->status=='update') {
                    // Remove updated image (Replace image)
                    $delete_local_img = Storage::disk($media_service->disk)->delete($document_model->{$key});
                    log_debug("delete_local_img -->", $delete_local_img, "queue_log");
                }
            }
            log_debug("url list -->", $url, "queue_log");
            if ( empty($url) ) throw new \Exception("Unable to upload image");
            $result = $document_model->fill($url);
            $result = $result->update();
            
        } catch (\Throwable $th) {
            log_error($th->getMessage(),[],"queue_log");
        }
        // 833471_linux.png
    }
}
// 527761_linux.png