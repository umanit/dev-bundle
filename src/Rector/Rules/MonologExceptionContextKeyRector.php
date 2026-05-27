<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Psr\Log\LoggerInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Umanit\DevBundle\Monolog\Psr3LoggerContextHelper;

/**
 * Renames Throwable context keys in PSR-3 logger calls to "exception".
 */
final class MonologExceptionContextKeyRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        $loggerType = new ObjectType(LoggerInterface::class);
        if (!$this->isObjectType($node->var, $loggerType)) {
            return null;
        }

        $contextArray = Psr3LoggerContextHelper::getContextArray($node);
        if (null === $contextArray) {
            return null;
        }

        $throwableType = new ObjectType(\Throwable::class);
        $changed = false;

        foreach ($contextArray->items as $item) {
            if (!$throwableType->isSuperTypeOf($this->getType($item->value))->yes()) {
                continue;
            }

            if ($item->key instanceof String_ && 'exception' === $item->key->value) {
                continue;
            }

            $item->key = new String_('exception');
            $changed = true;
        }

        return $changed ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rename Throwable context keys to "exception" in PSR-3 logger calls (Monolog)',
            [
                new CodeSample(
                    '$logger->error(\'message\', [\'e\' => $exception])',
                    '$logger->error(\'message\', [\'exception\' => $exception])',
                ),
            ],
        );
    }
}
