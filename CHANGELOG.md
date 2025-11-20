# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/umanit/dev-bundle/compare/2.1.3...HEAD

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
