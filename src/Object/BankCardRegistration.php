<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Object;

/**
 * Online payment with a card on the S-Money hosted form.
 * This object can also describe a refund.
 */
class BankCardRegistration extends AbstractHydratable
{
    /**
     * Bank card information for the registration
     *
     * @var Card
     */
    public $card;

    /**
     * URL where the card holder will be redirected to once he submits the registration form.
     *
     * @var string
     */
    public $urlReturn;

    /**
     * URL where S-Money server sends a notification request to the third party server.
     *
     * @var string
     */
    public $urlCallback;

    /**
     * Error code for the registration
     *
     * @var string
     */
    public $errorCode;

    /**
     * Return codes detail's
     *
     * @var iterable
     */
    public $extraResults;

    /**
     * Pending registration
     */
    public const STATUS_PENDING = 0;

    /**
     * Success
     */
    public const STATUS_SUCCESS = 1;

    /**
     * Failed
     */
    public const STATUS_FAILED = 2;

    /**
     * Bank card registration's status
     *
     * @var int
     */
    public $status;

    public const CARDTYPE_CB = 'CB';
    public const CARDTYPE_MASTERCARD = 'MASTERCARD';
    public const CARDTYPE_MAESTRO = 'MAESTRO';
    public const CARDTYPE_VISA = 'VISA';
    public const CARDTYPE_ELECTRON = 'VISA_ELECTRON';

    /**
     * List of available cards
     *
     * @var string
     */
    public $availableCards;
}
