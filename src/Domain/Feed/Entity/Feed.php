<?php

namespace App\Domain\Feed\Entity;

use App\Domain\User\Entity\User;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Table(name="feeds")
 * @ORM\Entity()
 *
 * @final
 */
class Feed
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidV4 $uuid;

    /**
     * @ORM\Column(name="url", type="string")
     */
    private $url;

    /**
     * @ORM\Column(name="removed_description", type="string")
     */
    private $removedDescription = '';

    /**
     * @ORM\Column(name="stop_words", type="array")
     */
    private $stopWords = [];

    /**
     * @ORM\Column(name="added_city", type="string")
     */
    private $addedCity = '';

    /**
     * @ORM\Column(name="is_remove_last_image", type="boolean", options={"default":false})
     */
    private $isRemoveLastImage = false;

    /**
     * @ORM\Column(name="text_after_description", type="string")
     */
    private $textAfterDescription = '';

    /**
     * @ORM\Column(name="is_exclude_out_of_stock_items", type="boolean", options={"default":false})
     */
    private $isExcludeOutOfStockItems = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\Entity\User", inversedBy="feeds")
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

    public function __construct(UuidV4 $uuidV4, User $user, string $url)
    {
        $this->uuid = $uuidV4;
        $this->user = $user;
        $this->url = $url;

        $this->createdAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
        $this->updatedAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
    }

    public function getUuid(): UuidV4
    {
        return $this->uuid;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRemovedDescription(): string
    {
        return $this->removedDescription;
    }

    /**
     * @return string[]
     */
    public function getStopWords(): array
    {
        return $this->stopWords;
    }

    public function getAddedCity(): string
    {
        return $this->addedCity;
    }

    public function isRemoveLastImage(): bool
    {
        return $this->isRemoveLastImage;
    }

    public function getTextAfterDescription(): string
    {
        return $this->textAfterDescription;
    }

    public function isExcludeOutOfStockItems(): bool
    {
        return $this->isExcludeOutOfStockItems;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function __clone()
    {
        $this->user = clone $this->user;
    }

    public function equals(self $feed): bool
    {
        if (!$this->equalsUsers($feed) || !$this->equalsDates($feed)) {
            return false;
        }

        return $this->getUrl() === $feed->getUrl()
            && $this->getRemovedDescription() === $feed->getRemovedDescription()
            && $this->getStopWords() === $feed->getStopWords()
            && $this->getAddedCity() === $feed->getAddedCity()
            && $this->isRemoveLastImage() === $feed->isRemoveLastImage()
            && $this->getTextAfterDescription() === $feed->getTextAfterDescription()
            && $this->isExcludeOutOfStockItems() === $feed->isExcludeOutOfStockItems();
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
}
