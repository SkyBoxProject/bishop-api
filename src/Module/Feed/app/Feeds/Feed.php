<?php
declare(strict_types=1);

namespace App\Feeds;

use App\Logger\Logger;

abstract class Feed
{
    public const ID          = 'Id';
    public const TITLE       = 'Title';
    public const DESCRIPTION = 'Description';
    public const CATEGORY    = 'Category';
    public const PRICE       = 'Price';

    /**
     * @var array
     */
    protected $source;

    /**
     * @var array
     */
    protected $headerColumns;

    /**
     * @var mixed
     */
    protected $file;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var bool
     */
    protected $isSkipLastImage = false;

    protected $isSkipNotAvailable = false;

    /**
     * @var bool
     */
    protected $isStopWords = false;

    /**
     * @var string
     */
    protected $pregStringStopWords;

    /**
     * @var bool
     */
    protected $isCutText = false;

    /**
     * @var string
     */
    protected $cutText;

    /**
     * @var bool
     */
    protected $isAddCity = false;

    /**
     * @var string
     */
    protected $addCity;

    /**
     * @var bool
     */
    protected $isAppendEndDescriptionText = false;

    /**
     * @var string
     */
    protected $appendEndDescriptionText;

    /**
     * @var \App\Interfaces\Feed
     */
    private $service;

    protected $logger;

    public function __construct(array $headerColumns = null)
    {
        $this->setHeaderColumns($headerColumns);
        $this->logger = new Logger();
    }

    /**
     * @return array
     */
    public function getHeaderColumns(): array
    {
        return $this->headerColumns;
    }

    /**
     * Set columns
     *
     * @param array|null $columns
     */
    public function setHeaderColumns(array $columns = null): void
    {
        $this->headerColumns = $columns ?? [
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
            ];
    }

    /**
     * @return false|string
     */
    public function readFile()
    {
        $handle = stream_get_contents($this->file);
        fclose($this->file);

        return $handle;
    }

    public function toWindows1251($str): string
    {
        return mb_convert_encoding($str, "windows-1251", "utf-8");
    }

    public function isSkipLastImage(): bool
    {
        return $this->isSkipLastImage;
    }

    public function setIsSkipLastImage(bool $isSkipLastImage): void
    {
        $this->isSkipLastImage = $isSkipLastImage;
    }

    public function isSkipNotAvailable(): bool
    {
        return $this->isSkipNotAvailable;
    }

    public function setIsSkipNotAvailable(bool $isSkipNotAvailable): void
    {
        $this->isSkipNotAvailable = $isSkipNotAvailable;
    }

    public function isStopWords(): bool
    {
        return $this->isStopWords;
    }

    public function setIsStopWords(bool $isStopWords, array $data = []): void
    {
        $this->isStopWords = $isStopWords;
        if ($isStopWords && count($data) > 0) {
            $this->pregStringStopWords = $this->generatePregStopWords($data);
        } elseif ($isStopWords && count($data) === 0) {
            $this->isStopWords = false;
        }
    }

    /**
     * Генерация регулярки для определения стоп слов
     *
     * @param array $stop
     * @return string
     */
    private function generatePregStopWords(array $stop): string
    {
        $str = '/(';
        $str .= implode("|", $stop);
        $str .= ')/miu';

        return $str;
    }

    /**
     * Проверка на наличие стоп слов в строке
     *
     * @param $string
     * @return bool
     */
    public function checkStopWords($string): bool
    {
        return preg_match_all($this->pregStringStopWords, $string) > 0;
    }

    /**
     * @return bool
     */
    public function isCutText(): bool
    {
        return $this->isCutText;
    }

    /**
     * @param bool $isCutText
     */
    public function setIsCutText(bool $isCutText, string $text = null): void
    {
        $this->isCutText = $isCutText;
        if ($isCutText && $text !== null) {
            $this->cutText = $text;
        } elseif ($isCutText && $text === null) {
            $this->isCutText = false;
        }
    }

    /**
     * @return bool
     */
    public function isAddCity(): bool
    {
        return $this->isAddCity;
    }

    /**
     * @param bool        $isAddCity
     * @param string|null $city
     */
    public function setIsAddCity(bool $isAddCity, string $city = null): void
    {
        $this->isAddCity = $isAddCity;
        if ($isAddCity && $city !== null) {
            $this->addCity = $city;
        } elseif ($isAddCity && $city === null) {
            $this->isAddCity = false;
        }
    }

    /**
     * @return bool
     */
    public function isAppendEndDescriptionText(): bool
    {
        return $this->isAppendEndDescriptionText;
    }

    /**
     * @param bool $isAppendEndEescriptionText
     */
    public function setIsAppendEndDescriptionText(bool $isAppendEndEescriptionText, string $text = null): void
    {
        $this->isAppendEndDescriptionText = $isAppendEndEescriptionText;
        if ($isAppendEndEescriptionText && $text !== null) {
            $this->appendEndDescriptionText = $text;
        } elseif ($isAppendEndEescriptionText && $text === null) {
            $this->isAppendEndDescriptionText = false;
        }
    }

    /**
     * @return string
     */
    public function getCutText(): string
    {
        return $this->cutText;
    }

    /**
     * @return string
     */
    public function getAddCity(): string
    {
        return $this->addCity;
    }

    /**
     * @return string
     */
    public function getAppendEndDescriptionText(): string
    {
        return $this->appendEndDescriptionText;
    }

    /**
     * @return \App\Interfaces\Feed
     */
    public function getService(): \App\Interfaces\Feed
    {
        return $this->service;
    }

    /**
     * @param \App\Interfaces\Feed $service
     */
    public function setService(\App\Interfaces\Feed $service): void
    {
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function getSource(): array
    {
        return $this->source;
    }

    /**
     * @param array $source
     */
    public function setSource(array $source): void
    {
        $this->source = $source;

    }

}
