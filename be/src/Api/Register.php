<?php

declare(strict_types=1);

namespace App\Api;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class Register
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();

        $user = new \App\Entities\User($body['username']);
        $user->setPassword($body['password']);
        $this->em->persist($user);
        $this->em->flush();

        $response->getBody()->write(__METHOD__);
        return $response;
    }
}
