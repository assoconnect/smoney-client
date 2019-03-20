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

    const COUNTRIES = [
        'AT', 'AU', 'AX', 'BE', 'BG', 'BL', 'BR', 'CA', 'CH', 'CY', 'CZ',
        'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GF', 'GP', 'GR', 'HK',
        'HR', 'HU', 'IE', 'IN', 'IS', 'IT', 'JP', 'KR', 'LI', 'LT', 'LU',
        'LV', 'MF', 'MQ', 'MT', 'NC', 'NL', 'NO', 'PF', 'PL', 'PM', 'PT',
        'RE', 'RO', 'SE', 'SG', 'SI', 'SK', 'TF', 'US', 'WS', 'WF', 'YT', 'ZA'
    ];

    /**
     * Nationality (code ISO 3166-1)
     *
     * @var string
     */
    public $country;
}
