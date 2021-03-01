<?php

namespace App\Domain\Feed\Entity;

use App\Domain\Feed\Entity\ValueObject\FeedType;
use App\Domain\Feed\TransferObject\FeedDTO;
use App\Domain\User\Entity\User;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Table(name="feeds")
 * @ORM\Entity()
 *
 * @final
 */
class Feed implements JsonSerializable
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
     * @ORM\Column(name="type", type="feed_type")
     */
    private $type;

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
    private $removeLastImage = false;

    /**
     * @ORM\Column(name="text_after_description", type="string")
     */
    private $textAfterDescription = '';

    /**
     * @ORM\Column(name="is_exclude_out_of_stock_items", type="boolean", options={"default":false})
     */
    private $excludeOutOfStockItems = false;

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

    public function __construct(UuidV4 $uuidV4, User $user, string $url, FeedType $type)
    {
        $this->uuid = $uuidV4;
        $this->user = $user;
        $this->url = $url;
        $this->type = $type;

        $this->createdAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
        $this->updatedAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));

        $this->user->addFeed($this);
    }

    public function getUuid(): UuidV4
    {
        return $this->uuid;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): FeedType
    {
        return $this->type;
    }

    public function getRemovedDescription(): string
    {
        return $this->removedDescription;
    }

    public function setRemovedDescription(string $removedDescription): self
    {
        $this->removedDescription = $removedDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getStopWords(): array
    {
        return $this->stopWords;
    }

    /**
     * @param string[] $stopWords
     */
    public function setStopWords(array $stopWords): self
    {
        $this->stopWords = $stopWords;

        return $this;
    }

    public function getAddedCity(): string
    {
        return $this->addedCity;
    }

    public function setAddedCity(string $addedCity): self
    {
        $this->addedCity = $addedCity;

        return $this;
    }

    public function isRemoveLastImage(): bool
    {
        return $this->removeLastImage;
    }

    public function markAsRemoveLastImage(): self
    {
        $this->removeLastImage = true;

        return $this;
    }

    public function unmarkAsRemoveLastImage(): self
    {
        $this->removeLastImage = false;

        return $this;
    }

    public function getTextAfterDescription(): string
    {
        return $this->textAfterDescription;
    }

    public function setTextAfterDescription(string $textAfterDescription): self
    {
        $this->textAfterDescription = $textAfterDescription;

        return $this;
    }

    public function isExcludeOutOfStockItems(): bool
    {
        return $this->excludeOutOfStockItems;
    }

    public function markAsExcludeOutOfStockItems(): self
    {
        $this->excludeOutOfStockItems = true;

        return $this;
    }

    public function unmarkAsExcludeOutOfStockItems(): self
    {
        $this->excludeOutOfStockItems = false;

        return $this;
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

    public function updateFromDTO(FeedDTO $feedDTO): self
    {
        if ($feedDTO->getRemovedDescription() !== null) {
            $this->setRemovedDescription($feedDTO->getRemovedDescription());
        }

        if ($feedDTO->getStopWords() !== null) {
            $this->setStopWords($feedDTO->getStopWords());
        }

        if ($feedDTO->getAddedCity() !== null) {
            $this->setAddedCity($feedDTO->getAddedCity());
        }

        if ($feedDTO->getTextAfterDescription() !== null) {
            $this->setTextAfterDescription($feedDTO->getTextAfterDescription());
        }


        if ($feedDTO->isRemoveLastImage() !== null) {
            $feedDTO->isRemoveLastImage() ? $this->markAsRemoveLastImage() : $this->unmarkAsRemoveLastImage();
        }

        if ($feedDTO->isExcludeOutOfStockItems() !== null) {
            $feedDTO->isExcludeOutOfStockItems() ? $this->markAsExcludeOutOfStockItems() : $this->unmarkAsExcludeOutOfStockItems();
        }

        return $this;
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

    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->getUuid(),
            'url' => $this->url,
            'removedDescription' => $this->removedDescription,
            'stopWords' => $this->stopWords,
            'addedCity' => $this->addedCity,
            'removeLastImage' => $this->removeLastImage,
            'textAfterDescription' => $this->textAfterDescription,
            'excludeOutOfStockItems' => $this->excludeOutOfStockItems,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
