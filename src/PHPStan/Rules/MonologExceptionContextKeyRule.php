<?php

declare(strict_types=1);

namespace Umanit\DevBundle\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;
use Psr\Log\LoggerInterface;
use Umanit\DevBundle\Monolog\Psr3LoggerContextHelper;

/**
 * @implements Rule<MethodCall>
 */
final class MonologExceptionContextKeyRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $loggerType = new ObjectType(LoggerInterface::class);
        if (!$loggerType->isSuperTypeOf($scope->getType($node->var))->yes()) {
            return [];
        }

        $contextArray = Psr3LoggerContextHelper::getContextArray($node);
        if (null === $contextArray) {
            return [];
        }

        $throwableType = new ObjectType(\Throwable::class);
        $errors = [];

        foreach ($contextArray->items as $item) {
            $valueType = $scope->getType($item->value);
            if (!$throwableType->isSuperTypeOf($valueType)->yes()) {
                continue;
            }

            if ($item->key instanceof String_ && 'exception' === $item->key->value) {
                continue;
            }

            $keyDescription = null === $item->key
                ? '(index numérique)'
                : \sprintf(
                    '"%s"',
                    $item->key instanceof String_ ? $item->key->value : $scope->getType($item->key)->describe(
                        VerbosityLevel::value(),
                    ),
                );

            $errors[] = RuleErrorBuilder
                ::message(
                    \sprintf(
                        'Monolog context : utilisez la clef "exception" pour les valeurs Throwable (PSR-3).'
                        . ' Clef utilisée : %s.',
                        $keyDescription,
                    ),
                )
                ->identifier('umanit.monologExceptionContextKey')
                ->build()
            ;
        }

        return $errors;
    }
}
