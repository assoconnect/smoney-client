<?php

declare(strict_types=1);
namespace AssoConnect\SMoney\Object;

/**
 * Class AbstractHydratable
 *
 * Abstract base class for object to be hydrated
 * with an associative array passed to the constructor.
 */
abstract class AbstractHydratable
{
    public function __construct(iterable $params)
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }
}
