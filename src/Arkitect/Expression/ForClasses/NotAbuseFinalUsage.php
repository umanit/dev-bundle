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
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;

class NotAbuseFinalUsage implements Expression
{
    public function describe(ClassDescription $theClass, string $because): Description
    {
        return new Description(
            'should not as at least one of his interface methods calls another one from the same',
            $because
        );
    }

    public function evaluate(ClassDescription $theClass, Violations $violations, string $because): void
    {
        if (!$theClass->isFinal()) {
            return;
        }

        if (!$this->hasInterdependentInterfaceMethods($theClass->getFQCN())) {
            return;
        }

        $violation = Violation::create(
            $theClass->getFQCN(),
            ViolationMessage::withDescription($this->describe($theClass, $because), 'is final'),
        );

        $violations->add($violation);
    }

    /**
     * Parcourt l’AST d'une classe et retourne true si une méthode d’interface appelle une autre méthode de la même
     * interface.
     */
    private function hasInterdependentInterfaceMethods(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $reflection = new \ReflectionClass($className);

        // Collecte les méthodes publiques par interface
        $interfaces = $reflection->getInterfaceNames();
        $interfacesMethods = [];
        foreach ($interfaces as $interface) {
            $refInterface = new \ReflectionClass($interface);
            $names = array_map(
                static fn(\ReflectionMethod $m) => $m->getName(),
                $refInterface->getMethods()
            );

            if (\count($names) > 1) {
                $interfacesMethods[$interface] = $names;
            }
        }

        if (empty($interfacesMethods)) {
            return false;
        }

        // Parsing de l’AST de la classe
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
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\NameResolver());
        $ast = $traverser->traverse($ast);

        /** @var Node\Stmt\Class_|null $classNode */
        $classNode = $finder->findFirst($ast, static function ($node) use ($className) {
            return $node instanceof Node\Stmt\Class_
                && isset($node->namespacedName)
                && $className === $node->namespacedName->toString();
        });

        if (null === $classNode) {
            throw new \RuntimeException(\sprintf('Class %s not found in file %s', $className, $file));
        }

        // Parcourt des méthodes de chacune des interfaces
        foreach ($interfacesMethods as $methods) {
            foreach ($methods as $caller) {
                // Trouve la méthode appelante dans la classe
                /** @var Node\Stmt\ClassMethod|null $methodNode */
                $methodNode = $finder->findFirst([$classNode], static function ($node) use ($caller) {
                    return $node instanceof Node\Stmt\ClassMethod
                        && $node->isPublic()
                        && $caller === $node->name->toString();
                });

                if (!$methodNode) {
                    continue;
                }

                // Cherche un appel à $this->callee() où callee ≠ caller et callee ∈ methods
                $calls = $finder->find($methodNode->stmts ?? [], static function ($node) use ($methods, $caller) {
                    if (!$node instanceof Node\Expr\MethodCall) {
                        return false;
                    }

                    $var = $node->var;
                    $name = $node->name;

                    if (
                        $var instanceof Node\Expr\Variable
                        && $var->name === 'this'
                        && $name instanceof Node\Identifier
                    ) {
                        $callee = $name->toString();

                        return $callee !== $caller && \in_array($callee, $methods, true);
                    }

                    return false;
                });

                if ($calls) {
                    return true;
                }
            }
        }

        return false;
    }
}
