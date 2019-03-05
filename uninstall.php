<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall, not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'shuttle_client';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_driver';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_order';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_route';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_stop';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_taxi';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_conf';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_route_available';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_route_stop';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
$table_name = $wpdb->prefix . 'shuttle_pending';
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
