<?php
declare(strict_types=1);

namespace App\Module\Feed\Common\Exception;

use Exception;

final class MultipleSupportForTypeException extends Exception
{
    public function __construct($type)
    {
        parent::__construct('Type: '.$type, 500, null);
    }
}
