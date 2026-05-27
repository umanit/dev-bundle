<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Monolog;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;

final class Psr3LoggerContextHelper
{
    public const array METHODS_CONTEXT_ARG_INDEX = [
        'emergency' => 1,
        'alert'     => 1,
        'critical'  => 1,
        'error'     => 1,
        'warning'   => 1,
        'notice'    => 1,
        'info'      => 1,
        'debug'     => 1,
        'log'       => 2,
    ];

    public static function getContextArray(MethodCall $node): ?Array_
    {
        if (!$node->name instanceof Identifier) {
            return null;
        }

        $methodName = $node->name->name;
        if (!isset(self::METHODS_CONTEXT_ARG_INDEX[$methodName])) {
            return null;
        }

        $contextArgIndex = self::METHODS_CONTEXT_ARG_INDEX[$methodName];
        if (!isset($node->args[$contextArgIndex])) {
            return null;
        }

        $contextArg = $node->args[$contextArgIndex];
        if (!$contextArg instanceof Arg || !$contextArg->value instanceof Array_) {
            return null;
        }

        return $contextArg->value;
    }
}
