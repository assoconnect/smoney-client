<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

class Address extends AbstractHydratable
{
    /**
     * Number and street
     *
     * @var string
     */
    public $street;

    /**
     * Postcode
     *
     * @var string
     */
    public $zipcode;

    /**
     * City
     *
     * @var string
     */
    public $city;

    /**
     * Nationality (code ISO 3166-1)
     *
     * @var string
     */
    public $country;
}
