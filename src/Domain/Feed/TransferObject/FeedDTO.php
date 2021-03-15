<?php

namespace App\Domain\Feed\TransferObject;

use App\Domain\Feed\Entity\ValueObject\FeedType;

final class FeedDTO
{
    private ?string $url = null;

    private ?FeedType $type = null;

    private ?string $name = null;

    private ?string $removedDescription = null;

    private ?array $stopWords = null;

    private ?string $addedCity = null;

    private ?bool $removeLastImage = null;

    private ?string $textAfterDescription = null;

    private ?bool $excludeOutOfStockItems = null;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?FeedType
    {
        return $this->type;
    }

    public function setType(FeedType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRemovedDescription(): ?string
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
    public function getStopWords(): ?array
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

    public function getAddedCity(): ?string
    {
        return $this->addedCity;
    }

    public function setAddedCity(string $addedCity): self
    {
        $this->addedCity = $addedCity;

        return $this;
    }

    public function isRemoveLastImage(): ?bool
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

    public function getTextAfterDescription(): ?string
    {
        return $this->textAfterDescription;
    }

    public function setTextAfterDescription(string $textAfterDescription): self
    {
        $this->textAfterDescription = $textAfterDescription;

        return $this;
    }

    public function isExcludeOutOfStockItems(): ?bool
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
}
