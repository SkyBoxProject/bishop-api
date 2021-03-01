<?php

namespace App\Module\DataTransferObjectFactory;

final class DataTransferObjectFactory
{
    /**
     * @param mixed $dataTransferObject
     * @param mixed $value
     */
    public function resolveBoolean($dataTransferObject, $value, callable $callback): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($value === null) {
            return;
        }

        $callback($dataTransferObject, $value);
    }
}
