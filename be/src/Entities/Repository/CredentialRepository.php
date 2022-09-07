<?php

declare(strict_types=1);

namespace App\Entities\Repository;

use App\Entities\Credential;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Firehed\WebAuthn\CredentialContainer;

class CredentialRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function getCredentialContainerForUser(User $user): CredentialContainer
    {
        $creds = $this->em->getRepository(Credential::class)
            ->findBy(['userId' => $user->id]);

        return new CredentialContainer(array_map(fn ($c) => $c->getCredential(), $creds));
    }
}
