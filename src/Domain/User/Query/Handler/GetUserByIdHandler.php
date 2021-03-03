<?php

namespace App\Domain\User\Query\Handler;

use App\Domain\User\Entity\User;
use App\Domain\User\Query\GetUserByIdQuery;
use App\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GetUserByIdHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(GetUserByIdQuery $query): User
    {
        return $this->userRepository->getById($query->getId());
    }
}
