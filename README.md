# smoney-client
PHP client for S-Money API

[![Build Status](https://travis-ci.org/assoconnect/smoney-client.svg?branch=master)](https://travis-ci.org/assoconnect/smoney-client)
[![Coverage Status](https://coveralls.io/repos/github/assoconnect/smoney-client/badge.svg?branch=master)](https://coveralls.io/github/assoconnect/smoney-client?branch=master)

The following features of the S-Money API are implemented:
- user creation and update
- subaccount creation and update

Feel free to submit a PR or contact us if you need a missing feature.

The package uses [Guzzle](https://github.com/guzzle/guzzle) as an HTTP client.

## Installation
This package can be installed with composer

`composer require assoconnect/smoney-client`

## Usage

````
<?php

$guzzle = GuzzleHttp\Client();
$client = new AssoConnect\SMoney\Client('YOUR S-MONEY ENDPOINT', 'YOUR S-MONEY TOKEN', $guzzle);

// Create a new User
$user = new AssoConnect\SMoney\Object\User([
    'appUserId' => 'appuserid-' . uniqid(),
    'type' => AssoConnect\SMoney\Object\User::TYPE_PROFESSIONAL_CLIENT,
    'profile' => new AssoConnect\SMoney\Object\UserProfile([
        'civility' => UserProfile::CIVILITY_MR,
        'firstname' => 'Test',
        'lastname' => 'McTestington',
        'birthdate' => new DateTime(),
        'address' => new AssoConnect\SMoney\Object\Address([
            'street' => 'rue du Test',
            'zipcode' => '75002',
            'city' => 'TestVille',
            'country' => 'FR',
        ]),
        'email' => 'test-' . uniqid() . '@test.com',
    ]),
    'company' => new AssoConnect\SMoney\Object\Company([
        'name' => 'CompanyName',
        'siret' => '123456789',
        'nafCode' => '4741Z',
    ])
]);

$client->createUser($userPro)->id; // S-Money's id of this newly created user
````