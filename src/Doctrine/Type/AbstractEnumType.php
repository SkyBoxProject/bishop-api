<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use MyCLabs\Enum\Enum;
use ReflectionClass;
use UnexpectedValueException;

abstract class AbstractEnumType extends Type
{
    abstract protected function getEnumClassName(): string;

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($enumValue, AbstractPlatform $platform): ?Enum
    {
        if (null === $enumValue) {
            return null;
        }

        $enumClassName = $this->getValidEnumClassName();

        return new $enumClassName($enumValue);
    }

    /**
     * {@inheritDoc}
     */
    final public function convertToDatabaseValue($enumValueObject, AbstractPlatform $platform)
    {
        if (null === $enumValueObject) {
            return null;
        }

        $expectedEnumClassName = $this->getValidEnumClassName();

        if (!$enumValueObject instanceof $expectedEnumClassName) {
            throw new UnexpectedValueException(sprintf('Enum value object must be instanced of %s (%s)', Enum::class, $expectedEnumClassName));
        }

        return $enumValueObject->getValue();
    }

    private function getValidEnumClassName(): string
    {
        try {
            $enumClassName = $this->getEnumClassName();
            $enumClassReflection = new ReflectionClass($enumClassName);

            if (false === $enumClassReflection->isSubclassOf(Enum::class)) {
                throw new UnexpectedValueException(sprintf('Enum value object must be instanced of %s (%s)', Enum::class, $enumClassName));
            }

            foreach ($enumClassName::toArray() as $enumValue) {
                if (false === is_scalar($enumValue)) {
                    throw new UnexpectedValueException('Enum type support only scalar types');
                }
            }
        } catch (\Throwable $exception) {
            throw new UnexpectedValueException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $enumClassName;
    }
}
