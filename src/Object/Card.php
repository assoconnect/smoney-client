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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Application card id
     *
     * @var string
     */
    public $appCardId;

    /**
     * @return string
     */
    public function getAppCardId(): string
    {
        return $this->appCardId;
    }

    /**
     * Name of the card
     *
     * @var string
     */
    public $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * card's mask
     *
     * @var string
     */
    public $hint;

    /**
     * @return string
     */
    public function getHint(): string
    {
        return $this->hint;
    }

    /**
     * ISO code
     *
     * @var string
     */
    public $country;

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * Network
     *
     * @var int
     */
    public $network;

    /**
     * @return int
     */
    public function getNetwork(): int
    {
        return $this->network;
    }

    /**
     * Expiry date
     *
     * @var string
     */
    public $expiryDate;

    /**
     * @return string
     */
    public function getExpiryDate(): string
    {
        return $this->expiryDate;
    }
}
