<?php
declare(strict_types=1);

namespace App\Feeds;

use SimpleXMLElement;
use Throwable;

class FullFeed extends Feed
{
    public const SOURCE_PRODUCTS   = 'products';
    public const SOURCE_PRODUCT    = 'product';
    public const SOURCE_CATEGORIES = 'categories';
    public const SOURCE_CATEGORY   = 'category';

    public const ATTRIBUTES  = '@attributes';
    public const ID          = 'id';
    public const AVAILABLE   = 'available';
    public const TITLE       = 'model';
    public const DESCRIPTION = 'description';
    public const CATEGORY    = 'category_id';
    public const PRICE       = 'price';

    public const CATEGORY_PARENT = 'parent';
    public const CATEGORY_NAME   = 'name';

    /**
     * @var array
     */
    private $data;

    /**
     * @var array
     */
    private $categories;

    public function run()
    {
        $this->file = fopen('php://output', 'wb');
        fputcsv($this->file, $this->getHeaderColumns(), ';', '"');
        $this->eachData();
        fclose($this->file);
    }

    private function eachData(): void
    {
        foreach ($this->getData() as $ad) {
            try {
                $this->processAd($ad);
            } catch (Throwable $exception) {
                $this->logger->exceptionWithAd($exception, $ad);
            }
        }
    }

    private function processAd(SimpleXMLElement $ad): void
    {
        if ($this->isStopWords() && $this->checkStopWords((string) $ad->{self::TITLE})) {
            return;
        }

        try {
            $desc = $this->toWindows1251($this->prepareDesc((string) $ad->{self::DESCRIPTION}));
        } catch (Throwable $exception) {
            $desc = $this->toWindows1251($this->prepareDesc(''));
        }

        $available = $ad->attributes()->{self::AVAILABLE}->__toString() === 'true' ? 1 : 0;

        if($this->isSkipNotAvailable() && !$available) {
            return;
        }

        fputcsv(
            $this->file,
            [
                $this->toWindows1251($this->prepareCategory((int) $ad->{self::CATEGORY})), //Категория
                $this->toWindows1251((string) $ad->{self::TITLE}), //Товар
                $this->toWindows1251($ad->{self::PRICE}), //Цена
                '', //Адрес
                $available, //Видим
                0, //Хит
                '', //Бренд
                '', //Вариант
                '', //Старая цена
                $this->toWindows1251($ad->attributes()->{self::ID}->__toString()), //Артикул
                '', //Склад
                $this->toWindows1251(mb_strtolower((string) $ad->{self::TITLE})), //Заголовок страницы
                $this->toWindows1251($this->prepareSeoKeyword((string) $ad->{self::TITLE})), //Ключевые слова
                $desc, //Описание страницы
                $desc, //Аннотация
                $desc, //Описание
                $this->prepareImage($ad), //Изображения
            ]
            ,
            ';',
            '"'
        );
    }

    /**
     * @return array[]|\SimpleXMLElement[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function prepareDesc(string $desc): string
    {
        $desc = str_replace('<br />', "\n", $desc);

        $desc = strip_tags($desc);
        $desc = preg_replace('!\n+!', "\n", $desc);
        $desc = str_replace(" ", '', $desc);
        $desc = preg_replace("/ {2,}/", ' ', $desc);
        if ($this->isCutText()) {
            $replaceStr = preg_replace('!\n+!', "\n", $this->getCutText());
            $replaceStr = str_replace(" ", '', $replaceStr);
            $replaceStr = preg_replace("/ {2,}/", ' ', $replaceStr);
            $desc       = (string) str_replace($replaceStr, '', $desc);
        }

        if ($this->isAppendEndDescriptionText()) {
            $desc .= $this->getAppendEndDescriptionText();
        }

        return trim($desc);
    }

    public function prepareCategory(int $id): string
    {
        return str_replace(',', '', $this->findCategory($id));
    }

    private function findCategory(int $id): string
    {
        $category = $this->getCategories()[$id];

        $result = [$category[self::CATEGORY_NAME]];
        if ($category[self::CATEGORY_PARENT] !== null) {
            $result[] = $this->findCategory($category[self::CATEGORY_PARENT]);
        }

        $result = array_reverse($result);

        return implode('/', $result);
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function prepareSeoKeyword(string $keyword): string
    {
        if ($this->isAddCity()) {
            $keyword .= ' купить в ';
            $keyword .= $this->getAddCity();
        }

        return $keyword;
    }

    public function prepareImage(\SimpleXMLElement $ad): string
    {
        $result = [];
        $images = $ad->xpath('Image');
        if (is_array($images)) {
            foreach ($images as $image) {
                $result[] = (string) $image->attributes()->{'url'};
            }
            // If not skip last image
            if (!$this->isSkipLastImage()) {
                $result[] = (string) $this->getSource()['Image']->attributes()->{'url'};
            }

            return implode(", ", $result);
        }

        return (string) $images->attributes()->{'url'};
    }

    /**
     * Инициализация
     */
    public function init(): void
    {
        $this->setData($this->getProducts());
        $this->setCategories($this->getParseCategories());
    }

    /**
     * @return array
     */
    private function getProducts(): array
    {
        $result = $this->getSource()[self::SOURCE_PRODUCTS];

        return $result->xpath(self::SOURCE_PRODUCT);
    }

    /**
     * @return array
     */
    private function getParseCategories(): array
    {
        $source        = $this->getSource()[self::SOURCE_CATEGORIES];
        $categoriesXml = $source->xpath(self::SOURCE_CATEGORY);
        $result        = [];
        foreach ($categoriesXml as $category) {
            $id       = (int) $category->attributes()->{'id'}->__toString();
            $parentId = $category->attributes()->{'parentId'};

            $result[$id] = [
                self::CATEGORY_PARENT => $parentId ? (int) $parentId->__toString() : null,
                self::CATEGORY_NAME   => (string) $category->__toString(),
            ];
        }

        return $result;
    }

    /**
     * Sort columns
     */
    public function sort(): void
    {
        $array = $this->getData();
        usort(
            $array,
            static function (\SimpleXMLElement $a, \SimpleXMLElement $b) {
                $aTitle = mb_strtolower(trim((string) $a->{self::TITLE}));
                $bTitle = mb_strtolower(trim((string) $b->{self::TITLE}));

                if ($aTitle === $bTitle) {
                    return 0;
                }

                return $aTitle > $bTitle ? -1 : 1;
            }
        );
        $this->setData($array);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
