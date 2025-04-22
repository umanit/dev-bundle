<?php

declare(strict_types=1);

namespace Umanit\DevBundle\PHPStan\Rules;

use Doctrine\ORM\QueryBuilder;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\TypeWithClassName;

class NoWhereOnQueryBuilderRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // Vérifie si l'appel est `->where()`
        if (
            !$node instanceof Node\Expr\MethodCall
            || !$node->name instanceof Node\Identifier
            || 'where' !== $node->name->name
        ) {
            return [];
        }

        // Vérifie si l’objet est de type QueryBuilder
        $calledOnType = $scope->getType($node->var);

        if ($calledOnType instanceof TypeWithClassName && QueryBuilder::class === $calledOnType->getClassName()) {
            return [
                RuleErrorBuilder::message(
                    'L’utilisation de la méthode « where » sur QueryBuilder est interdite.'
                    . ' Utilisez plutôt une méthode plus spécifique comme « andWhere » ou « orWhere ».'
                )->build(),
            ];
        }

        return [];
    }
}
