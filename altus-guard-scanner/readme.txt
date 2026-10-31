=== Altus Guard Scanner ===
Contributors: asifaltus
Tags: security, scanner, vulnerabilities, malware scan, hardening
Requires at least: 6.0
Tested up to: 7.1
Stable tag: 1.0.4
License: GPLv2 or later

Lightweight security scanner (Free). Scans your site, shows each problem as a notification, and gives one guided fix step per problem.

== Description ==

Altus Guard Scanner reports and guides only — it never changes your site on its own.

Free scan checks: WordPress core updates, plugin/theme updates, PHP version against known fixed branches, PHP files in the uploads folder, administrator accounts, file-editor and debug-display settings, and a sample core-file check via WordPress.org checksums.

Findings appear in wp-admin (Altus Guard menu + dashboard widget) and the site admin is emailed only when NEW critical findings appear.

Pro features shown in the plugin are planned, not active in v1. There is no payment or licence system in v1. Proposed Pro pricing (to be re-confirmed): $19/year (1 site), $49/year (5 sites), $99/year (unlimited).

== Frequently Asked Questions ==

= Does it fix problems automatically? =
No. Free shows the problem and one guided step. You (or your developer) apply the fix.

= Can it update PHP? =
No plugin can. It tells you to ask your host to update PHP.

= Does it send my data anywhere? =
No. Scan results stay in your own site database. v1 has no telemetry.

== Changelog ==
= 1.0.4 =
* Plugin Check clean: the "Scan complete" notice no longer uses a $_GET flag — it now uses a short-lived transient set by the nonce-protected scan handler, removing the 2 NonceVerification warnings from v1.0.3 (0 errors, 0 warnings expected).
= 1.0.3 =
* Renamed to Altus Guard Scanner (new slug altus-guard-scanner) to avoid confusion with the existing SiteGuard WP Plugin on WordPress.org.
* Submission polish: Plugin URI now points to altuswebsolutions.com (no example.com placeholder), admin scan-result flag is sanitized.
* No feature change vs 1.0.2; scan logic unchanged.
= 1.0.2 =
* Uploads scan now shows the FULL list of PHP files found (up to 50), not just the first 5, so an investigation is not left half-done.
* Uploads finding now notes that some files may belong to installed plugins — review each, do not delete blindly.
= 1.0.1 =
* First release: manual + daily scan, guided findings, dashboard widget, new-critical email alerts.
