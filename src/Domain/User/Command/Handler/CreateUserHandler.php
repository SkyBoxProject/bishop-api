<?php

declare(strict_types=1);

namespace App\Domain\User\Command\Handler;

use App\Domain\EmailVerificationToken\Command\SendEmailConfirmationMessagesCommand;
use App\Domain\User\Command\CreateUserCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Exception\EmailAlreadyExist;
use App\Domain\User\Exception\UserNotFound;
use App\Domain\User\Query\GetUserByEmailQuery;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;
use Throwable;

final class CreateUserHandler implements MessageHandlerInterface
{
    private MessageBusInterface $messageBus;
    private UserPasswordEncoderInterface $passwordEncoder;
    private UserRepository $userRepository;

    public function __construct(MessageBusInterface $messageBus, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->messageBus = $messageBus;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateUserCommand $command): User
    {
        $this->checkNotExistUserByEmail($command->getEmail());

        $user = new User(Uuid::v4(), $command->getEmail());

        $encodedPassword = $this->encodePasswordWithUser($user, $command->getPassword());

        $user->setPassword($encodedPassword);

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

    private function encodePasswordWithUser(User $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }

    private function sendEmailConfirmationMessagesCommand(User $user): void
    {
        $this->messageBus->dispatch(new SendEmailConfirmationMessagesCommand($user));
    }
}
