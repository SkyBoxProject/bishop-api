<?php

declare(strict_types=1);

namespace App\Domain\User\Command\Handler;

use App\Domain\EmailVerificationToken\Command\SendEmailConfirmationMessagesCommand;
use App\Domain\User\Command\ChangeEmailUserCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\EmailAlreadyExist;
use App\Domain\User\Exception\UserNotFound;
use App\Domain\User\Query\GetUserByEmailQuery;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final class ChangeEmailUserHandler implements MessageHandlerInterface
{
    private MessageBusInterface $messageBus;
    private UserRepository $userRepository;

    public function __construct(MessageBusInterface $messageBus, UserRepository $userRepository)
    {
        $this->messageBus = $messageBus;
        $this->userRepository = $userRepository;
    }

    public function __invoke(ChangeEmailUserCommand $command): User
    {
        $this->checkNotExistUserByEmail($command->getEmail());

        $user = $command->getUser();

        $user->setEmail($command->getEmail());
        $user->removeRole('ROLE_VERIFIED');

        $this->userRepository->save($user);

        $this->sendEmailConfirmationMessagesCommand($user);

        return $user;
    }

    private function checkNotExistUserByEmail(string $email): void
    {
        $user = null;

        try {
            $user = $this->messageBus->dispatch(new GetUserByEmailQuery($email));
        } catch (Throwable $exception) {
            if ($exception->getPrevious() instanceof UserNotFound) {
                //skip
            } else {
                throw $exception;
            }
        }

        if ($user !== null) {
            throw new EmailAlreadyExist();
        }
    }

    private function sendEmailConfirmationMessagesCommand(User $user): void
    {
        $this->messageBus->dispatch(new SendEmailConfirmationMessagesCommand($user));
    }
}
