<?php

namespace App\Module\Feed;

use App\Domain\Feed\Entity\ValueObject\FeedType;
use SimpleXMLElement;

final class FeedChecker
{
    public function checkUrl(string $url): FeedType
    {
        $content = $this->getContent($url);

        if (!empty($content->xpath('Ad'))) {
            return FeedType::micro();
        }

        if (!empty($content->xpath('products'))) {
            return FeedType::basic();
        }

        throw new \RuntimeException('Feed not found');
    }

    public function getContent(string $url): SimpleXMLElement
    {
        $xmlString = file_get_contents($url);

        return simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
    }
}
