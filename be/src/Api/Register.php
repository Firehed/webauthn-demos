<?php

declare(strict_types=1);

namespace App\Api;

use App\Services\AccessTokenGenerator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Register
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private AccessTokenGenerator $tokenGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $user = new \App\Entities\User($body['username']);
        if (array_key_exists('password', $body)) {
            $user->setPassword($body['password']);
        }
        $this->em->persist($user);
        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            $this->logger->notice('Tried to register duplicate user with name: {name}', ['name' => $body['username']]);
            return $response->withStatus(409);
        }

        $token = $this->tokenGenerator->generateToken($user, AccessTokenGenerator::METHOD_REGISTRATION);
        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'access_token' => $token,
        ]));
        return $response;
    }
}
