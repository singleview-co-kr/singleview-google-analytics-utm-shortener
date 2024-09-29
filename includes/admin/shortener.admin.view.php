<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  shortenerdAdminView
 * @author singleview.co.kr
 * @brief  shortener module admin view class
 **/
namespace SV_Utm_Url_Shortener\Includes\Modules\Shortener;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\SV_Utm_Url_Shortener\\Includes\\Modules\\Shortener\\shortenerdAdminView')) {

	class shortenerdAdminView {

		public function __construct(){
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || ! current_user_can( 'manage_utm_shortener' ) ) {
				unset($o_current_user);
				wp_die( __( 'msg_no_permission', UTM_SHORTENER_DOMAIN ) );
			}
			unset( $o_current_user );

			global $a_influencer_type;
			$a_influencer_type = array(
				1 => __( 'lbl_influencer', UTM_SHORTENER_DOMAIN ), // 블로거 or 인스타 인플루언서
				2 => __( 'lbl_top_ranker', UTM_SHORTENER_DOMAIN ), // 상위노출
				3 => __( 'lbl_penetration', UTM_SHORTENER_DOMAIN ), // 카페, 지식인 진입
			);
		}

		/**
		 * @brief display shortener list
		 **/
		public function disp_list() {
			$b_shortener_query_designated = false;
			if( get_option( UTM_SHORTENER_DOMAIN . '_shortener_query' ) ) {
				$b_shortener_query_designated = true;
			}

			require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'wp_admin_class' . DIRECTORY_SEPARATOR . 'wp_shortener_list.php';
			$o_shortener_list = new \SV_Utm_Url_Shortener\Includes\Modules\Shortener\WpAdminClass\wpShortenerList();
			$s_insert_shortener_url = esc_url( admin_url( "admin.php?page=" . UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT ) );
			include_once UTM_SHORTENER_PATH .'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'shortener_list.php';
			unset( $o_shortener_list );
		}

		/**
		 * @brief display the selected board configuration
		 **/
		public function disp_update() {
			$this->disp_insert();
		}

		/**
		 * @brief display the board insert form
		 **/
		public function disp_insert() {
			require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'settings-page.php';
			require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'default-settings.php';
			require_once UTM_SHORTENER_PATH . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'register-settings.php';
			\SV_Utm_Url_Shortener\Includes\Admin\Tpl\register_settings();
			\SV_Utm_Url_Shortener\Includes\Admin\Tpl\render_options_page();
		}
	} // END CLASS
}
