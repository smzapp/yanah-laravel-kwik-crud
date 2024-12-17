<?php
namespace Yanah\LaravelKwik\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * We handle Image or File upload by Samuel
 */
class UploadService
{
    const IMAGES_DIRECTORY  = 'images/';
    const UPLOADS_DIRECTORY = 'uploads/';
     
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
        $filePath = self::IMAGES_DIRECTORY . date('Y-m') . '/' . $fileName;

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
    public function uploadImageFile( $requestFile ): ?array
    {
        if(! $requestFile) {
            throw new Exception('Upload file is not recognized.');
        }

        $fileType    = $this->getFileType($requestFile);

        if ($fileType !== null) {

            $fileData    = explode(',', $requestFile);

            if (count($fileData) < 2) {
                throw new Exception('Invalid Base64 file format.');
            }
    
            $decodedFile = base64_decode($fileData[1]);
    
            if ($decodedFile === false) {
                throw new Exception('Failed to decode file.');
            }
    
            $extension = explode('/', $fileType)[1];
            $fileName  = uniqid('file_', true) . '.' . $extension;
            $filePath  = self::UPLOADS_DIRECTORY . date('Y-m') . '/' . $fileName;
            
            Storage::disk('public')->put($filePath, $decodedFile);
    
            $fileUrl = Storage::url($filePath);
    
            return [
                'full_url' => $fileUrl,
                'filename' => $fileName,
                'image_extension' => $extension,
            ];
        }

        return null;
    }

    /**
     * Example return: image/png
     */
    public function getFileType(string $base64File) : ?string
    {
        if(! $base64File ) throw new Exception('No file selected.');

        preg_match('/^data:(.+);base64,/', $base64File, $matches);

        if( count($matches) >= 2) {
            return $matches[1];
        }

        return null;
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