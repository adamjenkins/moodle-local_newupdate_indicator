# Changes

## v1.1.0

- Per-course overrides now survive course backup/restore, and are cleaned up
  when a course is deleted.
- Capability renamed to `local/newupdate_indicator:manage` (prefix must match
  the component name); re-apply any custom role permissions after upgrading.
- CI tests Moodle 5.0, 5.1 and 5.2 with compatible PHP versions
  (5.0: 8.2-8.3, 5.1: 8.2-8.4, 5.2: 8.3-8.4).

## v1.0.0

First public release.

- Adds "New" and "Updated" badges to activities on the course page, based on
  a configurable time span, plus an optional list of recently changed content.
- Site-wide defaults with per-course overrides for labels, icons, colours,
  badge position, time span and list length.
