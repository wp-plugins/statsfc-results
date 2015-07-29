=== StatsFC Results ===
Contributors: willjw
Donate link:
Tags: widget, football, soccer, results, premier league, fa cup, league cup
Requires at least: 3.3
Tested up to: 4.2.2
Stable tag: 1.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This widget will place list of football results in your website.

== Description ==

Add a list of football results to your WordPress website. To request a key sign up for your free trial at [statsfc.com](https://statsfc.com).

For a demo, check out [wp.statsfc.com/results/](http://wp.statsfc.com/results/).

== Installation ==

1. Upload the `statsfc-results` folder and all files to the `/wp-content/plugins/` directory
2. Activate the widget through the 'Plugins' menu in WordPress
3. Drag the widget to the relevant sidebar on the 'Widgets' page in WordPress
4. Set the StatsFC key and any other options. If you don't have a key, sign up for free at [statsfc.com](https://statsfc.com)

You can also use the `[statsfc-results]` shortcode, with the following options:

- `key` (required): Your StatsFC key
- `competition` (required*): Competition key, e.g., `EPL`
- `team` (required*): Team name, e.g., `Liverpool`
- `highlight` (optional): The name of the team you want to highlight, e.g., `Liverpool`
- `from` (optional): Date to show results from, e.g., `2014-01-01`
- `to` (optional): Date to show results to, e.g., `2014-01-07`
- `limit` (optional): Maximum number of results to show, e.g., `4`, `10`
- `goals` (optional): Show goal scorers, `true` or `false`
- `show_badges` (optional): Display team badges, `true` or `false`
- `show_dates` (optional): Display match dates, `true` or `false`
- `order` (optional): Whether to order ascending or descending, `asc` or `desc` *(default)*
- `timezone` (optional): The timezone to convert match times to, e.g., `Europe/London` ([complete list](https://php.net/manual/en/timezones.php))
- `default_css` (optional): Use the default widget styles, `true` or `false`

*Only one of `competition` or `team` is required.

== Frequently asked questions ==



== Screenshots ==



== Changelog ==

**1.0.1**: Fixed various bugs. More relevant scores/status for matches that were decided after extra-time or penalties.

**1.1**: Added a setting for number of results. Applies to single team only. Choose '0' to display all results.

**1.1.1**: Fixed a bug when selecting a specific team.

**1.2**: Added Community Shield results.

**1.2.1**: Use cURL to fetch API data if possible.

**1.2.2**: Fixed possible cURL bug.

**1.2.3**: Added fopen fallback if cURL request fails.

**1.2.4**: Tweaked error message.

**1.3**: Allow an actual timezone to be selected, and use the new API.

**1.3.3**: Tweaked CSS.

**1.4**: Added `[statsfc-results]` shortcode.

**1.4.2**: Updated team badges.

**1.4.3**: Default `default_css` parameter to `true`

**1.4.4**: Added badge class for each team

**1.4.5**: Use built-in WordPress HTTP API functions

**1.4.6**: Added `order` parameter

**1.5**: Enabled ad-support

**1.5.1**: Allow more discrete ads for ad-supported accounts

**1.6**: Added `highlight`, `goals`, `show_badges` and `show_dates` parameters

**1.6.1**: Fixed bug saving 'Highlight team', 'Show goals', 'Show badges' and 'Show dates' options

**1.6.2**: Minor bug fix with `to` example value

== Upgrade notice ==

