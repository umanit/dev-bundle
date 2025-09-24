<?php

declare(strict_types=1);

namespace UmanITCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class TodoSniff implements Sniff
{
    /**
     * Mots interdits (insensibles à la casse), hors forme "@todo".
     *
     * @var list<string>
     */
    public array $forbiddenWords = ['TODO', 'FIXME', 'XXX'];

    /**
     * @return list<int|string>
     */
    public function register(): array
    {
        return [T_COMMENT, T_DOC_COMMENT_TAG, T_DOC_COMMENT_STRING];
    }

    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];
        $content = $token['content'];

        // Normaliser la casse du tag dans les blocs de commentaires docblock
        if (T_DOC_COMMENT_TAG === $token['code']) {
            if ('@todo' !== $content && 0 === strcasecmp($content, '@todo')) {
                $shouldBeFixed = true === $phpcsFile
                        ->addFixableError(
                            'Use lowercased "@todo".',
                            $stackPtr,
                            'TodoTagCase',
                        )
                ;

                if ($shouldBeFixed) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($stackPtr, '@todo');
                    $phpcsFile->fixer->endChangeset();
                }
            }

            return;
        }

        // Détecter et interdire les autres formes
        $words = array_map('preg_quote', $this->forbiddenWords);
        $pattern = '/(?<!@)\b(' . implode('|', $words) . ')\b(?!\w)/i';

        if (1 === preg_match($pattern, $content)) {
            $shouldBeFixed = true === $phpcsFile
                    ->addFixableError(
                        'Use "@todo" only. "%s" is not allowed.',
                        $stackPtr,
                        'ForbiddenTodoForm',
                        [$this->matchFirst($pattern, $content)],
                    )
            ;

            if ($shouldBeFixed) {
                $phpcsFile->fixer->beginChangeset();
                $fixed = preg_replace_callback($pattern, static fn(): string => '@todo', $content);
                $phpcsFile->fixer->replaceToken($stackPtr, $fixed);
                $phpcsFile->fixer->endChangeset();
            }
        }

        // Normaliser la casse du tag dans les commentaires non-docblock
        $uppercaseTodo = '/@[Tt][Oo][Dd][Oo]\b/';
        if (1 === preg_match($uppercaseTodo, $content) && !str_contains($content, '@todo')) {
            $shouldBeFixed = true === $phpcsFile
                    ->addFixableError(
                        'Use lowercased "@todo".',
                        $stackPtr,
                        'TodoCaseInComment',
                    )
            ;

            if ($shouldBeFixed) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($stackPtr, preg_replace($uppercaseTodo, '@todo', $content));
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    private function matchFirst(string $pattern, string $subject): string
    {
        if (1 === preg_match($pattern, $subject, $m)) {
            return $m[1];
        }

        return '';
    }
}
