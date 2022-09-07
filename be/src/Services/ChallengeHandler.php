<?php

declare(strict_types=1);

namespace App\Services;

use Firehed\WebAuthn\{
    Challenge,
};
use RuntimeException;

/**
 * FIXME: get out of the session stuff.
 * FIXME: move to ChallengeInterface one it's published
 */
class ChallengeHandler
{
    private const KEY_CHALLENGE = 'webauthn_challenge';
    private const KEY_TIMEOUT = 'webauthn_challenge_exp';

    private const TIMEOUT_SEC = 120;

    public function getActiveChallenge(): Challenge
    {
        session_start();

        $challenge = $_SESSION[self::KEY_CHALLENGE] ?? null;
        if (!$challenge) {
            throw new RuntimeException('No challenge is active');
        } elseif (($_SESSION[self::KEY_TIMEOUT] ?? 0) < time()) {
            throw new RuntimeException('Active challenge is too old');
        }
        return $challenge;
    }

    public function generateChallenge(): Challenge
    {
        session_start();

        $challenge = Challenge::random();

        $_SESSION[self::KEY_CHALLENGE] = $challenge;
        $_SESSION[self::KEY_TIMEOUT] = (time() + self::TIMEOUT_SEC);

        return $challenge;
    }
}
