<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class Company extends AbstractHydratable
{
    /**
     * Company or association name
     *
     * @var string
     */
    public $name;

    /**
     * Siret or RNA
     *
     * @var string
     */
    public $siret;

    /**
     * Company's NAF Code
     *
     * @var string
     */
    public $nafCode;
}
