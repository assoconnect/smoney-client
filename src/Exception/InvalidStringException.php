<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Exception;

use Throwable;

class InvalidStringException extends \DomainException
{
    public const MESSAGE = '"%s" must be an alphanumerical string of no more than 35 chars.';

    public function __construct(string $source, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $source), $code, $previous);
    }
}
