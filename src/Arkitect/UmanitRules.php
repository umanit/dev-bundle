<?php

declare(strict_types=1);

namespace Umanit\DevBundle\Arkitect;

use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\IsEnum;
use Arkitect\Expression\ForClasses\MatchOneOfTheseNames;
use Arkitect\Expression\ForClasses\NotHaveNameMatching;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\DSL\ArchRule;
use Arkitect\Rules\Rule;
use Umanit\DevBundle\Arkitect\Expression\ForClasses\NotUseGenericException;

/**
 * Standard UmanIT architecture rules, to use from a project's phparkitect.php.
 *
 * ```php
 * $config->add($classSet, ...UmanitRules::all(['App']));
 * ```
 *
 * Every rule can be excluded for some classes through the `$except` argument, indexed by rule identifier:
 *
 * ```php
 * UmanitRules::all(['App'], [UmanitRules::FORM_NAMING => [LegacyFieldsAdder::class]]);
 * ```
 */
final class UmanitRules
{
    public const string CONTROLLER_NAMING = 'controller_naming';
    public const string COMMAND_NAMING = 'command_naming';
    public const string SUBSCRIBER_NAMING = 'subscriber_naming';
    public const string FORM_NAMING = 'form_naming';
    public const string ENUM_NAMING = 'enum_naming';
    public const string REPOSITORY_NAMING = 'repository_naming';
    public const string GENERIC_EXCEPTION = 'generic_exception';

    private const array RULES = [
        self::CONTROLLER_NAMING,
        self::COMMAND_NAMING,
        self::SUBSCRIBER_NAMING,
        self::FORM_NAMING,
        self::ENUM_NAMING,
        self::REPOSITORY_NAMING,
        self::GENERIC_EXCEPTION,
    ];

    private function __construct()
    {
    }

    /**
     * All the standard rules.
     *
     * @param list<string>                $namespaces Root namespaces of the code to check (e.g. `['App']`)
     * @param array<string, list<string>> $except     Classes to ignore, indexed by rule identifier
     *
     * @return list<ArchRule>
     */
    public static function all(array $namespaces, array $except = []): array
    {
        self::assertKnownRules($except);

        return [...self::namingRules($namespaces, $except), ...self::codebaseRules($namespaces, $except)];
    }

    /**
     * Rules enforcing a uniform naming of classes, based on their sub-namespace (`Controller`, `Command`, etc.).
     *
     * @param list<string>                $namespaces Root namespaces of the code to check (e.g. `['App']`)
     * @param array<string, list<string>> $except     Classes to ignore, indexed by rule identifier
     *
     * @return list<ArchRule>
     */
    public static function namingRules(array $namespaces, array $except = []): array
    {
        self::assertKnownRules($except);

        return [
            Rule
                ::allClasses()
                ->except('*Trait', ...($except[self::CONTROLLER_NAMING] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...self::subNamespaces($namespaces, 'Controller')))
                ->should(new MatchOneOfTheseNames(['*Controller', '*Action']))
                ->because('we want uniform controllers and actions naming'),
            Rule
                ::allClasses()
                ->except(...($except[self::COMMAND_NAMING] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...self::subNamespaces($namespaces, 'Command')))
                ->should(new HaveNameMatching('*Command'))
                ->because('we want uniform commands naming'),
            Rule
                ::allClasses()
                ->except(...($except[self::SUBSCRIBER_NAMING] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...self::subNamespaces($namespaces, 'Subscriber')))
                ->should(new HaveNameMatching('*Subscriber'))
                ->because('we want uniform event subscribers naming'),
            Rule
                ::allClasses()
                ->except('*Interface', '*Trait', ...($except[self::FORM_NAMING] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...self::subNamespaces($namespaces, 'Form')))
                ->should(
                    new MatchOneOfTheseNames(['*Mapper', '*Transformer', '*Type', '*TypeExtension', '*Subscriber']),
                )
                ->because('we want uniform form classes naming'),
            Rule
                ::allClasses()
                ->except(...($except[self::ENUM_NAMING] ?? []))
                ->that(new IsEnum())
                ->should(new NotHaveNameMatching('*Enum'))
                ->because('we don\'t want use "Enum" suffix'),
            Rule
                ::allClasses()
                ->except('*Trait', ...($except[self::REPOSITORY_NAMING] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...self::subNamespaces($namespaces, 'Repository')))
                ->should(new MatchOneOfTheseNames(['*Repository', '*RepositoryInterface']))
                ->because('we want uniform naming for repositories'),
        ];
    }

    /**
     * Rules applying to the whole code of the given namespaces.
     *
     * @param list<string>                $namespaces Root namespaces of the code to check (e.g. `['App']`)
     * @param array<string, list<string>> $except     Classes to ignore, indexed by rule identifier
     *
     * @return list<ArchRule>
     */
    public static function codebaseRules(array $namespaces, array $except = []): array
    {
        self::assertKnownRules($except);

        return [
            Rule
                ::allClasses()
                ->except(...($except[self::GENERIC_EXCEPTION] ?? []))
                ->that(new ResideInOneOfTheseNamespaces(...$namespaces))
                ->should(new NotUseGenericException())
                ->because('we want to force usage of SPL exceptions or custom ones'),
        ];
    }

    /**
     * @param list<string> $namespaces
     *
     * @return list<string>
     */
    private static function subNamespaces(array $namespaces, string $subNamespace): array
    {
        return array_map(
            static fn(string $namespace): string => rtrim($namespace, '\\') . '\\' . $subNamespace . '\\',
            $namespaces,
        );
    }

    /**
     * @param array<string, list<string>> $except
     */
    private static function assertKnownRules(array $except): void
    {
        $unknown = array_diff(array_keys($except), self::RULES);

        if ([] !== $unknown) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Unknown rule identifier(s) "%s". Known identifiers: "%s".',
                    implode('", "', $unknown),
                    implode('", "', self::RULES),
                ),
            );
        }
    }
}
