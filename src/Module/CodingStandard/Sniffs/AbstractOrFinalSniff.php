<?php

namespace App\Module\CodingStandard\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;

final class AbstractOrFinalSniff implements Sniff
{
    /**
     * @var int[]
     */
    private array $tokens = [
        T_ABSTRACT,
        T_FINAL,
    ];

    private ?Fixer $fixer = null;
    private int $position;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * {@inheritDoc}
     */
    public function process(File $file, $position): void
    {
        $this->fixer = $file->fixer;
        $this->position = $position;
        $finalAnnotations = AnnotationHelper::getAnnotationsByName($file, $position, '@final');

        if ($file->findPrevious($this->tokens, $position) || !empty($finalAnnotations)) {
            return;
        }

        $file->addFixableError(
            'All classes should be declared using either the "abstract" or "final" keyword',
            $position - 1,
            self::class
        );

        $this->fix();
    }

    private function fix(): void
    {
        $this->fixer->addContent($this->position - 1, 'final ');
    }
}
