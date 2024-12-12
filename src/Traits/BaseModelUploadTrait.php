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

        foreach($fields as $field)
        {
            $imagePayload = request($field);

            if($imagePayload != null) {

                $response = app(UploadService::class)->uploadOnly($imagePayload);

                $model->$field = $response['full_url'];

                // For updated(), We don't save again to avoid infinite updated() triggered.
                // $model->withoutEvents(function () use ($model, $field, $response) {
                //     $model->$field = $response['full_url'];
                //     $model->save();
                // });
            }
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