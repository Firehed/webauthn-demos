<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class AccessLogMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $this->logger->info(
            '"{method} {url} HTTP/{version}" {statusCode} {responseSize}',
            [
                'method' => $request->getMethod(),
                'url' => $request->getUri(),
                'version' => $request->getProtocolVersion(),
                'statusCode' => $response->getStatusCode(),
                'responseSize' => 'tbd',
            ]
        );

        return $response;
    }
}
