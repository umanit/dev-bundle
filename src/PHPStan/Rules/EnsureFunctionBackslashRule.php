<?php

declare(strict_types=1);

namespace Umanit\DevBundle\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
class EnsureFunctionBackslashRule implements Rule
{
    private const OPTIMIZED_FUNCTIONS = [
        'array_key_exists',
        'array_slice',
        'assert',
        'boolval',
        'call_user_func',
        'call_user_func_array',
        'chr',
        'count',
        'defined',
        'doubleval',
        'floatval',
        'func_get_args',
        'func_num_args',
        'get_called_class',
        'get_class',
        'gettype',
        'in_array',
        'intval',
        'is_array',
        'is_bool',
        'is_double',
        'is_float',
        'is_int',
        'is_integer',
        'is_long',
        'is_null',
        'is_object',
        'is_real',
        'is_resource',
        'is_scalar',
        'is_string',
        'ord',
        'sizeof',
        'strlen',
        'strval',
        'sprintf',

        'constant',
        'define',
        'dirname',
        'extension_loaded',
        'function_exists',
        'is_callable',
        'ini_get',
    ];

    public function getNodeType(): string
    {
        return FuncCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node instanceof FuncCall || !$node->name instanceof Node\Name) {
            return [];
        }

        $function = $node->name->toString();

        // Vérifie si la fonction est dans la liste et n’a pas de backslash
        if (\in_array($function, self::OPTIMIZED_FUNCTIONS, true) && !$node->name->isFullyQualified()) {
            return [
                RuleErrorBuilder::message(
                    \sprintf('La fonction « %s » doit être précédée d’un backslash.', $function)
                )->build(),
            ];
        }

        return [];
    }
}
