<?php

declare(strict_types=1);

return [
    Psr\Log\LoggerInterface::class => fn () => new Firehed\SimpleLogger\Stdout(),

    App\Middlewares\AccessLogMiddleware::class,
    App\Middlewares\CORSMiddleware::class,
    App\Middlewares\JsonBodyParserMiddleware::class,
    App\Middlewares\ErrorHandlingMiddleware::class,

    App\Context::class,
];
