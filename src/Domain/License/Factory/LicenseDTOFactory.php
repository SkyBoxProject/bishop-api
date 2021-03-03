<?php

namespace App\Domain\License\Factory;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\TransferObject\LicenseDTO;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

final class LicenseDTOFactory
{
    public function createFromRequest(Request $request): LicenseDTO
    {
        $dto = new LicenseDTO();

        try {
            if ($request->request->has('product')) {
                $dto->setProduct(new LicenseProduct($request->request->get('product', null)));
            }
        } catch (Throwable $exception) {
            //skip
        }

        try {
            if ($request->request->has('type')) {
                $dto->setType(new LicenseType($request->request->get('type', null)));
            }
        } catch (Throwable $exception) {
            //skip
        }

        if ($request->request->has('description')) {
            $dto->setDescription($request->request->get('description', null));
        }

        if ($request->request->has('options')) {
            $dto->setOptions((array) $request->request->get('options', null));
        }

        if ($request->request->has('maximumNumberOfFeeds') && null !== $request->request->get('maximumNumberOfFeeds', null)) {
            $dto->setMaximumNumberOfFeeds($request->request->getInt('maximumNumberOfFeeds', null));
        }

        if ($request->request->has('numberOfActivationsLeft') && null !== $request->request->get('numberOfActivationsLeft', null)) {
            $dto->setNumberOfActivationsLeft($request->request->getInt('numberOfActivationsLeft', null));
        }

        if ($request->request->has('expiresAtInTimestamp') && null !== $request->request->get('expiresAtInTimestamp', null)) {
            $dto->setExpiresAt((new DateTime())->setTimestamp($request->request->getInt('expiresAtInTimestamp', null)));
        }

        return $dto;
    }
}
