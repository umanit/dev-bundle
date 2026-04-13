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

/**
 * Vérifie qu’une classe n’utilise pas directement une classe concrète dans ses déclarations de types (paramètres de
 * méthode, propriétés, types de retour) lorsqu’une interface existe dessus.
 *
 * Exemple : si `CategoryFactory` implémente `CategoryFactoryInterface`, la déclaration `CategoryFactory $factory` est
 * une violation ; il faut utiliser `CategoryFactoryInterface`. En revanche, `new CategoryFactory()` est autorisé
 * (instanciation, pas déclaration de type).
 */
final readonly class NotUseConcreteWhenInterfaceExists implements Expression
{
    public function __construct(
        /** @var list<string> Seules les classes dont le FQCN commence par l'un de ces préfixes sont vérifiées. */
        private array $targetNamespacePrefixes = [],
    ) {
    }

    public function describe(ClassDescription $theClass, string $because): Description
    {
        return new Description(
            'should not use a concrete class in type declarations when an interface exists for it',
            $because,
        );
    }

    public function evaluate(ClassDescription $theClass, Violations $violations, string $because): void
    {
        if (!class_exists($theClass->getFQCN())) {
            return;
        }

        $absolutePath = (new \ReflectionClass($theClass->getFQCN()))->getFileName();
        if (false === $absolutePath) {
            return;
        }

        foreach ($this->findTypeDeclarations($absolutePath) as ['fqcn' => $fqcn, 'line' => $line]) {
            if (!$this->isTargetedNamespace($fqcn)) {
                continue;
            }

            if (!class_exists($fqcn)) {
                continue;
            }

            $reflection = new \ReflectionClass($fqcn);

            if (!$reflection->isInstantiable()) {
                continue;
            }

            $parentClass = $reflection->getParentClass();
            $parentInterfaces = $parentClass ? $parentClass->getInterfaceNames() : [];
            $ownInterfaces = array_diff($reflection->getInterfaceNames(), $parentInterfaces);

            $relevantInterfaces = array_values(
                array_filter($ownInterfaces, fn(string $interface): bool => $this->isTargetedNamespace($interface)),
            );

            if ([] === $relevantInterfaces) {
                continue;
            }

            $violations->add(
                Violation::createWithErrorLine(
                    $theClass->getFQCN(),
                    ViolationMessage::withDescription(
                        $this->describe($theClass, $because),
                        \sprintf(
                            'uses "%s" (concrete) but the following interface(s) exist: %s',
                            $fqcn,
                            implode(', ', $relevantInterfaces),
                        ),
                    ),
                    $line,
                    $absolutePath,
                ),
            );
        }
    }

    private function isTargetedNamespace(string $fqcn): bool
    {
        if ([] === $this->targetNamespacePrefixes) {
            return true;
        }

        foreach ($this->targetNamespacePrefixes as $prefix) {
            if (\str_starts_with($fqcn, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne les classes utilisées dans des positions de déclaration de type (propriétés, paramètres, retours).
     * Les instanciations `new Foo()` ne sont pas incluses.
     *
     * @return list<array{fqcn: string, line: int}>
     */
    private function findTypeDeclarations(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if (false === $content) {
            return [];
        }

        $parser = (new ParserFactory())->createForHostVersion();

        try {
            $ast = $parser->parse($content);
            if (null === $ast) {
                return [];
            }
        } catch (Error) {
            return [];
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\NameResolver());
        $ast = $traverser->traverse($ast);

        $result = [];

        foreach ((new NodeFinder())->findInstanceOf($ast, Node\Stmt\ClassLike::class) as $classNode) {
            foreach ($classNode->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Property && null !== $stmt->type) {
                    $this->extractTypeNames($stmt->type, $result);
                }

                if ($stmt instanceof Node\Stmt\ClassMethod) {
                    foreach ($stmt->params as $param) {
                        if (null !== $param->type) {
                            $this->extractTypeNames($param->type, $result);
                        }
                    }

                    if (null !== $stmt->returnType) {
                        $this->extractTypeNames($stmt->returnType, $result);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param list<array{fqcn: string, line: int}> $result
     */
    private function extractTypeNames(Node $typeNode, array &$result): void
    {
        if ($typeNode instanceof Node\Name\FullyQualified) {
            $result[] = ['fqcn' => $typeNode->toString(), 'line' => $typeNode->getLine()];

            return;
        }

        if ($typeNode instanceof Node\NullableType) {
            $this->extractTypeNames($typeNode->type, $result);

            return;
        }

        if ($typeNode instanceof Node\UnionType || $typeNode instanceof Node\IntersectionType) {
            foreach ($typeNode->types as $type) {
                $this->extractTypeNames($type, $result);
            }
        }
    }
}
