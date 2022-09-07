<?php

declare(strict_types=1);

namespace App\Api;

use App\Context;
use App\Entities\Repository\CredentialRepository;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetCredentials
{
    public function __construct(
        private Context $context,
        private EntityManagerInterface $em,
        private CredentialRepository $cr,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $username = $data['username'];

        $user = $this->em->getRepository(User::class)
            ->findOneBy(['name' => $username]);
        if (!$user) {
            return $response->withStatus(404);
        }

        $cc = $this->cr->getCredentialContainerForUser($user);

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'credentialIds' => $cc->getBase64Ids(),
        ]));

        return $response;
    }
}
