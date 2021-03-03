<?php

namespace App\Domain\License\Entity;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Table(name="licenses")
 * @ORM\Entity()
 *
 * @final
 */
class License implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidV4 $uuid;

    /**
     * @ORM\Column(name="product", type="license_product")
     */
    private $product;

    /**
     * @ORM\Column(name="type", type="license_type")
     */
    private $type;

    /**
     * @ORM\Column(name="description", type="string")
     */
    private $description = '';

    /**
     * @ORM\Column(name="options", type="array")
     */
    private $options = [];

    /**
     * @ORM\Column(name="maximum_number_of_feeds", type="integer", options={"default":0})
     */
    private $maximumNumberOfFeeds = 0;

    /**
     * @ORM\Column(name="number_of_activations_left", type="integer", options={"default":0})
     */
    private $numberOfActivationsLeft = 0;

    /**
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private $expiresAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Entity\User", inversedBy="licenses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __construct(UuidV4 $uuidV4, LicenseProduct $product, LicenseType $type, User $user)
    {
        $this->uuid = $uuidV4;
        $this->product = $product;
        $this->type = $type;
        $this->user = $user;

        $this->expiresAt = DateTime::createFromFormat('Y-m-d h:i:s', '2021-01-01 00:00:00');
        $this->createdAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
        $this->updatedAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));

        $this->user->addLicense($this);
    }

    public function getUuid(): UuidV4
    {
        return $this->uuid;
    }

    public function getProduct(): LicenseProduct
    {
        return $this->product;
    }

    public function getType(): LicenseType
    {
        return $this->type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed[] $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getMaximumNumberOfFeeds(): int
    {
        return $this->maximumNumberOfFeeds;
    }

    public function setMaximumNumberOfFeeds(int $maximumNumberOfFeeds): self
    {
        $this->maximumNumberOfFeeds = $maximumNumberOfFeeds;

        return $this;
    }

    public function getNumberOfActivationsLeft(): int
    {
        return $this->numberOfActivationsLeft;
    }

    public function setNumberOfActivationsLeft(int $numberOfActivationsLeft): self
    {
        $this->numberOfActivationsLeft = $numberOfActivationsLeft;

        return $this;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function updateFromDTO(LicenseDTO $licenseDTO): self
    {
        if ($licenseDTO->getType() !== null) {
            $this->type = $licenseDTO->getType();
        }

        if ($licenseDTO->getDescription() !== null) {
            $this->setDescription($licenseDTO->getDescription());
        }

        if ($licenseDTO->getOptions() !== null) {
            $this->setOptions($licenseDTO->getOptions());
        }

        if ($licenseDTO->getMaximumNumberOfFeeds() !== null) {
            $this->setMaximumNumberOfFeeds($licenseDTO->getMaximumNumberOfFeeds());
        }

        if ($licenseDTO->getNumberOfActivationsLeft() !== null) {
            $this->setNumberOfActivationsLeft($licenseDTO->getNumberOfActivationsLeft());
        }

        if ($licenseDTO->getExpiresAt() !== null) {
            $this->setExpiresAt($licenseDTO->getExpiresAt());
        }

        return $this;
    }

    public function __clone()
    {
        $this->user = clone $this->user;
    }

    public function equals(self $license): bool
    {
        if (!$this->equalsUsers($license) || !$this->equalsDates($license)) {
            return false;
        }

        return $this->getType()->equals($license->getType())
            && $this->getDescription() === $license->getDescription()
            && $this->getOptions() === $license->getOptions()
            && $this->getMaximumNumberOfFeeds() === $license->getMaximumNumberOfFeeds()
            && $this->getNumberOfActivationsLeft() === $license->getNumberOfActivationsLeft()
            && $this->getExpiresAt()->getTimestamp() === $license->getExpiresAt()->getTimestamp();
    }

    public function equalsUsers(self $feed): bool
    {
        if (($this->getUser()->getId() === null && $feed->getUser()->getId() instanceof UuidV4)
            || ($feed->getUser()->getId() === null && $this->getUser()->getId() instanceof UuidV4)
        ) {
            return false;
        }

        return $this->getUser()->getId()->equals($feed->getUser()->getId());
    }

    public function equalsDates(self $feed): bool
    {
        $startTimeInTimestamp = $this->getCreatedAt() ? $this->getCreatedAt()->getTimestamp() : 0;
        $beforeUpdatedStartTimeInTimestamp = $feed->getCreatedAt() ? $feed->getCreatedAt()->getTimestamp() : 0;

        return $startTimeInTimestamp === $beforeUpdatedStartTimeInTimestamp;
    }

    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->getUuid(),
            'type' => $this->type->getValue(),
            'description' => $this->description,
            'options' => $this->options,
            'maximumNumberOfFeeds' => $this->maximumNumberOfFeeds,
            'numberOfActivationsLeft' => $this->numberOfActivationsLeft,
            'expiresAt' => $this->expiresAt->format(DateTime::ATOM),
            'createdAt' => $this->createdAt->format(DateTime::ATOM),
            'updatedAt' => $this->updatedAt->format(DateTime::ATOM),
        ];
    }
}
