<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Object containing the address details of a User
 */
class Address extends AbstractHydratable
{
    /**
     * Number and street
     *
     * @var string
     */
    public $street;

    /**
     * Zipcode
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
     * ISO 3166 code of countries from where S-Money accepts Users
     *
     * @var string[]
     */
    public const COUNTRIES = [
        'AT', 'AU', 'AX', 'BE', 'BG', 'BL', 'BR', 'CA', 'CH', 'CY', 'CZ',
        'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GF', 'GP', 'GR', 'HK',
        'HR', 'HU', 'IE', 'IN', 'IS', 'IT', 'JP', 'KR', 'LI', 'LT', 'LU',
        'LV', 'MF', 'MQ', 'MT', 'NC', 'NL', 'NO', 'PF', 'PL', 'PM', 'PT',
        'RE', 'RO', 'SE', 'SG', 'SI', 'SK', 'TF', 'US', 'WS', 'WF', 'YT',
        'ZA'
    ];

    /**
     * Country of the client (code ISO 3166-1)
     *
     * @var string
     */
    public $country;
}
