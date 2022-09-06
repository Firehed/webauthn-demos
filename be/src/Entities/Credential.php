<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping;
use Firehed\WebAuthn\{
    Codecs,
    CredentialInterface,
};

#[Mapping\Entity]
#[Mapping\Table(name: 'credentials')]
class Credential
{
    #[Mapping\Column]
    #[Mapping\Id]
    public readonly string $id;

    #[Mapping\Column]
    public readonly string $nickname;

    #[Mapping\Column]
    public readonly string $userId;

    #[Mapping\Column]
    private string $credential;

    public function __construct(
        User $user,
        CredentialInterface $credential,
        string $nickname,
    ) {
        $this->id = $credential->getStorageId();
        $this->userId = $user->id;
        $this->nickname = $nickname;

        $this->updateCredential($credential);
    }

    public function updateCredential(CredentialInterface $credential): void
    {
        $codec = new Codecs\Credential();
        $this->credential = $codec->encode($credential);
    }

    public function getCredential(): CredentialInterface
    {
        $codec = new Codecs\Credential();
        return $codec->decode($this->credential);
    }
}
