<?php
namespace Yanah\LaravelKwik\Services;

use Illuminate\Support\Facades\Storage;

/**
 * We handle Image or File upload by Samuel
 */
class UploadService
{
    private $imageDirectory = 'uploads/';
    
    /**
     * @return string
     * Example: uploads/2024-10/
     */
    public function getImageDirectory()
    {
        return $this->imageDirectory . date('Y-m');
    }

    /**
     * Upload only. Does not insert into Image model
     * @return ['full_url', 'filename', 'image_extension']
     */
    public function uploadOnly( $requestImage = null)
    {
        if( $requestImage == null )
        {
            throw new \Exception('Unable to retrieve a file to be uploaded.');
        }

        if ($this->isBase64Image($requestImage)) {
            return $this->uploadBase64Image($requestImage);
        } else {
            return $this->uploadImageFile($requestImage);
        }
    }

    /**
     * @return image_url
     */
    private function uploadBase64Image( $requestImage ) : array
    {
        $base64String = substr($requestImage, strpos($requestImage, ',') + 1);
        $image = base64_decode($base64String);
        $extension = '.png';
        $fileName = 'image_' . uniqid() . '_' . time() . $extension;
        $filePath = $this->getImageDirectory() . '/' . $fileName;

        // Make sure it is accessible via public
        Storage::disk('public')->put($filePath, $image);

        return  [
            'full_url' => Storage::url($filePath),
            'filename' => $fileName,
            'image_extension' => $extension
        ];
    }

    /**
     * Upload image file
     */
    public function uploadImageFile( $requestFile )
    {
        if(! $requestFile) {
            throw new \Exception('Upload file is not recognized.');
        }
        $filePath = $requestFile->store($this->getImageDirectory() , 'public');  // storage/app/public/uploads
        $imageUrl  = Storage::url($filePath); 
        $fileName = basename($filePath);
        $extension = $requestFile->getClientOriginalExtension(); 

        return [
            'full_url' => $imageUrl,
            'filename' => $fileName,
            'image_extension' => $extension,
        ];
    }

    /**
     * @return boolean
     */
    private function isBase64Image($base64String)
    {
        if (preg_match('/^data:image\/(jpeg|png|gif|bmp|webp);base64,/', $base64String)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $decodedData = base64_decode($base64String, true);

            if ($decodedData === false) {
                return false;
            }

            $imageInfo = getimagesizefromstring($decodedData);
            return $imageInfo !== false; 
        }

        return false;
    }
}