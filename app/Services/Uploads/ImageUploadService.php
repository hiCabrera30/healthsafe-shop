<?php
namespace App\Services\Uploads;

use App\Models\Media\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Intervention\Image\ImageManagerStatic as Image;

class ImageUploadService {

    protected $image;

    /**
     * @author Ian C. Cabrera
     * @param string $image
     * @param string $disk
     */
    public function __construct(string $image) {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @author Ian C. Cabrera
     * @param  string $image
     * @return string
     */
    public static function upload(string $image, string $storage): string {
        $uploader = new ImageUploadService($image);
        
        return $uploader->uploadTheImage($storage);
    }

    /**
     * Resolve upload image when and if the image base64 is null
     *
     * @author Ian C. Cabrera
     * @param  string $image
     * @return string
     */
    public static function resolveImage(?string $image, string $placeholder, string $storage): string {
        if (!$image) {
            return $placeholder;
        }
        $uploader = new ImageUploadService($image);
        
        return $uploader->uploadTheImage($storage);
    }

    /**
     * Generate file name from base 64
     *
     * @author Ian C. Cabrera
     * @return string
     */
    public function generateFilename(): string {
        return sprintf(
            '%s_%s.%s',
            Carbon::now()->timestamp,
            uniqid(),
            $this->getExtension()
        );
    }

    /**
     * Returns the extension of the image uploaded
     *
     * @author Ian C. Cabrera
     * @return string
     */
    public function getExtension(): string {
        $image = substr($this->image, 0, strpos($this->image, ';'));
        $image = explode(':', $image);

        return count($image) > 1 ? explode('/', $image[1])[1] : "jpeg";
    }

    /**
     * Upload image process
     *
     * @author Ian C. Cabrera
     * @param  string $storage
     * @param  string $filename
     * @return string
     */
    public function uploadTheImage(string $storage, $filename = null): string {
        $filename = is_null($filename) ? $this->generateFilename() : $filename;

        return $this->store($storage, $filename);
    }

    /**
     * Moves image to the storage
     *
     * @author Ian C. Cabrera
     * @param  string $storage
     * @param  string $filename
     * @return string
     */
    public function store($storage, $filename): string {
        $this->moveToStorage($storage, $filename, $this->image);

        return sprintf("%s/storage/%s/%s", env('APP_URL'), $storage, $filename);
    }

    /**
     * Moves the file to server storage
     *
     * @author Ian C. Cabrera
     * @param  string $disk
     * @param  string $filename
     * @param  string $base64
     * @return void
     */
    public function moveToStorage(string $disk, string $filename, string $base64): void {
        $exploded = explode(',', $base64);
        \Storage::disk($disk)->put($filename, base64_decode($exploded[count($exploded) - 1]));
    }

}