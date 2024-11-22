<?php

declare(strict_types = 1);

namespace App\Exception;

use InvalidArgumentException;

/**
 * The resource can not be found
 *
 * @package App\Exception
 */
class NotFoundException extends InvalidArgumentException
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