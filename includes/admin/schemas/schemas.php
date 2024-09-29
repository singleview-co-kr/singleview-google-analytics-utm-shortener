<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://singleview.co.kr
 *
 * @package    singleview-google-analytics-utm-shortener
 */

namespace SV_Utm_Url_Shortener\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

global $wpdb;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
$charset_collate = $wpdb->get_charset_collate();

dbDelta(
"CREATE TABLE `{$wpdb->prefix}sv_utm_shortener` (
	`seq` bigint(64) unsigned NOT NULL AUTO_INCREMENT,
	`shorten_uri_value` varchar(50) NOT NULL,
	`author` bigint(20) unsigned NOT NULL DEFAULT 0,
	`utm_source` tinyint(4) DEFAULT NULL,
    `utm_medium` tinyint(4) DEFAULT NULL,
	`utm_term` varchar(50) DEFAULT NULL,
	`utm_content` varchar(50) DEFAULT NULL,
	`utm_campaign_id` varchar(50) DEFAULT NULL,
	`influencer_type` tinyint(4) DEFAULT NULL,
	`influencer_id` varchar(50) DEFAULT NULL,
	`fixed_cost_incl_vat` bigint(11) DEFAULT 0,
	`memo` text DEFAULT NULL,
	`click` bigint(4) DEFAULT 0,
	`allocation_start_date` date DEFAULT NULL,
	`allocation_end_date` date DEFAULT NULL,
	`update_date` date NOT NULL,
	`regdate` date NOT NULL,
	PRIMARY KEY (`seq`),
	UNIQUE KEY `unique_shorten_uri_value` (`shorten_uri_value`)
) {$charset_collate};"
);

dbDelta(
"CREATE TABLE `{$wpdb->prefix}sv_utm_shortener_log` (
	`shortener_seq` bigint(64) unsigned NOT NULL,
	`is_mobile` char(1) NOT NULL DEFAULT '0',
	`ipaddress` varchar(15) NOT NULL,
	`regdate` datetime DEFAULT NULL,
	KEY `idx_svshortener_log` (`shortener_seq`,`regdate`)
) {$charset_collate};"
);
