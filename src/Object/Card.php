<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Card for SMoney
 */
class Card extends AbstractHydratable
{
    /**
     * Id of the card
     *
     * @var int
     */
    public $id;

    /**
     * Application card id
     *
     * @var string
     */
    public $appCardId;

    /**
     * Name of the card
     *
     * @var string
     */
    public $name;

    /**
     * card's mask
     *
     * @var string
     */
    public $hint;

    /**
     * ISO code
     *
     * @var string
     */
    public $country;

    /**
     * Network
     *
     * @var int
     */
    public $network;

    /**
     * Expiry date
     *
     * @var string
     */
    public $expiryDate;
}
