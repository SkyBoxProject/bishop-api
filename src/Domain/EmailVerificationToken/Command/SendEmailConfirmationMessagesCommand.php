<?php

declare(strict_types=1);

namespace App\Domain\EmailVerificationToken\Command;

use App\Domain\User\Entity\User;

final class SendEmailConfirmationMessagesCommand
{
    private string $email;
    private string $token;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->token = $user->getEmailVerificationToken()->getToken();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
