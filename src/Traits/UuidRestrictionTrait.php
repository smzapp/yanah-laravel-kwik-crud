<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;
use Exception;

trait UuidRestrictionTrait
{

    public function restrictAccessByUuid($model, string $id)
    {
        $fillables = $model->getFillable();

        if(!in_array('uuid', $fillables)) {
            throw new Exception('Uuid is not added as fillable.');
        }

        $uuid = request('uuid');

        if(!$uuid) {
            abort(403, 'Unable to locate uuid');
        }

        $response = $model->where('uuid', $uuid)
                  ->where('id', $id);

        if(!$response->exists()) {
            abort(403, 'Invalid page request');
        }
    }

}