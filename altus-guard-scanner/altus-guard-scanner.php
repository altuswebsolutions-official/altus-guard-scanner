<?php
/**
 * Plugin Name: Altus Guard Scanner
 * Plugin URI: https://altuswebsolutions.com/
 * Description: Lightweight security scanner (Free). Scans your site, shows each problem as a notification, and gives one guided fix step per problem. It reports and guides only — it never changes your site on its own. Pro features shown in the plugin are planned, not active in v1.
 * Version: 1.0.3
 * Author: Altus Web Solutions
 * License: GPLv2 or later
 * Text Domain: altus-guard-scanner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AGS_VERSION', '1.0.3' );
define( 'AGS_OPTION', 'altus_guard_scanner_last_scan' );
define( 'AGS_CRON_HOOK', 'altus_guard_scanner_daily_scan' );

/**
 * Known fixed PHP branches (Oct 2026 PHP2Shell fixes).
 * Key = branch, value = first fixed version in that branch.
 */
function ags_fixed_php_branches() {
	return array(
		'8.2' => '8.2.35',
		'8.3' => '8.3.36',
		'8.4' => '8.4.27',
		'8.5' => '8.5.12',
	);
}

/**
 * Run a full scan. Returns an array of findings.
 * Each finding: severity (critical|warning|ok), title, detail, step.
 * This function only READS. It never updates, deletes, or fixes anything.
 */
function ags_run_scan() {
	$findings = array();

	/* 1. WordPress core version. */
	$wp_version = get_bloginfo( 'version' );
	$core_updates = get_site_transient( 'update_core' );
	$latest_core = '';
	if ( is_object( $core_updates ) && ! empty( $core_updates->updates ) && is_array( $core_updates->updates ) ) {
		foreach ( $core_updates->updates as $u ) {
			if ( isset( $u->response ) && 'latest' === $u->response && ! empty( $u->current ) ) {
				$latest_core = sanitize_text_field( $u->current );
				break;
			}
		}
	}
	if ( '' !== $latest_core && version_compare( $wp_version, $latest_core, '<' ) ) {
		$findings[] = array(
			'severity' => 'critical',
			'title'    => 'WordPress is out of date',
			'detail'   => sprintf( 'Installed: %s. Latest: %s.', $wp_version, $latest_core ),
			'step'     => 'Step 1: Go to Dashboard > Updates and click Update Now for WordPress.',
		);
	} else {
		$findings[] = array(
			'severity' => 'ok',
			'title'    => 'WordPress core is up to date',
			'detail'   => sprintf( 'Installed: %s.', $wp_version ),
			'step'     => 'No action needed.',
		);
	}

	/* 2. Plugin updates available. */
	$plugin_updates = get_site_transient( 'update_plugins' );
	$outdated_plugins = 0;
	if ( is_object( $plugin_updates ) && ! empty( $plugin_updates->response ) && is_array( $plugin_updates->response ) ) {
		$outdated_plugins = count( $plugin_updates->response );
	}
	if ( $outdated_plugins > 0 ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Plugins have updates available',
			'detail'   => sprintf( '%d plugin(s) have an update waiting.', $outdated_plugins ),
			'step'     => 'Step 1: Go to Dashboard > Updates, tick the plugins, and click Update Plugins.',
		);
	} else {
		$findings[] = array(
			'severity' => 'ok',
			'title'    => 'No plugin updates waiting',
			'detail'   => 'All installed plugins are on their latest available version.',
			'step'     => 'No action needed.',
		);
	}

	/* 3. Theme updates available. */
	$theme_updates = get_site_transient( 'update_themes' );
	$outdated_themes = 0;
	if ( is_object( $theme_updates ) && ! empty( $theme_updates->response ) && is_array( $theme_updates->response ) ) {
		$outdated_themes = count( $theme_updates->response );
	}
	if ( $outdated_themes > 0 ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Themes have updates available',
			'detail'   => sprintf( '%d theme(s) have an update waiting.', $outdated_themes ),
			'step'     => 'Step 1: Go to Appearance > Themes and update the theme shown as having an update.',
		);
	}

	/* 4. PHP version against known fixed branches. */
	$php_version = phpversion();
	$branch = substr( $php_version, 0, 3 );
	$fixed = ags_fixed_php_branches();
	if ( isset( $fixed[ $branch ] ) ) {
		if ( version_compare( $php_version, $fixed[ $branch ], '<' ) ) {
			$findings[] = array(
				'severity' => 'critical',
				'title'    => 'PHP version needs a security fix',
				'detail'   => sprintf( 'Installed: %s. Fixed version for this branch: %s.', $php_version, $fixed[ $branch ] ),
				'step'     => 'Step 1: Ask your hosting provider to update PHP to ' . $fixed[ $branch ] . ' or newer. A plugin cannot update PHP itself.',
			);
		} else {
			$findings[] = array(
				'severity' => 'ok',
				'title'    => 'PHP version has the known security fix',
				'detail'   => sprintf( 'Installed: %s.', $php_version ),
				'step'     => 'No action needed.',
			);
		}
	} elseif ( version_compare( $php_version, '8.2', '<' ) ) {
		$findings[] = array(
			'severity' => 'critical',
			'title'    => 'PHP version is too old for security fixes',
			'detail'   => sprintf( 'Installed: %s. This branch no longer receives the security fixes checked by this plugin.', $php_version ),
			'step'     => 'Step 1: Ask your hosting provider to move PHP to 8.3 or newer.',
		);
	}

	/* 5. Uploads folder scan for PHP files (depth-limited, count-capped). */
	$upload_dir = wp_upload_dir();
	$php_in_uploads = ags_scan_uploads_for_php( $upload_dir['basedir'] );
	if ( count( $php_in_uploads ) > 0 ) {
		$findings[] = array(
			'severity' => 'critical',
			'title'    => 'PHP files found in the uploads folder',
			'detail'   => sprintf( '%d PHP file(s) found. Uploads should hold media, not PHP. Full list: %s. Note: some may belong to installed plugins (cache, index, or log files) — review each file, do not delete blindly.', count( $php_in_uploads ), implode( ', ', $php_in_uploads ) ),
			'step'     => 'Step 1: Do NOT delete blindly. Ask a developer to check each listed file first — a PHP file in uploads is a common sign of a hack.',
		);
	} else {
		$findings[] = array(
			'severity' => 'ok',
			'title'    => 'No PHP files in the uploads folder',
			'detail'   => 'Uploads scan found no .php files.',
			'step'     => 'No action needed.',
		);
	}

	/* 6. Admin user audit (usernames only, escaped on output). */
	$admins = get_users( array( 'role' => 'administrator', 'fields' => array( 'ID', 'user_login' ) ) );
	$admin_names = array();
	foreach ( $admins as $a ) {
		$admin_names[] = $a->user_login;
	}
	if ( in_array( 'admin', $admin_names, true ) ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'An administrator account is named "admin"',
			'detail'   => 'The username "admin" is the first name attackers try.',
			'step'     => 'Step 1: Create a new administrator with a different username, then remove the "admin" account (attribute its content to the new account).',
		);
	}
	if ( count( $admins ) > 2 ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Many administrator accounts',
			'detail'   => sprintf( '%d administrator accounts exist.', count( $admins ) ),
			'step'     => 'Step 1: Go to Users > All Users and remove administrator rights from anyone who does not need them.',
		);
	}

	/* 7. Hardening checks (read-only — the plugin changes nothing here). */
	if ( ! defined( 'DISALLOW_FILE_EDIT' ) || ! DISALLOW_FILE_EDIT ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Theme/plugin file editor is enabled',
			'detail'   => 'Anyone with admin access can edit theme and plugin files from wp-admin.',
			'step'     => 'Step 1: Ask your developer to add define( \'DISALLOW_FILE_EDIT\', true ); to wp-config.php.',
		);
	}
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Debug display is on',
			'detail'   => 'Errors may be shown to visitors, leaking server details.',
			'step'     => 'Step 1: Ask your developer to turn WP_DEBUG_DISPLAY off in wp-config.php.',
		);
	}

	/* 8. Core-file sample check (best-effort; "not checked" on API failure). */
	$core_check = ags_check_core_sample();
	if ( 'not-checked' === $core_check ) {
		$findings[] = array(
			'severity' => 'warning',
			'title'    => 'Core-file check could not run',
			'detail'   => 'The WordPress.org checksums service could not be reached during this scan.',
			'step'     => 'Step 1: Run the scan again later. This is not a clean bill of health and not a warning of a hack.',
		);
	} elseif ( 'mismatch' === $core_check ) {
		$findings[] = array(
			'severity' => 'critical',
			'title'    => 'A sampled core file does not match WordPress.org',
			'detail'   => 'At least one sampled WordPress core file differs from the official copy.',
			'step'     => 'Step 1: Ask a developer to compare the core files and reinstall WordPress core from wordpress.org if needed. Do not delete files blindly.',
		);
	}

	return $findings;
}

/**
 * Scan the uploads directory for .php files.
 * Depth-limited (4), file-count-capped (5000), realpath-contained.
 * Returns a list of relative paths (max 50 stored).
 */
function ags_scan_uploads_for_php( $base_dir ) {
	$found = array();
	$base_real = realpath( $base_dir );
	if ( false === $base_real || ! is_dir( $base_real ) ) {
		return $found;
	}
	$stack = array( array( $base_real, 0 ) );
	$seen = 0;
	while ( ! empty( $stack ) && $seen < 5000 && count( $found ) < 50 ) {
		$item = array_pop( $stack );
		$dir = $item[0];
		$depth = $item[1];
		$entries = scandir( $dir );
		if ( false === $entries ) {
			continue;
		}
		foreach ( $entries as $entry ) {
			if ( '.' === $entry || '..' === $entry ) {
				continue;
			}
			$seen++;
			$path = $dir . DIRECTORY_SEPARATOR . $entry;
			$real = realpath( $path );
			if ( false === $real ) {
				continue;
			}
			if ( 0 !== strpos( $real, $base_real ) ) {
				continue;
			}
			if ( is_dir( $real ) ) {
				if ( $depth < 4 ) {
					$stack[] = array( $real, $depth + 1 );
				}
			} elseif ( is_file( $real ) ) {
				$ext = strtolower( pathinfo( $real, PATHINFO_EXTENSION ) );
				if ( 'php' === $ext ) {
					$found[] = substr( $real, strlen( $base_real ) + 1 );
				}
			}
			if ( $seen >= 5000 || count( $found ) >= 50 ) {
				break;
			}
		}
	}
	return $found;
}

/**
 * Compare a small sample of core files against WordPress.org checksums.
 * Returns 'ok', 'mismatch', or 'not-checked'.
 */
function ags_check_core_sample() {
	$version = get_bloginfo( 'version' );
	$locale = get_locale();
	$url = add_query_arg(
		array( 'version' => $version, 'locale' => $locale ),
		'https://api.wordpress.org/core/checksums/1.0/'
	);
	$response = wp_remote_get( $url, array( 'timeout' => 10 ) );
	if ( is_wp_error( $response ) ) {
		return 'not-checked';
	}
	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );
	if ( ! is_array( $data ) || empty( $data['checksums'] ) || ! is_array( $data['checksums'] ) ) {
		return 'not-checked';
	}
	$sample = array( 'wp-admin/admin.php', 'wp-includes/version.php', 'wp-login.php', 'wp-settings.php' );
	foreach ( $sample as $file ) {
		if ( ! isset( $data['checksums'][ $file ] ) ) {
			continue;
		}
		$full = ABSPATH . $file;
		$real = realpath( $full );
		if ( false === $real || 0 !== strpos( $real, realpath( ABSPATH ) ) || ! is_file( $real ) ) {
			continue;
		}
		if ( md5_file( $real ) !== $data['checksums'][ $file ] ) {
			return 'mismatch';
		}
	}
	return 'ok';
}

/**
 * Admin UI, cron, dashboard widget and email alerts for Altus Guard Scanner.
 * Security rules enforced here: capability check + nonce on every action,
 * all output escaped, no public endpoint, no file upload, no remote code.
 */

/* Save a scan, emailing the admin ONLY when new critical findings appear. */
function ags_save_scan( $findings ) {
	$previous = get_option( AGS_OPTION, false );
	$prev_criticals = array();
	if ( is_array( $previous ) && ! empty( $previous['findings'] ) ) {
		foreach ( $previous['findings'] as $f ) {
			if ( isset( $f['severity'] ) && 'critical' === $f['severity'] && isset( $f['title'] ) ) {
				$prev_criticals[] = $f['title'];
			}
		}
	}
	$new_criticals = array();
	foreach ( $findings as $f ) {
		if ( isset( $f['severity'] ) && 'critical' === $f['severity'] && isset( $f['title'] ) ) {
			$new_criticals[] = $f['title'];
		}
	}
	$new_only = array_diff( $new_criticals, $prev_criticals );
	update_option(
		AGS_OPTION,
		array(
			'time'     => current_time( 'mysql' ),
			'findings' => $findings,
		),
		false
	);
	if ( ! empty( $new_only ) ) {
		$admin_email = get_option( 'admin_email' );
		if ( is_email( $admin_email ) ) {
			$subject = '[Altus Guard] New critical security findings on ' . get_bloginfo( 'name' );
			$message = "Altus Guard Scanner found new critical findings. Log in to wp-admin and open Altus Guard to see each finding and its one guided fix step.\n\nNew critical findings:\n- " . implode( "\n- ", $new_only ) . "\n";
			wp_mail( $admin_email, $subject, $message );
		}
	}
}

/* Manual scan handler: admin-post, capability + nonce, then redirect back. */
function ags_handle_manual_scan() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to run a scan.', 'altus-guard-scanner' ) );
	}
	check_admin_referer( 'altusguard_run_scan' );
	$findings = ags_run_scan();
	ags_save_scan( $findings );
	wp_safe_redirect( add_query_arg( 'ags_scanned', '1', admin_url( 'admin.php?page=altus-guard-scanner' ) ) );
	exit;
}
add_action( 'admin_post_altusguard_run_scan', 'ags_handle_manual_scan' );

/* Daily cron scan. */
function ags_daily_scan() {
	$findings = ags_run_scan();
	ags_save_scan( $findings );
}
add_action( AGS_CRON_HOOK, 'ags_daily_scan' );

function ags_activate() {
	if ( ! wp_next_scheduled( AGS_CRON_HOOK ) ) {
		wp_schedule_event( time(), 'daily', AGS_CRON_HOOK );
	}
}
register_activation_hook( __FILE__, 'ags_activate' );

function ags_deactivate() {
	$timestamp = wp_next_scheduled( AGS_CRON_HOOK );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, AGS_CRON_HOOK );
	}
}
register_deactivation_hook( __FILE__, 'ags_deactivate' );

/* Admin menu page. */
function ags_admin_menu() {
	add_menu_page(
		'Altus Guard Scanner',
		'Altus Guard',
		'manage_options',
		'altus-guard-scanner',
		'ags_render_admin_page',
		'dashicons-shield',
		80
	);
}
add_action( 'admin_menu', 'ags_admin_menu' );

function ags_render_finding( $f ) {
	$severity = isset( $f['severity'] ) ? $f['severity'] : 'warning';
	$label = 'critical' === $severity ? 'CRITICAL' : ( 'ok' === $severity ? 'OK' : 'WARNING' );
	echo '<div style="border:1px solid #ddd;padding:12px;margin:12px 0;">';
	echo '<strong>[' . esc_html( $label ) . '] ' . esc_html( isset( $f['title'] ) ? $f['title'] : '' ) . '</strong><br>';
	echo esc_html( isset( $f['detail'] ) ? $f['detail'] : '' ) . '<br>';
	echo '<em>' . esc_html( isset( $f['step'] ) ? $f['step'] : '' ) . '</em>';
	echo '</div>';
}

function ags_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'altus-guard-scanner' ) );
	}
	$scan = get_option( AGS_OPTION, false );
	echo '<div class="wrap"><h1>Altus Guard Scanner (Free)</h1>';
	echo '<p>This plugin reports problems and guides you, one step at a time. It never changes your site on its own.</p>';
	$ags_scanned = isset( $_GET['ags_scanned'] ) ? sanitize_text_field( wp_unslash( $_GET['ags_scanned'] ) ) : '';
	if ( '1' === $ags_scanned ) {
		echo '<div class="notice notice-success"><p>Scan complete.</p></div>';
	}
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="altusguard_run_scan">';
	wp_nonce_field( 'altusguard_run_scan' );
	submit_button( 'Run Scan Now' );
	echo '</form>';
	if ( is_array( $scan ) && ! empty( $scan['findings'] ) ) {
		echo '<h2>Last scan: ' . esc_html( $scan['time'] ) . '</h2>';
		foreach ( $scan['findings'] as $f ) {
			ags_render_finding( $f );
		}
	} else {
		echo '<p>No scan has run yet. Click "Run Scan Now".</p>';
	}
	echo '<h2>Altus Guard Pro (planned — not active in v1)</h2>';
	echo '<p>Pro will add: hourly scans, CVE feed, login protection, file quarantine with backup, security-release auto-updates, 90-day history, PDF reports.</p>';
	echo '<p><strong>Proposed price: $19/year (1 site), $49/year (5 sites), $99/year (unlimited).</strong> Prices are proposed only and will be re-confirmed after CVE-feed cost research. There is no payment or licence system in v1.</p>';
	echo '<p><strong>Free limits:</strong> daily scan only, no CVE feed, no auto-fix, no quarantine, last scan only, no priority support.</p>';
	echo '</div>';
}

/* Dashboard widget: counts only, no detail leak beyond admin screens. */
function ags_dashboard_widget() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$scan = get_option( AGS_OPTION, false );
	$critical = 0;
	$warning = 0;
	if ( is_array( $scan ) && ! empty( $scan['findings'] ) ) {
		foreach ( $scan['findings'] as $f ) {
			if ( isset( $f['severity'] ) && 'critical' === $f['severity'] ) {
				$critical++;
			} elseif ( isset( $f['severity'] ) && 'warning' === $f['severity'] ) {
				$warning++;
			}
		}
	}
	echo '<p>Critical: ' . intval( $critical ) . ' &nbsp;|&nbsp; Warnings: ' . intval( $warning ) . '</p>';
	echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=altus-guard-scanner' ) ) . '">Open Altus Guard for the full list and fix steps.</a></p>';
}
function ags_add_dashboard_widget() {
	wp_add_dashboard_widget( 'altus_guard_scanner_widget', 'Altus Guard Scanner', 'ags_dashboard_widget' );
}
add_action( 'wp_dashboard_setup', 'ags_add_dashboard_widget' );
