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
    /**
     * Create a new job instance.
     */
    public function __construct($id, $images)
    {
        $this->images = $images;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $media_service       = new MediaService();
        $media_service->disk = "space";
        // $media_service->disk = "public";
        $document_model      = Document::find($this->id);

        foreach ($this->images as $key => $imgs) {
            $file_content     = Storage::disk('public')->get($imgs);
            $url[$key]        = $media_service->upload_to_do($imgs, $file_content);
            // Remove tmp img store in local
            // $delete_local_img = Storage::disk('public')->delete($imgs);
        }
        $result = $document_model->fill($url);
        $result = $result->update();

    }
}
