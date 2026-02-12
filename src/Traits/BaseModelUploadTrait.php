<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Facades\Storage;
use Yanah\LaravelKwik\Services\UploadService;

/**
 * Handle upload
 */
trait BaseModelUploadTrait 
{

    /**
     * Child Model will define $uploadFields to determine the fields 
     * to store the URL to be persisted.
     */
    public static function getUploadedFields()
    {
        if(isset(static::$uploadFields)) {
            return static::$uploadFields;
        }
        return [];
    }

    public static function uploadFile($model)
    {
        $fields = static::getUploadedFields();

        foreach ($fields as $field) {

            $payload = request($field);

            if (empty($payload)) {
                continue;
            }

            // Normalize into array
            $files = is_array($payload) ? $payload : [$payload];

            $uploadedUrls = [];

            foreach ($files as $file) {

                if (!$file) {
                    continue;
                }

                $response = app(UploadService::class)->uploadOnly($file);

                if (!empty($response['full_url'])) {
                    $uploadedUrls[] = $response['full_url'];
                }
            }

            $model->$field = json_encode($uploadedUrls);
        }
    }


    /**
     * Remove $payload fields which are included in $uploadedFields.
     * We don't want to persist base64 &blob.
     */
    public static function filterByUploadFields($payload)
    {
        $uploadFields = static::getUploadedFields();

        return array_diff_key($payload, array_flip($uploadFields));
    }
}