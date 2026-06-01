# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Add `TestEventDispatcher`, a test double for Symfony's `EventDispatcher` that records dispatched events so they can
  be inspected and asserted on in tests without triggering real listeners

## [3.1.0] - 2026-05-27

### Added

- Add Rector as a dependency
- Add a PHPStan rule to detect PSR-3 violation: If an "Exception" object is passed in the context data, it MUST be in
  the 'exception' key.
- Add a Rector rule to fix PSR-3 violation: If an "Exception" object is passed in the context data, it MUST be in the
  'exception' key.

## [3.0.1] - 2026-05-22

### Added

- Add `phpstan/phpstan-deprecation-rules` dependency

## [3.0.0] - 2026-05-21

### Added

- Add PHP 8.4 support

### Removed

- Remove PHP <8.4 support

### Changed

- Upgrade Zenstruck Foudry to 2.10
- Change bundle's Factory to use new Foundry's "PersistentObjectFactory"

## [2.2.1] - 2026-04-29

### Added

- Add support for Doctrine bundle 3.2

## [2.2.0] - 2026-04-23

### Added

- foundry: add a per-class key/value store to `Factory` to avoid declaring static properties in child factories

## [2.1.5] - 2026-04-13

### Added

- New PHPArkitect rule that prohibits the use of class names in method properties or arguments if an interface exists
  for that class

## [2.1.4] - 2025-11-19

### Fixed

- Fix Phpstan generics for AliasedFactory

## [2.1.3] - 2025-11-18

### Fixed

- Fix Phpstan generics for foundry factories

## [2.1.2] - 2025-11-14

### Added

- Add support for doctrine/orm 3.

## [2.1.1] - 2025-11-14

### Added

- Add support for Symfony 7.3

## [2.1.0] - 2025-09-26

### Removed

- Remove coding standard files

### Added

- Include `umanit/coding-standard`

## [2.0.2] - 2025-09-26

### Changed

- [PHPCS] Update to version 4
- [PHPCS] PHPUnit data provider methods are the latest in the file

## [2.0.1] - 2025-09-25

### Added

- Allow PHPUnit `protected function tearDown` before all others, as `protected function setUp`

## [2.0.0] - 2025-09-24

### Added

- Sniff PHPCS to allow only the "@todo" form to mark "to do" code
- Coding Standard PHPCS UmanIT pre-configured for use in projects

### Changed

- Upgrade to PHPStan 2

## [1.1.1] - 2025-05-14

### Fixed

- zenstruck/foundry: force version 2.4.* as our code is not working with 2.5 for now

## [1.1.0] - 2025-04-30

### Added

- add multiple dependencies for static analyze

### Changed

- phparkitect: `NotAbuseFinalUsage` is triggered if an interface method called another one from the same

## [1.0.0] - 2025-04-24

### Added

- phparkitect: add dependency
- phparkitect: add `NotAbuseFinalUsage` and `NotUseGenericException` rules

## [0.0.3] - 2025-04-24

### Added

- add `mockery/mockery` dependency

## [0.0.2] - 2025-04-23

### Added

- foundry: add a `Randomizer` class

## [0.0.1] - 2025-04-22

Initial release

[Unreleased]: https://github.com/umanit/dev-bundle/compare/3.1.0...HEAD

[3.1.0]: https://github.com/umanit/dev-bundle/compare/3.0.1...3.1.0

[3.0.1]: https://github.com/umanit/dev-bundle/compare/3.0.0...3.0.1

[3.0.0]: https://github.com/umanit/dev-bundle/compare/2.2.0...3.0.0

[2.2.0]: https://github.com/umanit/dev-bundle/compare/2.1.5...2.2.0

[2.1.5]: https://github.com/umanit/dev-bundle/compare/2.1.4...2.1.5

[2.1.4]: https://github.com/umanit/dev-bundle/compare/2.1.3...2.1.4

[2.1.3]: https://github.com/umanit/dev-bundle/compare/2.1.2...2.1.3

[2.1.2]: https://github.com/umanit/dev-bundle/compare/2.1.1...2.1.2

[2.1.1]: https://github.com/umanit/dev-bundle/compare/2.1.0...2.1.1

[2.1.0]: https://github.com/umanit/dev-bundle/compare/2.0.2...2.1.0

[2.0.2]: https://github.com/umanit/dev-bundle/compare/2.0.1...2.0.2

[2.0.1]: https://github.com/umanit/dev-bundle/compare/2.0.0...2.0.1

[2.0.0]: https://github.com/umanit/dev-bundle/compare/1.1.1...2.0.0

[1.1.1]: https://github.com/umanit/dev-bundle/compare/1.1.0...1.1.1

[1.1.0]: https://github.com/umanit/dev-bundle/compare/1.0.0...1.1.0

[1.0.0]: https://github.com/umanit/dev-bundle/compare/0.0.3...1.0.0

[0.0.3]: https://github.com/umanit/dev-bundle/compare/0.0.2...0.0.3

[0.0.2]: https://github.com/umanit/dev-bundle/compare/0.0.1...0.0.2

[0.0.1]: https://github.com/umanit/dev-bundle/releases/tag/0.0.1
