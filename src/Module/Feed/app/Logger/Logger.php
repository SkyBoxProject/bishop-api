<?php
declare(strict_types=1);

namespace App\Logger;

use SimpleXMLElement;
use Throwable;

final class Logger
{
    /**
     * @param Throwable        $exception
     * @param SimpleXMLElement|array $ad
     */
    public function exceptionWithAd(Throwable $exception, $ad): void
    {
        file_put_contents(
            './logs/' . date('d.m.Y') . '.log',
            "\n[" . date('d.m.Y H:i:s') . sprintf('] Ошибка: %s. Файл (%s): %s. Данные: %s', $exception->getMessage(), $exception->getLine(), $exception->getFile(), json_encode((array) $ad)),
            FILE_APPEND | LOCK_EX
        );
    }
}
