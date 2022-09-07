<?php

declare(strict_types=1);

namespace App\Api;

use App\Services\ChallengeHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetChallenge
{
    public function __construct(
        private ChallengeHandler $challengeHandler,
    ) {
    }

    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $challenge = $this->challengeHandler->generateChallenge();

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode([
            'challengeB64' => $challenge->getBase64(),
        ]));
        return $response;
    }
}
