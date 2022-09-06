<?php

declare(strict_types=1);

use Firehed\JWT;
use Firehed\Security\Secret;

use function Firehed\Container\env;

return [
    'JWT_CURRENT_KEY' => env('JWT_CURRENT_KEY')->asInt(),

    'JWT_KEY_1' => env('JWT_KEY_1'),

    JWT\KeyContainer::class => function ($c): JWT\KeyContainer {
        $kc = new JWT\KeyContainer();
        $kc->addKey(1, JWT\Algorithm::HMAC_SHA_256, new Secret($c->get('JWT_KEY_1')));
        $kc->setDefaultKey($c->get('JWT_CURRENT_KEY'));
        return $kc;
    },
];
