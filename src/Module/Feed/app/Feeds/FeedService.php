<?php
declare(strict_types=1);

namespace App\Feeds;

class FeedService
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Feed
     */
    private $feed;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getContent(string $url): void
    {
        $xml_string = file_get_contents($url);
        $data       = (array) simplexml_load_string($xml_string, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);

        if (isset($data['Ad'])) {
            $json = json_encode($data);
            $data = json_decode($json, true);
            $this->setFeed(new MicroFeed());
            $this->getFeed()->setSource($data);
        } elseif (isset($data['products'])) {
            $this->setFeed(new FullFeed());
            $this->getFeed()->setSource($data);
        }
        $this->getFeed()->init();
        $this->getFeed()->sort();
    }

    public function getFeed(): Feed
    {
        return $this->feed;
    }

    public function setFeed(Feed $feed): void
    {
        $this->feed = $feed;
    }
}
