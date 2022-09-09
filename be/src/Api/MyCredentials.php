<?php

declare(strict_types=1);

namespace App\Api;

use App\Context;
use App\Entities\Credential;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MyCredentials
{
    public function __construct(
        private Context $context,
        private EntityManagerInterface $em,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = $this->context->authenticatedUserId;
        if (!$userId) {
            return $response->withStatus(403);
        }
        $user = $this->em->find(User::class, $userId);
        if (!$user) {
            // should be impossible
            return $response->withStatus(500);
        }

        $creds = $this->em->getRepository(Credential::class)
            ->findBy(['userId' => $user->id]);

        $data = [
            'credentials' => array_map(fn ($c) => [
                'id' => $c->id,
                'nickname' => $c->nickname,
            ], $creds),
        ];

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($data));
        return $response;
    }
}
