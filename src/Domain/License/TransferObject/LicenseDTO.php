<?php

namespace App\Domain\License\TransferObject;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use DateTimeInterface;

final class LicenseDTO
{
    private ?LicenseProduct $product = null;
    private ?LicenseType $type = null;
    private ?string $description = null;
    private ?array $options = null;
    private ?int $maximumNumberOfFeeds = null;
    private ?int $numberOfActivationsLeft = null;
    private ?DateTimeInterface $expiresAt = null;

    public function getProduct(): ?LicenseProduct
    {
        return $this->product;
    }

    public function setProduct(?LicenseProduct $product): LicenseDTO
    {
        $this->product = $product;

        return $this;
    }

    public function getType(): ?LicenseType
    {
        return $this->type;
    }

    public function setType(?LicenseType $type): LicenseDTO
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): LicenseDTO
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param mixed[]|null $options
     */
    public function setOptions(?array $options): LicenseDTO
    {
        $this->options = $options;

        return $this;
    }

    public function getMaximumNumberOfFeeds(): ?int
    {
        return $this->maximumNumberOfFeeds;
    }

    public function setMaximumNumberOfFeeds(?int $maximumNumberOfFeeds): LicenseDTO
    {
        $this->maximumNumberOfFeeds = $maximumNumberOfFeeds;

        return $this;
    }

    public function getNumberOfActivationsLeft(): ?int
    {
        return $this->numberOfActivationsLeft;
    }

    public function setNumberOfActivationsLeft(?int $numberOfActivationsLeft): LicenseDTO
    {
        $this->numberOfActivationsLeft = $numberOfActivationsLeft;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): LicenseDTO
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
