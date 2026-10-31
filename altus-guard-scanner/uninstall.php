<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'altus_guard_scanner_last_scan' );
$timestamp = wp_next_scheduled( 'altus_guard_scanner_daily_scan' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'altus_guard_scanner_daily_scan' );
}
