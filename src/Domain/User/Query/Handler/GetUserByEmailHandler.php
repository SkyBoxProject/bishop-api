<?php

namespace App\Domain\User\Query\Handler;

use App\Domain\User\Entity\User;
use App\Domain\User\Query\GetUserByEmailQuery;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GetUserByEmailHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(GetUserByEmailQuery $query): User
    {
        return $this->userRepository->getByEmail($query->getEmail());
    }
}
