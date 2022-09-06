<?php

declare(strict_types=1);

namespace App\Api;

use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Firehed\JWT\{Claim, JWT, KeyContainer};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class LoginPassword
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private KeyContainer $keyContainer,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        session_start();

        $body = $request->getParsedBody();

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['name' => $body['username']]);
        if (!$user) {
            return $response->withStatus(404);
        }

        if ($user->isPasswordCorrect($body['password'])) {
            $data = [
                Claim::ISSUED_AT => date('c'),
                Claim::SUBJECT => $user->id,
                Claim::JWT_ID => session_id(),
            ];
            $token = new JWT($data);
            $token->setKeys($this->keyContainer);

            $response = $response->withStatus(200)
                ->withHeader('Content-type', 'application/json');
            $response->getBody()->write(json_encode([
                    'access_token' => $token->getEncoded(),
                ]));
            return $response;
        } else {
            return $response->withStatus(403);
        }
    }
}
