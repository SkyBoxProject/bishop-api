<?php

declare(strict_types=1);

namespace App\Domain\License\Command\Handler;

use App\Domain\License\Command\CreateLicenseCommand;
use App\Domain\License\Entity\License;
use App\Domain\License\Repository\LicenseRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\UuidV4;

final class CreateLicenseHandler implements MessageHandlerInterface
{
    private LicenseRepository $licenseRepository;

    public function __construct(LicenseRepository $licenseRepository)
    {
        $this->licenseRepository = $licenseRepository;
    }

    public function __invoke(CreateLicenseCommand $command): License
    {
        $licenseDTO = $command->getLicenseDTO();

        $license = new License(UuidV4::v4(), $licenseDTO->getProduct(), $licenseDTO->getType(), $command->getUser());

        $license->updateFromDTO($licenseDTO);

        $this->licenseRepository->save($license);

        return $license;
    }
}
