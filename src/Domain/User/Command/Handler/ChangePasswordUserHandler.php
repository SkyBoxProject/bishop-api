<?php

declare(strict_types=1);

namespace App\Domain\User\Command\Handler;

use App\Domain\User\Command\ChangePasswordUserCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class ChangePasswordUserHandler implements MessageHandlerInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private UserRepository $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function __invoke(ChangePasswordUserCommand $command): User
    {
        $user = $command->getUser();

        $encodedPassword = $this->encodePasswordWithUser($user, $command->getPassword());

        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);

        return $user;
    }

    private function encodePasswordWithUser(User $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
