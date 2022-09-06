<?php

declare(strict_types=1);

namespace App\Api;

use Firehed\WebAuthn\Challenge;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetChallenge
{
    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        session_start();

        $challenge = Challenge::random();

        $_SESSION['webauthn_challenge'] = $challenge;
        $_SESSION['webauthn_challenge_exp'] = (time() + 120);

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'challengeB64' => $challenge->getBase64(),
        ]));
        return $response;
    }
}
