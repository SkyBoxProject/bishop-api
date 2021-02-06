<?php

namespace Tests\Functional;

use Liip\FunctionalTestBundle\Validator\DataCollectingValidator;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ValidationTestCase extends TestCase
{
    use FixturesTrait;

    private ValidatorInterface $validator;

    protected function assertOnlyFieldsAreInvalid($command, array $properties, $value, string $message): void
    {
        $commandReflection = new ReflectionClass(get_class($command));

        foreach ($commandReflection->getProperties(ReflectionProperty::IS_PUBLIC) as $field) {
            $pattern = "#(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_].*)#";
            preg_match_all($pattern, $field->getDocComment(), $matches, PREG_PATTERN_ORDER);

            if (in_array('@Assert\Valid', $matches[0], true)) {
                continue;
            }

            $field->setValue($command, $value);
        }

        $validationErrors = $this->getValidator()->validate($command);

        $fieldsWithError = [];

        foreach ($validationErrors as $error) {
            if ($message !== $error->getMessage()) {
                continue;
            }

            $fieldsWithError[] = $error->getPropertyPath();
        }

        $notWaitedValidationFields = array_diff($fieldsWithError, $properties);

        self::assertEmpty(
            $notWaitedValidationFields,
            sprintf('The field(s) %s should not get the error message "%s"', implode(', ', $notWaitedValidationFields), $message)
        );

        foreach ($properties as $field) {
            self::assertContains(
                $field,
                $fieldsWithError,
                sprintf('The field %s has not the error message "%s" after validation', $field, $message)
            );
        }
    }

    protected function getValidator(): ValidatorInterface
    {
        if ($this->validator instanceof DataCollectingValidator) {
            return $this->validator;
        }

        return $this->getContainer()->get('validator');
    }
}
