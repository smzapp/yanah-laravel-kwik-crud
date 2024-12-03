<?php
namespace Yanah\LaravelKwik\App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;


/**
 * We apply collection
 */
interface BodyCollectionInterface
{
    public function responseBodyCollection(Builder $query): Collection;
}