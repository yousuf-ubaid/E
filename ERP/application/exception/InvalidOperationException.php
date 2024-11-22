<?php

declare(strict_types = 1);

namespace App\Exception;

use RuntimeException;

/**
 * InvalidOperationException
 *
 * @package App\Exception
 */
class InvalidOperationException extends RuntimeException
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