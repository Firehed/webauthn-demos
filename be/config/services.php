<?php

declare(strict_types=1);

return [
    Psr\Log\LoggerInterface::class => fn () => new Firehed\SimpleLogger\Stdout(),

    Firehed\WebAuthn\RelyingParty::class => fn ($c) => new Firehed\WebAuthn\RelyingParty($c->get('hostname')),

    App\Middlewares\AccessLogMiddleware::class,
    App\Middlewares\AuthenticatingMiddleware::class,
    App\Middlewares\CORSMiddleware::class,
    App\Middlewares\ErrorHandlingMiddleware::class,
    App\Middlewares\JsonBodyParserMiddleware::class,

    App\Context::class,

    App\Entities\Repository\CredentialRepository::class,

    App\Services\AccessTokenGenerator::class,
    App\Services\ChallengeHandler::class,
];
