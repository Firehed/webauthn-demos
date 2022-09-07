<?php

declare(strict_types=1);

namespace App\Entities\Repository;

use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use UnexpectedValueException;

class UserRepository
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function getByName(string $name): User
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['name' => $name]);
        if (!$user) {
            throw new UnexpectedValueException('User not found');
        }
        return $user;
    }
}
