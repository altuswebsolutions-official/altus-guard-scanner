# Altus Guard Scanner

Lightweight WordPress security scanner (Free). It scans your site, shows each problem as a notification, and gives **one guided fix step per problem**. It reports and guides only — it never changes your site on its own.

## Free scan checks

- WordPress core updates
- Plugin and theme updates
- PHP version against known fixed branches (8.2.35 / 8.3.36 / 8.4.27 / 8.5.12)
- PHP files in the uploads folder (full list shown, not just the first few)
- Administrator accounts
- File-editor and debug-display settings
- A sample core-file check via WordPress.org checksums

Findings appear in wp-admin (Altus Guard menu + dashboard widget). The site admin is emailed **only when NEW critical findings appear**.

## Install

1. Download the latest release zip from the [Releases](../../releases) page.
2. In WordPress: Plugins → Add New → Upload Plugin → choose the zip → Install Now → Activate.
3. Open **Altus Guard** in wp-admin and click **Run Scan Now**.

## Privacy

No telemetry, no tracking, no data sent anywhere except:
- WordPress.org checksums API (for the core-file sample check)
- Email to the site admin, only on new critical findings

Results are stored in one WordPress option, visible to administrators only. All actions require `manage_options` capability and a valid nonce. No public REST endpoint, no unauthenticated AJAX, no file upload, no custom-PHP-code box, no `eval`.

## Pro (planned, not active in v1)

A Pro version is planned (CVE/vulnerability feed, more checks, more sites). It is **not** in this release and no payment is taken here. Prices will be confirmed after the vulnerability-feed cost research.

## License

GPLv2 or later.
