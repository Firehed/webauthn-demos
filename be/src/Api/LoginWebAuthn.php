<?php

declare(strict_types=1);

namespace App\Api;

use App\Entities\Credential;
use App\Entities\User;
use App\Services\AccessTokenGenerator;
use App\Services\ChallengeHandler;
use Doctrine\ORM\EntityManagerInterface;
use Firehed\WebAuthn\{
    CredentialContainer,
    RelyingParty,
    ResponseParser,
};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class LoginWebAuthn
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private RelyingParty $rp,
        private ChallengeHandler $challengeHandler,
        private AccessTokenGenerator $tokenGenerator,
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

        $challenge = $this->challengeHandler->getActiveChallenge();

        $creds = $this->em->getRepository(Credential::class)
            ->findBy(['userId' => $user->id]);

        $cc = new CredentialContainer(array_map(fn ($c) => $c->getCredential(), $creds));

        $parser = new ResponseParser();
        $getResponse = $parser->parseGetResponse($data['assertion']);

        $foundCredential = $cc->findCredentialUsedByResponse($getResponse);
        if ($foundCredential === null) {
            return $response->withStatus(400);
        }

        $updatedCredential = $getResponse->verify($challenge, $this->rp, $foundCredential);

        // FIXME UPSTREAM: this has HORRIBLE ergonomics
        $uc = $this->em->find(Credential::class, $updatedCredential->getStorageId());
        if ($uc === null) {
            throw new \LogicException('somehow used a nonsaved credential');
        }
        if ($uc->userId !== $user->id) {
            throw new \LogicException('somehow user id mismatches');
        }

        $this->logger->debug(print_r($foundCredential, true));
        $this->logger->debug(print_r($updatedCredential, true));

        $uc->updateCredential($updatedCredential);
        $this->logger->debug('flushing');
        $this->em->flush();

        $this->logger->debug('build response');

        $token = $this->tokenGenerator->generateToken($user, AccessTokenGenerator::METHOD_WEBAUTHN);

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'access_token' => $token,
        ]));

        return $response;
    }
}
