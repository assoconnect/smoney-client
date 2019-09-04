<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * A Company is a Corporation as an S-Money User
 */
class Company extends AbstractHydratable
{
    /**
     * Company's legal name
     *
     * @var string
     */
    public $name;

    /**
     * Siret or RNA in the French administrative system
     *
     * @var string
     */
    public $siret;

    /**
     * Company's NAF Code in the French administrative system
     *
     * @var string
     */
    public $nafCode;
}
