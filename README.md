# New and updated activity indicator

A Moodle local plugin that highlights recently added and recently modified
activities and resources directly on the course page, so students can quickly
spot what's new without hunting through every section.

- **"New" badges** appear next to activities that were added to the course
  within a configurable time-span.
- **"Updated" badges** appear next to activities whose content was modified
  (and that aren't already flagged as new) within that same time-span.
- An optional **recent content list** can be shown at the top of the course
  page, summarising the newest and most recently updated items.

Everything is configurable site-wide via the admin settings page, and every
course can optionally override those defaults for its own needs.

## Requirements

- Moodle 5.0+ (`$plugin->requires = 2024100700;`)
- PHP as required by your Moodle version

## Installation

1. Copy (or clone) this plugin into `local/newupdate_indicator` in your Moodle
   codebase.
2. Visit *Site administration → Notifications* to complete the installation,
   or run `php admin/cli/upgrade.php` from the command line.
3. Configure the site defaults at *Site administration → Plugins → Local
   plugins → New and updated activity indicator*.

## How it works

The plugin compares two timestamps for each activity in a course:

- `course_modules.added` — when the activity was added to the course.
- The activity instance's `timemodified` value (where the module table
  provides one) — when its content was last changed.

If an activity's *added* timestamp falls within the configured time-span, it
is shown as **new**. Otherwise, if its *modified* timestamp is more recent
than its *added* timestamp and also falls within the time-span, it is shown
as **updated**. Once both timestamps fall outside the time-span, no indicator
is shown.

Indicators are only shown for activities the current user can see
(`$cm->uservisible`).

## Site administration settings

All settings live in a single section at *Site administration → Plugins →
Local plugins → New and updated activity indicator*:

| Setting | Description |
| --- | --- |
| Indicator time-span | How long an activity continues to display a "new" or "updated" indicator after it was added or modified. |
| Show "new" indicator | Enables/disables the "new" indicator site-wide. |
| Label text / Icon / Colour style (New) | The text, icon and colour used for "new" badges. |
| Show "updated" indicator | Enables/disables the "updated" indicator site-wide, independently of the "new" indicator. |
| Label text / Icon / Colour style (Updated) | The text, icon and colour used for "updated" badges. |
| Indicator position | Where badges are placed relative to the activity link (see below). |
| Show recent content list | Displays a short summary list of new/updated content at the top of the course page. |
| Maximum number of items | How many items to show in the recent content list. |

### Indicator position

Badges can be placed:

- **Before the link** / **After the link** — inline with the activity name,
  in the normal text flow.
- **Top-left / top-right / bottom-left / bottom-right corner** — absolutely
  positioned within the padded area of the activity card, clear of the icon
  and activity name.

### Colour styles

Badge colours are named after Bootstrap's contextual colours (Primary,
Secondary, Success, Danger, Warning, Info, Light, Dark), so they adapt to
your theme's palette automatically (with sensible fallback colours for themes
that don't define the Bootstrap design tokens).

## Per-course overrides

Teachers and managers (anyone with the `local/newupdateindicator:manage`
capability — granted to the *Teacher* and *Manager* roles by default) can
override any of the site defaults for an individual course from
*Course administration → New/updated indicator settings*. This includes:

- The time-span.
- Whether the "new" and "updated" indicators are shown — these can be toggled
  independently, e.g. show "new" badges but hide "updated" badges for a
  particular course.
- The label, icon and colour for each indicator type.
- The badge position.
- Whether the recent content list is shown, and how many items it lists.

When the "Override site default settings for this course" option is
unchecked, the course follows the site defaults and any values entered are
ignored.

## Capabilities

| Capability | Description | Default roles |
| --- | --- | --- |
| `local/newupdateindicator:manage` | Manage new/updated indicator settings for a course | Teacher, Manager |

## Privacy

This plugin does not store any personal data — only per-course display
preferences (labels, icons, colours, positions, time-spans). It implements
Moodle's `null_provider` privacy interface.

## Architecture notes

- `classes/local/config.php` resolves the effective configuration for a
  course by merging site defaults (from `get_config()`) with any per-course
  override stored in the `local_newupdate_indicator` table (`null` columns
  mean "inherit the site default").
- `classes/local/indicator_finder.php` works out which course modules should
  currently display an indicator, and whether each should be "new" or
  "updated".
- `classes/local/page_injector.php` builds the badge and recent-list markup
  (server-side, using `html_writer` and Mustache templates) and assembles the
  data structure passed to the front end.
- `amd/src/indicator.js` (built to `amd/build/indicator.min.js`) places the
  pre-rendered markup into the DOM on the course page — there is no core hook
  that lets a local plugin add content directly to each activity item, so the
  module locates each activity by its `data-id` and inserts the badge at the
  configured position.
- `lib.php` hooks into `local_newupdate_indicator_before_footer()` (the
  `before_footer` callback) to load the AMD module on course view pages only.

## License

GNU GPL v3 or later. See <https://www.gnu.org/copyleft/gpl.html>.
