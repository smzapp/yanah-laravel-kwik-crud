<?php
namespace Yanah\LaravelKwik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Yanah\LaravelKwik\Traits\BaseModelUploadTrait;

class BaseModel extends Model {

    use BaseModelUploadTrait;

    // we'll add upload 
    // uuid

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (array_key_exists('uuid', $model->getAttributes()) && empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
            
            static::uploadFile($model); //upload first then set the url
        });

        static::updating(function ($model) {
            static::uploadFile($model); //upload first then set the url
        });
    }
}
