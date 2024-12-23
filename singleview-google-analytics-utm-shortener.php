<?php
/**
 * Plugin Name:       UTM url shortener for Google Analytics 4
 * Description:       Shorten complicated and dizzy UTM url for Google Analytics 4
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.0.1
 * Author:            singleview.co.kr
 * Author URI:        https://singleview.co.kr/
 * Tested up to:      6.7.1
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       singleview-google-analytics-utm-shortener
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}

if ( !defined( 'UTM_SHORTENER_VERSION' ) ) {
    define('UTM_SHORTENER_VERSION', '0.0.1');
}
if ( !defined( 'UTM_SHORTENER_DOMAIN' ) ) {
    define('UTM_SHORTENER_DOMAIN', 'utm_shortener');
}
if ( !defined( 'UTM_SHORTENER_ADMIN_PAGE_TITLE' ) ) {
    define('UTM_SHORTENER_ADMIN_PAGE_TITLE', 'UTM shortener');
}

if ( !defined( 'UTM_SHORTENER__FILE__' ) ) {
    define('UTM_SHORTENER__FILE__', __FILE__);
    define('UTM_SHORTENER_PATH', plugin_dir_path(UTM_SHORTENER__FILE__));
    define('UTM_SHORTENER_URL', plugins_url('/', UTM_SHORTENER__FILE__));
}

if ( !isset($a_utm_source ) ) {
    $a_utm_source_info = array( 
        1 => 'naver', 2 => 'google', 3 => 'youtube', 
        4 => 'facebook', 5 => 'instagram', 6 => 'kakao', 7 => 'mobon' 
    );
    $a_utm_source_mapper = array( 
        1 => 'NV', 2 => 'GG', 3 => 'YT', 
        4 => 'FB', 5 => 'IG', 6 => 'KKO', 7 => 'MBO' 
    );
    // $a_search_type = array( 
    //     1 => 'PS', // paid search 
    //     2 => 'PNS', // paid naturals search
    //     3 => 'SNS', // social network service
    // );
    $a_utm_medium_info = array(
        1 => 'cpc', 2 => 'display', 3 => 'referal'
    );
    $a_utm_medium_mapper = array(
        1 => 'CPC', 2 => 'DISP', 3 => 'REF'
    );
    $a_influencer_type = array( // allocate translataion in \includes\admin\shortener.admin.view.php::__construct(), PO translation not work in this point
        1 => 'lbl_influencer', // 블로거 or 인스타 인플루언서
        2 => 'lbl_top_ranker', // 상위노출
        3 => 'lbl_penetration', // 카페, 지식인 진입
    );
}

/*
 *----------------------------------------------------------------------------
 * Guest Service Functionality
 *----------------------------------------------------------------------------
 */
if ( !is_admin() || !defined( 'WP_CLI' ) ) {
	require_once UTM_SHORTENER_PATH . 'includes/user.php';
}

/*
 *----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin.php';
} // End if.
