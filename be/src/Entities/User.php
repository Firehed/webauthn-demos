<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping;

use function password_hash;

use const PASSWORD_DEFAULT;

#[Mapping\Entity]
#[Mapping\Table(name: 'users')]
#[Mapping\UniqueConstraint(fields: ['name'])]
class User
{
    #[Mapping\Column]
    #[Mapping\Id]
    public readonly string $id;

    #[Mapping\Column(unique: true)]
    public readonly string $name;

    #[Mapping\Column]
    private string $passwordHash = '';

    public function __construct(string $name)
    {
        $this->id = \Ramsey\Uuid\v4();
        $this->name = $name;
    }

    public function setPassword(#[\SensitiveParameter] string $password): void
    {
        $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function isPasswordCorrect(#[\SensitiveParameter] string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
