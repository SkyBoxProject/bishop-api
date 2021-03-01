<?php
declare(strict_types=1);

namespace App\Feeds;

use Throwable;

class MicroFeed extends Feed
{
    public const ITEMS = 'Ad';

    /**
     * @var array
     */
    private $data;

    public function run(): void
    {
        $this->file = fopen('php://output', 'wb');
        fputcsv($this->file, $this->getHeaderColumns(), ';', '"');
        $this->eachData();
    }

    /**
     * Перебор товара
     */
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

    private function processAd(array $ad): void
    {
        if ($this->isStopWords() && $this->checkStopWords($ad[self::TITLE])) {
            return;
        }

        $desc = $this->toWindows1251($this->prepareDesc($ad[self::DESCRIPTION]));
        fputcsv(
            $this->file,
            [
                $this->toWindows1251(str_replace(',', ' ', $ad[self::CATEGORY])), //Категория
                $this->toWindows1251($ad[self::TITLE]), //Товар
                $this->toWindows1251($ad[self::PRICE]), //Цена
                '', //Адрес
                1, //Видим
                0, //Хит
                '', //Бренд
                '', //Вариант
                '', //Старая цена
                $this->toWindows1251($ad[self::ID]), //Артикул
                '', //Склад
                $this->toWindows1251(mb_strtolower($ad[self::TITLE])), //Заголовок страницы
                $this->toWindows1251($this->prepareSeoKeyword($ad[self::TITLE])), //Ключевые слова
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
     * @return array
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

    /**
     * @param array|string $desc
     */
    public function prepareDesc($desc): string
    {
        if (is_array($desc)) {
            $desc = empty($desc) ? '' : $desc[0];
        }

        if (is_string($desc)) {
            $desc = str_replace('<br />', "\n", $desc);

            $desc = strip_tags($desc);
            $desc = preg_replace('!\n+!', "\n", $desc);
            $desc = str_replace(" ", '', $desc);
            $desc = preg_replace("/ {2,}/", ' ', $desc);
        } else {
            $desc = '';
        }

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

    public function prepareSeoKeyword(string $keyword): string
    {
        if ($this->isAddCity()) {
            $keyword .= 'купить в ';
            $keyword .= $this->getAddCity();
        }

        return $keyword;
    }

    public function prepareImage(array $ad): string
    {
        $image = [];
        if (isset($ad['Images'])) {
            foreach ($ad['Images']['Image'] as $url) {
                $image[] = $url['@attributes']['url'];
            }
            // If skip last image
            if ($this->isSkipLastImage()) {
                array_pop($image);
            }
        }

        return implode(", ", $image);
    }

    /**
     * Инициализация
     */
    public function init(): void
    {
        $this->setData($this->getSource()[self::ITEMS]);
    }

    /**
     * Sort columns
     */
    public function sort(): void
    {
        $array = $this->getData();
        usort(
            $array,
            static function ($a, $b) {
                $aTitle = mb_strtolower(trim($a['Title']));
                $bTitle = mb_strtolower(trim($b['Title']));

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
