<?php

declare(strict_types=1);

namespace App\Api;

use App\Context;
use App\Entities\Credential;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Firehed\WebAuthn\{
    RelyingParty,
    ResponseParser,
};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class AddCredential
{
    public function __construct(
        private Context $context,
        private RelyingParty $rp,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        session_start();

        $user = $this->em->find(User::class, $this->context->authenticatedUserId);
        if (!$user) {
            throw new \Exception('not auth');
        }

        $challenge = $_SESSION['webauthn_challenge'] ?? null;
        if (!$challenge) {
            throw new \Exception('no challenge');
        } elseif (($_SESSION['webauthn_challenge_exp'] ?? 0) < time()) {
            throw new \Exception('challenge too old');
        }

        $params = $request->getParsedBody();
        $credentialData = $params['credential'];

        $parser = new ResponseParser();
        $createResponse = $parser->parseCreateResponse($credentialData);
        $credential = $createResponse->verify($challenge, $this->rp);

        $credentialEntity = new Credential($user, $credential, $params['nickname']);
        $this->em->persist($credentialEntity);
        $this->em->flush();

        return $response;
    }
}
