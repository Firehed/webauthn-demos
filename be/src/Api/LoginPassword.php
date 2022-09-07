<?php

declare(strict_types=1);

namespace App\Api;

use App\Entities\Repository\UserRepository;
use App\Services\AccessTokenGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class LoginPassword
{
    public function __construct(
        private UserRepository $ur,
        private LoggerInterface $logger,
        private AccessTokenGenerator $tokenGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $user = $this->ur->getByName($body['username']);

        if ($user->isPasswordCorrect($body['password'])) {
            $token = $this->tokenGenerator->generateToken($user, AccessTokenGenerator::METHOD_PASSWORD);

            $response = $response->withStatus(200)
                ->withHeader('Content-type', 'application/json');
            $response->getBody()->write(json_encode([
                    'access_token' => $token,
                ]));
            return $response;
        } else {
            return $response->withStatus(403);
        }
    }
}
