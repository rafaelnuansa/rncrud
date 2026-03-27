# Changelog

All notable changes to **RNCrud** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.1.2] - 2026-03-28

### Added
- **Interactive Field Builder**: Users can now define fields and data types step-by-step if the `--fields` flag is omitted.
- **Smart Relation Detection**: Automatically sets `foreignId` and `belongsTo` when a field name ends with `_id`.
- **Improved UX**: Added confirmation prompts and loops for adding multiple fields easily.

### Changed
- Refined the interactive flow to be more user-friendly.

---

## [1.1.1] - 2026-03-23

### Added
- **Laravel Prompts Integration**: Replaced standard `choice` with `multiselect` and `select` for a modern terminal UI.
- **Keyboard Navigation**: Support for Arrow Keys to navigate and Spacebar to toggle file selections.

### Fixed
- Improved file generation logic for sub-folders/namespaces.

---

## [1.1.0] - 2026-03-19

### Added
- **Soft Delete Support**: Added `--soft-delete` (shortcut `-s`) flag to automatically add traits and migration columns.
- **No-Migration Flag**: Added `--no-migration` (shortcut `-m`) to skip database migration generation.
- **API Mode**: Support for generating API Controllers with JSON responses.

---

## [1.0.5] - 2026-02-15

### Added
- Initial support for multi-namespace (sub-folders).
- Automatic route appending for `web.php` and `api.php`.

---

## [1.0.0] - 2025-12-23

### Added
- Initial release of RNCrud.
- Basic CRUD generation (Model, Controller, Migration, Blade Views).
- Support for Laravel 10 and 11.

---

[1.1.2]: https://github.com/rafaelnuansa/rncrud/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/rafaelnuansa/rncrud/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/rafaelnuansa/rncrud/compare/v1.0.5...v1.1.0
[1.0.5]: https://github.com/rafaelnuansa/rncrud/compare/v1.0.0...v1.0.5