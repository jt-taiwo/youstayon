<?php

declare(strict_types=1);

namespace App\Core\Base\Exceptions;

use Exception;
use Throwable;

abstract class DomainException extends Exception
{
    public function __construct(
        string $message = 'A domain error occurred.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Default HTTP status code for domain exceptions.
     * Child exceptions may override this.
     */
    public function status(): int
    {
        return 400;
    }

    /**
     * Machine-readable error identifier.
     * Child exceptions may override this.
     */
    public function error(): string
    {
        return class_basename(static::class);
    }

    /**
     * Extra context for API responses.
     */
    public function context(): array
    {
        return [];
    }
}