<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Arkitect\Expression\ForClasses;

use Arkitect\Analyzer\ClassDescription;
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
            if (
                // The class depends on the \Exception class
                \Exception::class === $dependency->getFQCN()->toString()
                // The class doesn't extend the \Exception class
                && \Exception::class !== $theClass->getExtends()?->toString()
            ) {
                $violation = Violation::createWithErrorLine(
                    $theClass->getFQCN(),
                    ViolationMessage::withDescription(
                        $this->describe($theClass, $because),
                        'use the generic exception class'
                    ),
                    $dependency->getLine()
                );

                $violations->add($violation);
            }
        }
    }
}
