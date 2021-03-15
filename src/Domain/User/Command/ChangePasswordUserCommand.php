<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\User\Entity\User;

final class ChangePasswordUserCommand
{
    private User $user;
    private string $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
