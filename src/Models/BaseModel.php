<?php
namespace Yanah\LaravelKwik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaseModel extends Model {

    // we'll add upload 
    // uuid

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (array_key_exists('uuid', $model->getAttributes()) && empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}
