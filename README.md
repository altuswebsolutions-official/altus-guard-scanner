![Altus Guard Scanner — Free WordPress Security Plugin](assets/banner.png)

# Altus Guard Scanner — Free WordPress Security Plugin

**Free WordPress security scanner plugin: scan your WordPress site for security problems, see each issue in plain language, and fix it one guided step at a time.**

> **⬇️ Download now:** get the latest release zip from the [**Releases page**](../../releases) → WordPress → Plugins → Add New → Upload Plugin → Install Now → Activate.

![License: GPLv2 or later](https://img.shields.io/badge/License-GPLv2%20or%20later-blue.svg)
![WordPress tested up to 7.1](https://img.shields.io/badge/WordPress-tested%20up%20to%207.1-blue.svg)
![PHP 8.2–8.5](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-green.svg)
![No account needed](https://img.shields.io/badge/Account-not%20needed-brightgreen.svg)

**Keywords people search for:** free WordPress security plugin, WordPress security scanner, WordPress malware scanner, WordPress vulnerability scanner, WordPress security audit plugin, WordPress hacked check, lightweight WordPress security plugin, WordPress hardening plugin, WordPress update checker, WordPress PHP files in uploads check.

## Why download Altus Guard Scanner?

Most WordPress security plugins are heavy, paid, or full of jargon. Altus Guard Scanner is different:

- **Free, forever, for the full scan** — no account, no API key, no signup.
- **Lightweight** — one small plugin, no heavy firewall, no site slowdown.
- **Plain-language results** — every finding comes with *one* clear fix step, not a wall of technical warnings.
- **Reports only, never changes your site on its own** — nothing is deleted, edited, or "fixed" without you.

## What this WordPress security scanner checks

| Check | What it finds |
|---|---|
| WordPress core updates | Outdated WordPress core that hackers target first |
| Plugin & theme updates | Plugins and themes with updates waiting |
| PHP version security | Old PHP branches (like PHP 8.1) that no longer get security fixes |
| PHP files in uploads | Suspicious PHP files hiding in the uploads folder — a classic sign of a hacked WordPress site |
| Admin users | Extra or suspicious administrator accounts |
| File editor & debug settings | Dangerous settings left open on a live site |
| Core file integrity (sample) | Changed WordPress core files, checked against WordPress.org checksums |

## Is my WordPress site hacked? Run this check first

If your site is slow, redirecting visitors, showing strange ads, or Google warns about it, run Altus Guard Scanner first. In one click it tells you: outdated core, outdated plugins, old PHP, PHP files in uploads, and unexpected admins — the five most common signs of a hacked WordPress site — plus exactly what to do about each one.

## Install in 2 minutes (free WordPress security plugin download)

1. Download the latest `.zip` from the [**Releases page**](../../releases).
2. WordPress admin → **Plugins → Add New → Upload Plugin** → choose the zip → **Install Now** → **Activate**.
3. Open **Altus Guard** → click **Run Scan Now**.
4. Work through the findings, one step at a time.

No account needed. No settings to configure. The scan also runs automatically once a day, and you get an email **only when a NEW critical finding appears**.

## Who is it for?

- WordPress site owners who want a **free security check** without paying $99+/year
- Small business owners, bloggers, freelancers, and agencies managing client sites
- Anyone asking "is my WordPress site secure?" and wanting an honest answer, not a sales pitch

## What it does NOT do (honest limits)

- It does **not** clean an already-hacked site, and it does **not** guarantee 100% security — no plugin can.
- It does **not** upgrade PHP for you (only your host can do that) and it does **not** yet check a live vulnerability (CVE) database — that is planned for Pro.
- It does **not** send your data anywhere. See Privacy below.

## Privacy: no telemetry, no tracking

- No data leaves your site except: WordPress.org checksums API (core-file sample check) and an email to **your own site admin**, only on new critical findings.
- Results live in one WordPress option, visible to administrators only.
- Every action requires `manage_options` + a valid nonce. No public REST endpoint, no unauthenticated AJAX, no file upload, no custom-PHP-code box, no `eval`.

## Frequently asked questions

**Is Altus Guard Scanner really free?**
Yes. The full scan in this repo is free. A Pro version with a vulnerability feed is planned later — it is not in this release and no payment is taken here.

**Does it slow down my site?**
No. It is a small scanner that runs on demand and once a day via WP-Cron. It has no firewall and no always-on monitoring.

**Do I need an account or API key?**
No. Install, activate, scan. That is it.

**Can it replace Wordfence or Sucuri?**
For a quick, free security audit — yes. For a full firewall + malware removal service — no, those are different (and paid) products. Many site owners use Altus Guard Scanner as their free first check.

**Where can I get support?**
Open an issue in this repo with your WordPress version, PHP version, and the finding you need help with.

## Pro (planned, not active in v1)

A Pro version is planned (CVE/vulnerability feed, more checks, more sites). It is **not** in this release. Prices will be confirmed after vulnerability-feed cost research.

## License

GPLv2 or later.

## Disclaimer — please read before installing

This plugin is provided **free, "as is", without any warranty** — including no warranty that it is error-free, fit for a particular purpose, or that it will prevent every security problem. No plugin can guarantee 100% security.

- **Back up your site first, and test on a staging site if you can.** The authors are not liable for any loss, damage, or cost arising from use of this plugin, including any site breakage, data loss, or missed security issue.
- This plugin **reports problems only — it never changes, deletes, or "fixes" your site on its own**, which is exactly why that risk is kept low.
- If you are unsure about a finding, ask a WordPress professional before acting on it.

## WordPress hacking, PHP flaws, and hacked sites — check your site now

WordPress hacking news is everywhere right now, and most of it comes down to the same few weaknesses. A **PHP flaw** can let attackers run code on a server — the recent PHP2Shell PHP vulnerabilities (CVE-2026-109618 and CVE-2026-109619) were fixed only in PHP 8.5.12, 8.4.27, 8.3.36, and 8.2.35, and a WordPress update alone does not fix PHP. A **WordPress security flaw** in core itself can be just as dangerous — the WordPress 7.1.4 update fixed 22 vulnerabilities, including a remote code execution issue affecting versions 4.7 to 7.1.3.

If your WordPress site has been hacked, the signs are often quiet at first: strange PHP files appearing in the uploads folder, new admin users you did not create, outdated plugins nobody updated, or a PHP version your host stopped patching years ago. That is exactly what Altus Guard Scanner checks in one click: WordPress core updates, plugin and theme updates, your PHP version against the known fixed branches, PHP files hiding in uploads, unexpected administrators, and dangerous file-editor and debug settings.

So if you searched for "is my WordPress site hacked", "WordPress malware scanner", "free WordPress security plugin", "PHP vulnerability check", or "WordPress hacked check" — this is the free first check to run. It takes two minutes, needs no account, changes nothing on your site, and tells you in plain language what to fix first.
