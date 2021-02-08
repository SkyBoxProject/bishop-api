<?php

namespace App\Module\CodingStandard\Sniffs;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;

final class MultipleBlankLinesSniff extends AbstractScopeSniff
{
    public function __construct()
    {
        parent::__construct(Tokens::$ooScopeTokens, [T_WHITESPACE]);
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPosition, $currentScopeOpener): void
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPosition]['code'] !== T_WHITESPACE) {
            return;
        }

        $nextThreePositions = [$stackPosition + 1, $stackPosition + 2, $stackPosition + 3];

        foreach ($nextThreePositions as $nextPosition) {
            $nextTokenCode = $tokens[$nextPosition]['code'] ?? [];

            if ($nextTokenCode !== T_WHITESPACE) {
                return;
            }
        }

        $phpcsFile->addError('Multiple blank lines disallowed.', $stackPosition + 2, 'Multiple whitespaces');
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPosition): void
    {
        // outside processing not required
    }
}
