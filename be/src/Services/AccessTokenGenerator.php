<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\User;
use Firehed\JWT\{Claim, JWT, KeyContainer};

class AccessTokenGenerator
{
    public const METHOD_PASSWORD = 'password';
    public const METHOD_WEBAUTHN = 'webauthn';

    public function __construct(
        private KeyContainer $keyContainer,
    ) {
    }

    /**
     * @param self::METHOD_* $method
     */
    public function generateToken(User $user, string $method): string
    {
        $data = [
            Claim::ISSUED_AT => date('c'),
            Claim::SUBJECT => $user->id,
            // https://openid.net/specs/openid-connect-core-1_0.html
            // https://www.iana.org/assignments/jwt/jwt.xhtml#claims
            'amr' => [$method],
        ];
        $token = new JWT($data);
        $token->setKeys($this->keyContainer);

        return $token->getEncoded();
    }
}
