<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Arkitect\Expression\ForClasses;

use Arkitect\Analyzer\ClassDescription;
use Arkitect\Expression\Description;
use Arkitect\Expression\Expression;
use Arkitect\Rules\Violation;
use Arkitect\Rules\ViolationMessage;
use Arkitect\Rules\Violations;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;

class NotAbuseFinalUsage implements Expression
{
    public function describe(ClassDescription $theClass, string $because): Description
    {
        return new Description('should not as at least one of his "public" method calls another one', $because);
    }

    public function evaluate(ClassDescription $theClass, Violations $violations, string $because): void
    {
        if (!$theClass->isFinal()) {
            return;
        }

        if (!$this->hasInternalPublicMethodCall($theClass->getFQCN())) {
            return;
        }

        $violation = Violation::create(
            $theClass->getFQCN(),
            ViolationMessage::withDescription($this->describe($theClass, $because), 'is final'),
        );

        $violations->add($violation);
    }

    /**
     * Parcourt l’AST d'une classe et retourne true si une méthode « public » appelle une autre méthode « public » de
     * la même classe.
     */
    private function hasInternalPublicMethodCall(string $className): bool
    {
        /** @var class-string $className */

        // Parsing de l’AST de la classe
        $reflection = new \ReflectionClass($className);
        $file = $reflection->getFileName();
        if (false === $file) {
            throw new \RuntimeException(\sprintf('Cannot determine the file of the class %s', $className));
        }

        $content = file_get_contents($file);
        if (false === $content) {
            throw new \RuntimeException(\sprintf('Cannot read file content of %s', $file));
        }

        $parser = (new ParserFactory())->createForHostVersion();

        try {
            $ast = $parser->parse($content);
            if (null === $ast) {
                throw new \RuntimeException('AST is null');
            }
        } catch (Error $e) {
            throw new \RuntimeException('Parse error: ' . $e->getMessage());
        }

        // Récupération de la classe dans l’AST
        $finder = new NodeFinder();
        /** @var list<Node\Stmt\Class_> $classes */
        $classes = $finder->findInstanceOf($ast, Node\Stmt\Class_::class);
        $targetClass = null;

        foreach ($classes as $class) {
            if ($class->name?->toString() === $reflection->getShortName()) {
                $targetClass = $class;
                break;
            }
        }

        if (!$targetClass) {
            throw new \RuntimeException(\sprintf('Class %s not found in file %s', $className, $file));
        }

        // Récupération des méthodes « public »
        $publicMethods = [];
        foreach ($targetClass->getMethods() as $method) {
            if ($method->isPublic()) {
                $publicMethods[] = $method->name->toString();
            }
        }

        // Vérification de l’utilisation d’une autre méthode « public » dans chacune d’elle
        foreach ($targetClass->getMethods() as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $stmts = $method->getStmts() ?: [];
            /** @var list<Node\Expr\MethodCall> $calls */
            $calls = $finder->findInstanceOf($stmts, Node\Expr\MethodCall::class);

            foreach ($calls as $call) {
                // var doit être $this
                if (
                    $call->var instanceof Node\Expr\Variable
                    && 'this' === $call->var->name
                    && $call->name instanceof Node\Identifier
                ) {
                    $called = $call->name->toString();
                    if (
                        \in_array($called, $publicMethods, true)
                        && $called !== $method->name->toString()
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
