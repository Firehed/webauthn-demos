<?php

declare(strict_types=1);

namespace App\Api;

use App\Context;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Me
{
    public function __construct(
        private Context $context,
        private EntityManagerInterface $em,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $userId = $this->context->authenticatedUserId;
        if ($userId) {
            $user = $this->em->find(User::class, $userId);
            if (!$userId) {
            }

            $response = $response->withStatus(200)
                ->withHeader('Content-type', 'application/json');
            $response->getBody()->write(json_encode([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ]));
        } else {
            $response = $response->withStatus(403);
        }

        return $response;
    }
}
