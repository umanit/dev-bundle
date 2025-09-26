# UmanIT - Dev Bundle

This bundle is used for development at UmanIT.

It provides multiple tools and rules to ease development.

## PHP Arkitect

Rules for PHP Arkitect:

* `NotAbuseFinalUsage`: Disallow to use final classes if at least one public method of your class is called in another
  public method of the same class.
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
    ->should(new NotAbuseFinalUsage())
    ->because('we want avoid final classes which reduce extensibility')
;
```

## Foundry

Various tools to ease tests creation:

* A database reseter usable as a Symfony command.
* Some utilities function to ease the creation of entities.
* Possibility to use aliases on Doctrine entities within factories.

## PHPStan

Rules for PHPStan:

* `EnsureFunctionBackslashRule`: Ensure that some optimizable functions are called with backslash.
* `NoWhereOnQueryBuilderRule`: Disallow to use `where` method on `QueryBuilder` in favor of `andWhere`.

### Usage

Edit your `phpstan.neon` file to include the following:

```neon
rules:
    - Umanit\DevBundle\PHPStan\Rules\EnsureFunctionBackslashRule
    - Umanit\DevBundle\PHPStan\Rules\NoWhereOnQueryBuilderRule
```

## TestUtils

One static method `TestUtils::setId` to set the id of an entity by reflection. Useful for tests when your entities do
not expose a `setId` method.

### Usage

In your test:

```php
TestUtils::setId($entity, 42);
```
