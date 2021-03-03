<?php

declare(strict_types=1);

namespace App\Domain\License\Command\Handler;

use App\Domain\License\Command\UpdateLicenseCommand;
use App\Domain\License\Entity\License;
use App\Domain\License\Repository\LicenseRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateLicenseHandler implements MessageHandlerInterface
{
    private LicenseRepository $licenseRepository;

    public function __construct(LicenseRepository $licenseRepository)
    {
        $this->licenseRepository = $licenseRepository;
    }

    public function __invoke(UpdateLicenseCommand $command): License
    {
        $license = $command->getLicense();

        $license->updateFromDTO($command->getLicenseDTO());

        $this->licenseRepository->save($license);

        return $license;
    }
}
