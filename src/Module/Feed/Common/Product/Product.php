<?php

declare(strict_types=1);

namespace App\Module\Feed\Common\Product;

final class Product implements ProductInterface
{
    private string $category = '';

    private string $name = '';

    private string $price = '';

    private string $address = '';

    private bool $see = false;

    private bool $hit = false;

    private string $brand = '';

    private string $variant = '';

    private string $oldPrice = '';

    private string $articleNumber = '';

    private bool $stockAvailability = false;

    private string $pageTitle = '';

    private string $keywords = '';

    private string $pageDescription = '';

    private string $annotation = '';

    private string $description = '';

    private string $images = '';

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function isSee(): bool
    {
        return $this->see;
    }

    public function markAsSee(): self
    {
        $this->see = true;

        return $this;
    }

    public function unmarkAsSee(): self
    {
        $this->see = false;

        return $this;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function markAsHit(): self
    {
        $this->hit = true;

        return $this;
    }

    public function unmarkAsHit(): self
    {
        $this->hit = false;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    public function getOldPrice(): string
    {
        return $this->oldPrice;
    }

    public function setOldPrice(string $oldPrice): self
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    public function setArticleNumber(string $articleNumber): self
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    public function isStockAvailability(): bool
    {
        return $this->stockAvailability;
    }

    public function markAsStockAvailability(): self
    {
        $this->stockAvailability = true;

        return $this;
    }

    public function unmarkAsStockAvailability(): self
    {
        $this->stockAvailability = false;

        return $this;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(string $pageTitle): self
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getPageDescription(): string
    {
        return $this->pageDescription;
    }

    public function setPageDescription(string $pageDescription): self
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    public function setAnnotation(string $annotation): self
    {
        $this->annotation = $annotation;

        return $this;
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

    public function getImages(): string
    {
        return $this->images;
    }

    public function setImages(string $images): self
    {
        $this->images = $images;

        return $this;
    }
}
