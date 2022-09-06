<?php

declare(strict_types=1);

return [
    Psr\Log\LoggerInterface::class => fn () => new Firehed\SimpleLogger\Stdout(),
];
