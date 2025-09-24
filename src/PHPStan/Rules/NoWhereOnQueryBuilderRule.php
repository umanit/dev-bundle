<?php

declare(strict_types=1);

namespace Umanit\DevBundle\PHPStan\Rules;

use Doctrine\ORM\QueryBuilder;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<MethodCall>
 */
class NoWhereOnQueryBuilderRule implements Rule
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
        // Vérifie si l'appel est `->where()`
        if (!$node->name instanceof Node\Identifier || 'where' !== $node->name->name) {
            return [];
        }

        // Vérifie si l’objet est de type QueryBuilder
        $calledOnType = $scope->getType($node->var);

        if (\in_array(QueryBuilder::class, $calledOnType->getObjectClassNames(), true)) {
            return [
                RuleErrorBuilder
                    ::message(
                        'L’utilisation de la méthode « where » sur QueryBuilder est interdite.'
                        . ' Utilisez plutôt une méthode plus spécifique comme « andWhere » ou « orWhere ».',
                    )
                    ->identifier('umanit.noWhereOnQueryBuilder')
                    ->build(),
            ];
        }

        return [];
    }
}
