<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Response;
use Slim\Exception\{
    HttpMethodNotAllowedException,
    HttpNotFoundException,
};
use Throwable;

class ErrorHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException $e) {
            return new Response(404);
        } catch (HttpMethodNotAllowedException $e) {
            return new Response(405);
        } catch (Throwable $e) {
            $this->logger->error((string)$e);
            return new Response(503);
        }
    }
}
