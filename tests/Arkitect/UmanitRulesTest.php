<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Arkitect;

use Arkitect\Rules\DSL\ArchRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UmanitRules::class)]
class UmanitRulesTest extends TestCase
{
    public function testAllReturnsNamingAndCodebaseRules(): void
    {
        $rules = UmanitRules::all(['App']);

        $this->assertCount(8, $rules);
        $this->assertContainsOnlyInstancesOf(ArchRule::class, $rules);
        $this->assertCount(6, UmanitRules::namingRules(['App']));
        $this->assertCount(2, UmanitRules::codebaseRules(['App']));
    }

    public function testClassesCanBeExcludedFromARule(): void
    {
        $rules = UmanitRules::all(['App'], [UmanitRules::FORM_NAMING => ['App\Form\LegacyFieldsAdder']]);

        $this->assertCount(8, $rules);
    }

    public function testUnknownRuleIdentifierIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown rule identifier(s) "unknown"');

        UmanitRules::all(['App'], ['unknown' => ['App\Foo']]);
    }

    public function testNamespacesAreCombinedWithoutDuplicatedBackslash(): void
    {
        $rules = UmanitRules::namingRules(['Akkalia\CoreBundle', 'Akkalia\UserBundle\\']);

        $this->assertCount(6, $rules);
    }
}
