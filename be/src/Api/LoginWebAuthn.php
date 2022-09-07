<?php

declare(strict_types=1);

namespace App\Api;

use App\Entities\Credential;
use App\Entities\Repository\CredentialRepository;
use App\Entities\Repository\UserRepository;
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
        private UserRepository $ur,
        private LoggerInterface $logger,
        private RelyingParty $rp,
        private ChallengeHandler $challengeHandler,
        private AccessTokenGenerator $tokenGenerator,
        private CredentialRepository $cr,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $user = $this->ur->getByName($data['username']);

        $parser = new ResponseParser();
        $assertion = $parser->parseGetResponse($data['assertion']);

        $challenge = $this->challengeHandler->getActiveChallenge();

        $cc = $this->cr->getCredentialContainerForUser($user);
        $foundCredential = $cc->findCredentialUsedByResponse($assertion);
        if ($foundCredential === null) {
            return $response->withStatus(400);
        }

        $updatedCredential = $assertion->verify($challenge, $this->rp, $foundCredential);

        // FIXME UPSTREAM: this has HORRIBLE ergonomics
        $uc = $this->em->find(Credential::class, $updatedCredential->getStorageId());
        if ($uc === null) {
            throw new \LogicException('somehow used a nonsaved credential');
        }
        if ($uc->userId !== $user->id) {
            throw new \LogicException('somehow user id mismatches');
        }

        $uc->updateCredential($updatedCredential);
        $this->em->flush();

        $token = $this->tokenGenerator->generateToken($user, AccessTokenGenerator::METHOD_WEBAUTHN);

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'access_token' => $token,
        ]));

        return $response;
    }
}
