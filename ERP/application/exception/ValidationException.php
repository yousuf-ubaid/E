<?php

declare(strict_types = 1);

namespace App\Exception;

use InvalidArgumentException;

/**
 * The data is invalid
 *
 * @package App\Exception
 */
class ValidationException extends InvalidArgumentException
{
    /**
     * Factory default resource not found message
     *
     * @return static
     */
    public static function create(): self
    {
        return new self('Resource not found');
    }

}