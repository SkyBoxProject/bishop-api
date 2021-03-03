<?php

namespace App\Domain\License\Normalizer;

use App\Domain\License\Collection\LicenseCollection;
use App\Domain\License\Entity\License;
use DateTimeInterface;

final class LicenseNormalizer
{
    /**
     * @return mixed[]
     */
    public function normalize(License $license): array
    {
        return [
            'id' => (string) $license->getUuid(),
            'product' => $license->getProduct()->getValue(),
            'type' => $license->getType()->getValue(),
            'description' => $license->getDescription(),
            'maximumNumberOfFeeds' => (string) $license->getMaximumNumberOfFeeds(),
            'numberOfActivationsLeft' => (string) $license->getNumberOfActivationsLeft(),
            'expiresAt' => $license->getExpiresAt()->format(DateTimeInterface::ATOM),
            'createdAt' => (string) $license->getCreatedAt(),
            'updatedAt' => (string) $license->getUpdatedAt(),
        ];
    }

    /**
     * @return mixed[]
     */
    public function normalizeCollection(LicenseCollection $licenses): array
    {
        $normalizedLicenses = [];

        foreach ($licenses as $license) {
            $normalizedLicenses[] = $this->normalize($license);
        }

        return $normalizedLicenses;
    }

    /**
     * @return mixed[]
     */
    public function normalizeForUser(License $license): array
    {
        $normalizedLicense = $this->normalize($license);

        unset(
            $normalizedLicense['id'],
            $normalizedLicense['description'],
            $normalizedLicense['createdAt'],
            $normalizedLicense['updatedAt']
        );

        return $normalizedLicense;
    }

    /**
     * @return mixed[]
     */
    public function normalizeCollectionForUser(LicenseCollection $licenses): array
    {
        $normalizedLicenses = [];

        foreach ($licenses as $license) {
            $normalizedLicenses[] = $this->normalizeForUser($license);
        }

        return $normalizedLicenses;
    }
}
