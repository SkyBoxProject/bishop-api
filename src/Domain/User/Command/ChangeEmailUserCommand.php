<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\User\Entity\User;

final class ChangeEmailUserCommand
{
    private User $user;
    private string $email;

    public function __construct(User $user, string $email)
    {
        $this->user = $user;
        $this->email = $email;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
