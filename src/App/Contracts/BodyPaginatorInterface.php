<?php
namespace Yanah\LaravelKwik\App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * We may apply pagination on the table list
 */
interface BodyPaginatorInterface
{
    public function responseBodyPaginator(Builder $query): LengthAwarePaginator;
}
