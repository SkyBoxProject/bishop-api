<?php

namespace App\Module\Feed;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Entity\ValueObject\FeedType;
use App\Module\Feed\Common\Exception\FeedManagerNotFoundException;
use App\Module\Feed\Common\Exception\MultipleSupportForTypeException;
use App\Module\Feed\Common\Exception\SkipRawProductException;
use App\Module\Feed\Common\FeedManagerInterface;
use App\Module\Feed\Common\Product\ProductInterface;

final class FeedManager
{
    private array $feedManagers;

    /**
     * @param FeedManagerInterface[] $feedManagers
     *
     * @throws MultipleSupportForTypeException
     */
    public function __construct(array $feedManagers)
    {
        $this->addFeedManagers($feedManagers);
    }

    /**
     * @return resource
     *
     * @throws FeedManagerNotFoundException
     */
    public function execute(Feed $feed)
    {
        $feedManager = $this->getSupportManager($feed->getType());

        $feedManager->build($feed);
        $container = $feedManager->getContainer();

        $stream = fopen('php://output', 'wb');

        $this->addHeaderColumnsToStream($stream);

        foreach ($container->getRawProducts() as $rawProduct) {
            $product = $feedManager->createEmptyProduct();

            try {
                foreach ($feedManager->getModules() as $module) {
                    $module->execute($container, $product, $rawProduct);
                }
            } catch (SkipRawProductException $skipRawProductException) {
                continue;
            }

            $this->addProductToStream($stream, $product);
        }

        return $stream;
    }

    /**
     * @param resource $stream
     */
    private function addHeaderColumnsToStream($stream): void
    {
        fputcsv(
            $stream,
            [
                $this->toWindows1251('Категория'),
                $this->toWindows1251('Товар'),
                $this->toWindows1251('Цена'),
                $this->toWindows1251('Адрес'),
                $this->toWindows1251('Видим'),
                $this->toWindows1251('Хит'),
                $this->toWindows1251('Бренд'),
                $this->toWindows1251('Вариант'),
                $this->toWindows1251('Старая цена'),
                $this->toWindows1251('Артикул'),
                $this->toWindows1251('Склад'),
                $this->toWindows1251('Заголовок страницы'),
                $this->toWindows1251('Ключевые слова'),
                $this->toWindows1251('Описание страницы'),
                $this->toWindows1251('Аннотация'),
                $this->toWindows1251('Описание'),
                $this->toWindows1251('Изображения'),
            ],
            ';',
            '"'
        );
    }

    /**
     * @param resource $stream
     */
    private function addProductToStream($stream, ProductInterface $product): void
    {
        fputcsv(
            $stream,
            [
                $this->toWindows1251($product->getCategory()),
                $this->toWindows1251($product->getName()),
                $this->toWindows1251($product->getPrice()),
                $this->toWindows1251($product->getAddress()),
                (int) $product->isSee(),
                (int) $product->isHit(),
                $this->toWindows1251($product->getBrand()),
                $this->toWindows1251($product->getVariant()),
                $this->toWindows1251($product->getOldPrice()),
                $this->toWindows1251($product->getArticleNumber()),
                (int) $product->isStockAvailability(),
                $this->toWindows1251($product->getPageTitle()),
                $this->toWindows1251($product->getKeywords()),
                $this->toWindows1251($product->getPageDescription()),
                $this->toWindows1251($product->getAnnotation()),
                $this->toWindows1251($product->getDescription()),
                $this->toWindows1251($product->getImages()),
            ],
            ';',
            '"'
        );
    }

    public function toWindows1251($str): string
    {
        return mb_convert_encoding($str, 'windows-1251', 'utf-8');
    }

    private function getSupportManager(FeedType $feedType): FeedManagerInterface
    {
        if (array_key_exists($feedType->getValue(), $this->feedManagers) === false) {
            throw new FeedManagerNotFoundException($feedType);
        }

        return $this->feedManagers[$feedType->getValue()];
    }

    /**
     * @param FeedManagerInterface[] $feedManagers
     *
     * @throws MultipleSupportForTypeException
     */
    private function addFeedManagers(array $feedManagers): void
    {
        $this->feedManagers = [];

        foreach ($feedManagers as $feedManager) {
            if (array_key_exists($feedManager::getSupportType()->getValue(), $this->feedManagers)) {
                throw new MultipleSupportForTypeException($feedManager::getSupportType());
            }

            $this->feedManagers[$feedManager::getSupportType()->getValue()] = $feedManager;
        }
    }
}
