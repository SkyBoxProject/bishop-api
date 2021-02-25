<?php
declare(strict_types=1);

namespace App\Module\Feed\Common\Exception;

use App\Domain\Feed\Entity\ValueObject\FeedType;
use Exception;

final class FeedManagerNotFoundException extends Exception
{
    public function __construct(FeedType $type)
    {
        parent::__construct('Type: '.$type->getValue(), 500, null);
    }
}
