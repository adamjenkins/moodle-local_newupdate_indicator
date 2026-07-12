# Changelog

All notable changes to the New/updated activity indicator (`local_newupdate_indicator`) are documented in this file.

## [1.1.0] - 2026-07-12

### Added

- Course lifecycle handling: a course_deleted observer removes the course's
  override row when a course is deleted.
- Course backup and restore support: per-course indicator overrides are
  included in course backups and restored into the destination course.
- PHPUnit tests for configuration resolution and the deletion observer.

### Changed

- The page JavaScript is now injected via the Moodle 4.4+ Hooks API
  (`before_footer_html_generation`) instead of the deprecated legacy
  `before_footer` callback, which raised a deprecation notice on every page
  on Moodle 5.2 with developer debugging enabled.
- Language strings are sorted alphabetically (required by moodle-cs).
- The capability was renamed from `local/newupdateindicator:manage` to
  `local/newupdate_indicator:manage` so its prefix matches the component
  name (required by the Moodle Plugins directory validator). Sites that had
  customised role permissions for the old name will need to re-apply them.
- CI matrix corrected: Moodle 5.0 on PHP 8.2-8.3, Moodle 5.1 on PHP 8.2-8.4,
  Moodle 5.2 on PHP 8.3-8.4, each against PostgreSQL 16 and MariaDB 10.11.
- Added the moodle-release.yml workflow for automatic Moodle Plugins
  directory releases, and CHANGES.md release notes.

## [1.0.0] - 2026-06-08

Initial release.

- "New" and "Updated" badges on course page activities, based on a
  configurable time span.
- Optional "recently changed content" list at the top of the course page.
- Site-wide defaults with per-course overrides (labels, icons, colours,
  position, time span, recent-list length) for teachers with the manage
  capability.
