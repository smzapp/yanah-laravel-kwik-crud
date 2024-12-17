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

            if (in_array('uuid', $model->getFillable())) {
                $model->uuid = Str::uuid()->toString();
            }
            
            static::uploadFile($model); //upload first then set the url
        });

        static::updating(function ($model) {
            static::uploadFile($model); //upload first then set the url
        });
    }
}
