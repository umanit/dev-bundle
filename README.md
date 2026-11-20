# UmanIT - Dev Bundle

This bundle is used for development at UmanIT.

It provides multiple tools and rules to ease development.

## PHP Arkitect

Rules for PHP Arkitect:

* `NotAbuseFinalUsage`: Disallow to use final classes if at least one public method of your class is called in another
  public method of the same class.
* `NotUseConcreteWhenInterfaceExists`: Disallow the use of a concrete class inside typehint if an interface exists for
  tha class.
* `NotUseGenericException`: Disallow the use of generic `\Exception` class.

### Usage

Edit your `arkitect.php` file to include the following:

```php
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Umanit\DevBundle\Arkitect\Expression\ForClasses\NotAbuseFinalUsage;
use Umanit\DevBundle\Arkitect\Expression\ForClasses\NotUseGenericException;

// [...]

$rules[] = Rule
    ::allClasses()
    ->that(new ResideInOneOfTheseNamespaces('App'))
    ->should(new NotUseGenericException())
    ->because('we want to force usage of SPL exceptions or custom ones')
;

$rules[] = Rule
    ::allClasses()
    ->that(new ResideInOneOfTheseNamespaces('App'))
    ->should(new NotUseConcreteWhenInterfaceExists())
    ->because('we want to depend on interfaces, not concrete implementations')
;

$rules[] = Rule
    ::allClasses()
    ->that(new ResideInOneOfTheseNamespaces('App'))
    ->should(new NotAbuseFinalUsage())
    ->because('we want avoid final classes which reduce extensibility')
;
```

### Standard rules

`UmanitRules` builds the standard UmanIT rules, so a project does not have to copy them:

```php
use Umanit\DevBundle\Arkitect\UmanitRules;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__ . '/src');

    $config->add($classSet, ...UmanitRules::all(['App']));
};
```

* `UmanitRules::namingRules()`: uniform naming of controllers, commands, subscribers, form classes and repositories,
  based on the sub-namespace (`App\Controller\`, `App\Command\`, etc.), and no `Enum` suffix.
* `UmanitRules::codebaseRules()`: no generic `\Exception`, no abuse of `final`.
* `UmanitRules::all()`: both of them.

Each method takes the root namespaces to check, and an optional list of classes to ignore, indexed by rule identifier
(`UmanitRules::FORM_NAMING`, etc.). Project-specific rules are still added with `Rule::allClasses()`.

## Twig-CS-Fixer

`ConfigFactory` builds the standard configuration. In your `.twig-cs-fixer.dist.php`:

```php
return \Umanit\DevBundle\TwigCsFixer\ConfigFactory::create(['templates/']);
```

## GrumPHP

`shared-config/grumphp/base.yml` declares the usual tasks (composer, phplint, git_blacklist, git_commit_message,
yamllint, phpstan, phpcs, phparkitect, phpunit, rector, twigcsfixer). Import it from your `grumphp.yml.dist`; the
tasks are merged with yours, so you can add or override any of them:

```yaml
imports:
    - { resource: vendor/umanit/dev-bundle/shared-config/grumphp/base.yml }

grumphp:
    tasks:
        eslint: ~
    git_hook_variables:
        EXEC_GRUMPHP_COMMAND: symfony php
```

Some tasks assume a configuration file at the root of the project (`phparkitect.php` for `phparkitect`, `phpstan.dist.neon`
for `phpstan`, `phpcs.xml.dist` for `phpcs`) and fail without it. A project that does not want one of the inherited tasks
can disable it:

```yaml
grumphp:
    tasks:
        phparkitect:
            metadata:
                enabled: false
```

## Rector

`UmanitRectorConfig::configure()` returns a Rector builder with the standard UmanIT configuration (PHP 8.4 sets, prepared
sets, Composer-based sets, attributes, imports and the custom rules). The project only declares its own paths, and can
still add or skip any rule on the returned builder:

```php
use Umanit\DevBundle\Rector\Config\UmanitRectorConfig;

return UmanitRectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
;
```

## PHP_CodeSniffer

`shared-config/phpcs/ruleset.xml` applies the UmanIT coding standard and excludes the files managed by Symfony.
Reference it from your `phpcs.xml.dist`, which still declares the `<file>` entries to analyze, and the `basepath` and
`cache` arguments (they are resolved from the directory of the ruleset declaring them, so they cannot be shared):

```xml

<arg name="basepath" value="." />
<arg name="cache" value=".phpcs-cache" />

<rule ref="vendor/umanit/dev-bundle/shared-config/phpcs/ruleset.xml" />

<file>src/</file>
<file>tests/</file>
```

## Foundry

Various tools to ease tests creation:

* A database reseter usable as a Symfony command.
* Some utilities function to ease the creation of entities.
* Possibility to use aliases on Doctrine entities within factories.
* A per-class key/value store accessible via `addToStore`/`getListFromStore` (lists) and `setInStore`/`getFromStore`
  (scalars), so child factories can accumulate state between persists without declaring static properties (which
  PHPStan flags on `@immutable` Foundry factories). The store is reset automatically via `ResetInterface`.

## PHPStan

Rules for PHPStan:

* `EnsureFunctionBackslashRule`: Ensure that some optimizable functions are called with backslash.
* `MonologExceptionContextKey`: Ensure that an exception inside a Monolog context use the `exception` key.
* `NoWhereOnQueryBuilderRule`: Disallow to use `where` method on `QueryBuilder` in favor of `andWhere`.

### Usage

Include the shared configurations instead of copying them in each project:

* `shared-config/phpstan/base.neon`: Doctrine and Symfony extensions, the three rules above and a default `level` (8).
* `shared-config/phpstan/deprecation.neon`: optional, reports usages of deprecated code.

```neon
includes:
    - vendor/umanit/dev-bundle/shared-config/phpstan/base.neon
    - vendor/umanit/dev-bundle/shared-config/phpstan/deprecation.neon # optional

parameters:
    level: 6 # overrides the default
    paths:
        - src/
        - tests/
```

`paths`, `excludePaths`, `scanDirectories` and project-specific `ignoreErrors` stay in the project: relative paths
are resolved from the file that declares them.

To use the rules alone, declare them under `rules:` in your `phpstan.neon`.

## TestEventDispatcher

A test double for Symfony's `EventDispatcher` that records all dispatched events so they can be inspected and asserted
on in tests without triggering real listeners.

### Usage

Bind `TestEventDispatcher` as the `EventDispatcherInterface` service in your test environment, then assert on the
events after the action under test:

```php
$event = $dispatcher->getLastDispatchedEvent(MyEvent::class);
$this->assertInstanceOf(MyEvent::class, $event);

// All events dispatched under a given name
$events = $dispatcher->getDispatchedEvents(MyEvent::class);

// Full map of every dispatched event
$all = $dispatcher->getAllDispatchedEvents();
```

## TestUtils

One static method `TestUtils::setId` to set the id of an entity by reflection. Useful for tests when your entities do
not expose a `setId` method.

### Usage

In your test:

```php
TestUtils::setId($entity, 42);
```
