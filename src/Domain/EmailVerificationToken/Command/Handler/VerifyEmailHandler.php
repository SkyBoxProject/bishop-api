<?php

declare(strict_types=1);

namespace App\Domain\EmailVerificationToken\Command\Handler;

use App\Domain\EmailVerificationToken\Command\VerifyEmailCommand;
use App\Domain\EmailVerificationToken\Exception\EmailVerificationTokenNotFound;
use App\Domain\EmailVerificationToken\Repository\EmailVerificationTokenRepository;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class VerifyEmailHandler implements MessageHandlerInterface
{
    private EmailVerificationTokenRepository $emailVerificationTokenRepository;
    private UserRepository $userRepository;

    public function __construct(EmailVerificationTokenRepository $emailVerificationTokenRepository, UserRepository $userRepository)
    {
        $this->emailVerificationTokenRepository = $emailVerificationTokenRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(VerifyEmailCommand $command): void
    {
        $emailVerificationToken = $this->emailVerificationTokenRepository->getByToken($command->getToken());

        if ($emailVerificationToken->isVerified()) {
            throw new EmailVerificationTokenNotFound($command->getToken());
        }

        $emailVerificationToken->markAsVerified();

        $this->emailVerificationTokenRepository->save($emailVerificationToken);
        $this->userRepository->save($emailVerificationToken->getUser());
    }
}
