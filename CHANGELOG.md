# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/2.0.0...HEAD

[2.0.0]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/1.1.1...2.0.0

[1.1.1]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/1.1.0...1.1.1

[1.1.0]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/1.0.0...1.1.0

[1.0.0]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/0.0.3...1.0.0

[0.0.3]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/0.0.2...0.0.3

[0.0.2]: https://holygit.umanit.fr/umanit/dev-bundle/-/compare/0.0.1...0.0.2

[0.0.1]: https://holygit.umanit.fr/umanit/dev-bundle/-/tags/0.0.1
