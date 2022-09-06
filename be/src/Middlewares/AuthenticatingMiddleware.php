<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Context;
use Firehed\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class AuthenticatingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Context $context,
        private JWT\KeyContainer $keyContainer,
        private LoggerInterface $logger,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->authorize($request);

        return $handler->handle($request);
    }

    private function authorize(ServerRequestInterface $request): void
    {
        $header = $request->getHeaderLine('Authorization');
        if (!$header) {
            $this->logger->debug('No auth header');
            return;
        }
        [$bearer, $jwt] = explode(' ', $header, 2);
        if ($bearer !== 'Bearer') {
            $this->logger->debug('Not bearer token');
            return;
        }

        $token = JWT\JWT::fromEncoded($jwt, $this->keyContainer);
        $data = $token->getClaims();
        if (array_key_exists(JWT\Claim::SUBJECT, $data)) {
            $this->context->authenticatedUserId = $data[JWT\Claim::SUBJECT];
            session_id($data[JWT\Claim::JWT_ID]);
        } else {
            $this->logger->debug('Got a weird JWT');
        }
    }
}
