<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class CORSMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'OPTIONS') {
            return (new Response(204))
                ->withHeader('Access-control-allow-headers', 'Content-type')
                ->withHeader('Access-control-allow-origin', '*');
        }
        return $handler->handle($request)
            ->withHeader('Access-control-allow-origin', '*');
    }
}
