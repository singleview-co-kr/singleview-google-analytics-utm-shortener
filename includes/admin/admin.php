<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://singleview.co.kr
 * @since 0.0.1
 *
 * @package    singleview-google-analytics-utm-shortener
 * @subpackage Admin
 */

namespace SV_Utm_Url_Shortener\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'shortener.admin.view.php';
require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'shortener.admin.controller.php';

if ( ! defined( 'UTM_SHORTENER_CMD_ADMIN_VIEW_LIST' ) ) {
	// define admin view cmd
	define( 'UTM_SHORTENER_CMD_ADMIN_VIEW_LIST', 'utm_shortener_disp_list' );
	define( 'UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT', 'utm_shortener_disp_insert' );
	define( 'UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE', 'utm_shortener_disp_update' );

	// define admin controller cmd
	define( 'UTM_SHORTENER_CMD_ADMIN_PROC_DECIDE_SHORTENER_QRY', 'utm_shortener_proc_decice_shortener_qry' );
	define( 'UTM_SHORTENER_CMD_ADMIN_PROC_INSERT', 'utm_shortener_proc_insert' );
	define( 'UTM_SHORTENER_CMD_ADMIN_PROC_UPDATE', 'utm_shortener_proc_update' );
}

/*  Plugins Activation Hook */
function activate() {
	require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'schemas' . DIRECTORY_SEPARATOR . 'schemas.php';
}

register_activation_hook( UTM_SHORTENER__FILE__, 'SV_Utm_Url_Shortener\Includes\Admin\activate' );

/* Plugins Loaded Hook */
function plugin_loaded() {
	// third parameter should be relative path to WP_PLUGIN_DIR
	load_plugin_textdomain( UTM_SHORTENER_DOMAIN, false, DIRECTORY_SEPARATOR . 'singleview-google-analytics-utm-shortener' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'languages' );
}
add_action( 'plugins_loaded', 'SV_Utm_Url_Shortener\Includes\Admin\plugin_loaded' );

$A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE = array();

/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 0.0.1
 *
 * @global
 * @return void
 */
function add_admin_pages_links() {
	global $A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE;

	global $_wp_last_object_menu;
	++$_wp_last_object_menu;
	// visible admin page
	add_menu_page( UTM_SHORTENER_ADMIN_PAGE_TITLE, UTM_SHORTENER_ADMIN_PAGE_TITLE, 'manage_utm_shortener', UTM_SHORTENER_CMD_ADMIN_VIEW_LIST, 'SV_Utm_Url_Shortener\Includes\Admin\disp_admin_shortener', 'dashicons-admin-post', $_wp_last_object_menu );
	$A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE[] = add_submenu_page( UTM_SHORTENER_CMD_ADMIN_VIEW_LIST, UTM_SHORTENER_ADMIN_PAGE_TITLE, __( 'cmd_insert', UTM_SHORTENER_DOMAIN ), 'manage_utm_shortener', UTM_SHORTENER_CMD_ADMIN_VIEW_LIST, 'SV_Utm_Url_Shortener\Includes\Admin\disp_admin_shortener' );
	// hidden admin page
	$A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE[] = add_submenu_page( null, UTM_SHORTENER_ADMIN_PAGE_TITLE, null, 'manage_utm_shortener', UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT, 'SV_Utm_Url_Shortener\Includes\Admin\disp_admin_shortener' );
	$A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE[] = add_submenu_page( null, UTM_SHORTENER_ADMIN_PAGE_TITLE, null, 'manage_utm_shortener', UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE, 'SV_Utm_Url_Shortener\Includes\Admin\disp_admin_shortener' );
}
add_action( 'admin_menu', 'SV_Utm_Url_Shortener\Includes\Admin\add_admin_pages_links', 99 );


/* Plugins Loaded Hook */
function admin_init() {
	// 관리자에게 manage_utm_shortener 권한 추가
	$admin_role = get_role( 'administrator' );
	if ( ! $admin_role->has_cap( 'manage_utm_shortener' ) ) {
		$admin_role->add_cap( 'manage_utm_shortener', true );
	}

	add_action( 'admin_post_' . UTM_SHORTENER_CMD_ADMIN_PROC_DECIDE_SHORTENER_QRY, 'SV_Utm_Url_Shortener\Includes\Admin\proc_admin_shortener' );
	add_action( 'admin_post_' . UTM_SHORTENER_CMD_ADMIN_PROC_INSERT, 'SV_Utm_Url_Shortener\Includes\Admin\proc_admin_shortener' );
	add_action( 'admin_post_' . UTM_SHORTENER_CMD_ADMIN_PROC_UPDATE, 'SV_Utm_Url_Shortener\Includes\Admin\proc_admin_shortener' );
}
add_action( 'admin_init', 'SV_Utm_Url_Shortener\Includes\Admin\admin_init' );

/**
 * Trigger Shortener Admin View.
 *
 * @return void
 */
function disp_admin_shortener() {
	$o_module       = new \SV_Utm_Url_Shortener\Includes\Modules\Shortener\shortenerdAdminView();
	$calling_method = isset( $_REQUEST['page'] ) ? str_replace( UTM_SHORTENER_DOMAIN . '_', '', sanitize_text_field( $_REQUEST['page'] ) ) : '';
	if ( ! method_exists( $o_module, $calling_method ) ) {
		wp_die( sprintf( __( 'msg_call_invalid_module', UTM_SHORTENER_DOMAIN ), $calling_method ) );
	}
	$o_module->$calling_method();
	unset( $o_module );
}

/**
 * Trigger Shortener Admin control.
 */
function proc_admin_shortener() {
	$o_module       = new \SV_Utm_Url_Shortener\Includes\Modules\Shortener\shortenerAdminController();
	$calling_method = isset( $_REQUEST['action'] ) ? str_replace( UTM_SHORTENER_DOMAIN . '_', '', sanitize_text_field( $_REQUEST['action'] ) ) : '';
	if ( ! method_exists( $o_module, $calling_method ) ) {
		wp_die( sprintf( __( 'msg_call_invalid_module', UTM_SHORTENER_DOMAIN ), $calling_method ) );
	}
	$o_module->$calling_method();
	unset( $o_module );
	exit; // to execute wp_redirect(admin_url());
}

/**
 * Add rating links to the admin dashboard
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function footer( $footer_text ) {
	global $A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE;
	$o_current_screen = get_current_screen();
	$s_screen_id = $o_current_screen->id;
	unset( $o_current_screen );
	if ( in_array( $s_screen_id, $A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE, true ) ) {
		$text = sprintf(
			__( 'msg_thank_you_using_utm_shortener', UTM_SHORTENER_DOMAIN ),
			'https://singleview.co.kr/utm_shortener',
			'https://wordpress.org/support/plugin/utm_shortener/reviews/#new-post'
		);
		return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';
	} else {
		return $footer_text;
	}
}
add_filter( 'admin_footer_text', 'SV_Utm_Url_Shortener\Includes\Admin\footer' );

/**
 * Enqueue Admin JS
 *
 * @param string $hook The current admin page.
 */
function load_scripts( $hook ) {
	global $A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE;

	// dummy script container to load $a_ajax_info below
	wp_register_script(
		UTM_SHORTENER_DOMAIN . '-ajax-scripts',
		null,
		array(),
		UTM_SHORTENER_VERSION,
		true
	);

	wp_register_style(
		UTM_SHORTENER_DOMAIN . '-admin-style',
		UTM_SHORTENER_URL . 'includes/admin/css/admin.css',
		array(),
		UTM_SHORTENER_VERSION
	);

	if ( in_array( $hook, $A_UTM_SHORTENER_ADMIN_SETTINGS_PAGE, true ) ) {
		wp_enqueue_style( UTM_SHORTENER_DOMAIN . '-admin-style' );
		wp_enqueue_script( UTM_SHORTENER_DOMAIN . '-ajax-scripts' );
	}
}
add_action( 'admin_enqueue_scripts', 'SV_Utm_Url_Shortener\Includes\Admin\load_scripts' );