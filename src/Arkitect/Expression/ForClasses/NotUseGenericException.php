<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Arkitect\Expression\ForClasses;

use Arkitect\Analyzer\ClassDescription;
use Arkitect\Analyzer\FullyQualifiedClassName;
use Arkitect\Expression\Description;
use Arkitect\Expression\Expression;
use Arkitect\Rules\Violation;
use Arkitect\Rules\ViolationMessage;
use Arkitect\Rules\Violations;

class NotUseGenericException implements Expression
{
    public function describe(ClassDescription $theClass, string $because): Description
    {
        return new Description('should not use the generic exception class', $because);
    }

    public function evaluate(ClassDescription $theClass, Violations $violations, string $because): void
    {
        foreach ($theClass->getDependencies() as $dependency) {
            $extends = array_map(
                static fn(FullyQualifiedClassName $class): string => $class->toString(),
                $theClass->getExtends()
            );

            if (
                // The class doesn't extend the \Exception class
                !\in_array(\Exception::class, $extends, true)
                // The class depends on the \Exception class
                && \Exception::class === $dependency->getFQCN()->toString()
            ) {
                $violation = Violation::createWithErrorLine(
                    $theClass->getFQCN(),
                    ViolationMessage::withDescription(
                        $this->describe($theClass, $because),
                        'use the generic exception class'
                    ),
                    $dependency->getLine(),
                    $theClass->getFilePath(),
                );

                $violations->add($violation);
            }
        }
    }
}
