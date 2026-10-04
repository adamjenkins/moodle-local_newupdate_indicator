# Changes

## v1.1.3

- The minimum Moodle version is now 5.0 (`$plugin->requires = 2025041400`),
  matching the supported range (Moodle 5.0 to 5.3). The plugin previously
  advertised Moodle 4.5, which was never supported or tested.
- The Composer package now accepts later Moodle 5.x releases
  (`moodle/moodle` `^5.0`) instead of stopping before 5.4. The supported
  versions declared in `version.php` are unchanged.
- The automated tests now run against the released Moodle 5.3
  (`MOODLE_503_STABLE`) instead of Moodle's development branch, and those runs
  now count towards a pass.
- Each tagged release is also published to the camp plugin registry.

No database or capability changes. No action is required after upgrading.
