<?php

namespace App\Module\Feed\Common\Product;

interface ProductInterface
{
    public function getCategory(): string;

    public function setCategory(string $category): self;

    public function getName(): string;

    public function setName(string $name): self;

    public function getPrice(): string;

    public function setPrice(string $price): self;

    public function getAddress(): string;

    public function setAddress(string $address): self;

    public function isSee(): bool;

    public function markAsSee(): self;

    public function unmarkAsSee(): self;

    public function isHit(): bool;

    public function markAsHit(): self;

    public function unmarkAsHit(): self;

    public function getBrand(): string;

    public function setBrand(string $brand): self;

    public function getVariant(): string;

    public function setVariant(string $variant): self;

    public function getOldPrice(): string;

    public function setOldPrice(string $oldPrice): self;

    public function getArticleNumber(): string;

    public function setArticleNumber(string $articleNumber): self;

    public function isStockAvailability(): bool;

    public function markAsStockAvailability(): self;

    public function unmarkAsStockAvailability(): self;

    public function getPageTitle(): string;

    public function setPageTitle(string $pageTitle): self;

    public function getKeywords(): string;

    public function setKeywords(string $keywords): self;

    public function getPageDescription(): string;

    public function setPageDescription(string $pageDescription): self;

    public function getAnnotation(): string;

    public function setAnnotation(string $annotation): self;

    public function getDescription(): string;

    public function setDescription(string $description): self;

    public function getImages(): string;

    public function setImages(string $images): self;
}
