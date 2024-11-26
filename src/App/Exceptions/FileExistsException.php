<?php
namespace Yanah\LaravelKwik\App\Exceptions;

use Exception;

class FileExistsException extends Exception
{
    public function __construct($message = "The file already exists.", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
