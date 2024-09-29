<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  shortenerdAdminController
 * @author singleview.co.kr
 * @brief  shortener module admin controller class
 **/
namespace SV_Utm_Url_Shortener\Includes\Modules\Shortener;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\SV_Utm_Url_Shortener\\Includes\\Modules\\Shortener\\shortenerAdminController')) {

	class shortenerAdminController {

		/**
		 * @brief constructor
		 **/
		public function __construct(){
			$o_current_user = wp_get_current_user();
			if( !user_can( $o_current_user, 'administrator' ) || !current_user_can('manage_'.UTM_SHORTENER_DOMAIN) ) {
				unset($o_current_user);
				wp_die(__('msg_no_permission', UTM_SHORTENER_DOMAIN));
			}
			unset($o_current_user);
		}
		
		/**
		 * @brief fix shortener query
		 **/
		public function proc_decice_shortener_qry() {
			check_admin_referer( UTM_SHORTENER_CMD_ADMIN_PROC_DECIDE_SHORTENER_QRY );  // check nounce
			update_option( UTM_SHORTENER_DOMAIN . '_shortener_query', $_POST['utm_shortener_query'] );
			wp_redirect( admin_url( 'admin.php?page='.UTM_SHORTENER_CMD_ADMIN_VIEW_LIST ) );
			exit;
		}

		/**
		 * @brief update shortener
		 **/
		public function proc_update() {
			check_admin_referer( UTM_SHORTENER_CMD_ADMIN_PROC_UPDATE );  // check nounce

			// begin - remove unnecessary params
			unset( $_POST['_wpnonce']);
			unset( $_POST['_wp_http_referer']);
			unset( $_POST['action']);
			unset( $_POST['submit']);
			// end - remove unnecessary params

			$_POST = stripslashes_deep($_POST);

			$n_shortener_id = isset( $_POST['shortener_id'] ) ? intval( esc_sql( sanitize_text_field( $_POST['shortener_id'] ) ) ) : 0;
			if( ! $n_shortener_id ) {
				wp_die( __( 'msg_error_invalid_shortener_id', UTM_SHORTENER_DOMAIN ) );
			}

			global $a_utm_source_info;
			global $a_utm_medium_info;
			global $a_influencer_type;

			$n_utm_source = isset( $_POST['utm_source'] ) ? esc_sql( sanitize_text_field( $_POST['utm_source'] ) ) : 0;
			if( ! isset( $a_utm_source_info[ $n_utm_source ] ) ) {
				wp_die( __( 'msg_error_invalid_utm_source', UTM_SHORTENER_DOMAIN ) );
			}
			$n_utm_medium = isset( $_POST['utm_medium'] ) ? esc_sql( sanitize_text_field( $_POST['utm_medium'] ) ) : 0;
			if( ! isset( $a_utm_medium_info[ $n_utm_medium ] ) ) {
				wp_die( __( 'msg_error_invalid_utm_medium', UTM_SHORTENER_DOMAIN ) );
			}
			
			$n_influencer_type = isset( $_POST['influencer_type']) ? esc_sql( sanitize_text_field($_POST['influencer_type'])) : 0;
			if( ! isset( $a_influencer_type[ $n_influencer_type ] ) ) {
				wp_die( __( 'msg_error_invalid_influencer_type', UTM_SHORTENER_DOMAIN ) );
			}

			foreach( array( 'allocation_start_date', 'allocation_end_date' ) as $s_param_title ) {
				$_POST[$s_param_title] = str_replace( "-", "", $_POST[$s_param_title] );
				$b_valid = $this->_validate_date_actual( $_POST[$s_param_title] );
				if(!$b_valid) {
					wp_die( __( 'msg_error_invalid_date_info', UTM_SHORTENER_DOMAIN ) );
				}
			}
			
			$update = array(
				'data' => array ( 
					'utm_source'            => $n_utm_source,
					'utm_medium'            => $n_utm_medium,
					'utm_term'              => isset( $_POST['utm_term'] ) ? esc_sql( sanitize_text_field( $_POST['utm_term'] ) ) : '',
					'utm_content'           => isset( $_POST['utm_content'] ) ? esc_sql( sanitize_text_field( $_POST['utm_content'] ) ) : '',
					'utm_campaign_id'       => isset( $_POST['utm_campaign_id']) ? esc_sql( sanitize_text_field($_POST['utm_campaign_id'])) : '',
					'influencer_type'       => $n_influencer_type,
					'influencer_id'         => isset( $_POST['influencer_id'] ) ? esc_sql( sanitize_text_field( $_POST['influencer_id'] ) ) : '',
					'fixed_cost_incl_vat'   => isset($_POST['fixed_cost_incl_vat']) ? esc_sql(sanitize_text_field($_POST['fixed_cost_incl_vat'])) : 0,
					'memo'                  => isset($_POST['memo']) ? esc_sql(sanitize_text_field($_POST['memo'])) : '',
					'allocation_start_date' => $_POST['allocation_start_date'],
					'allocation_end_date'   => $_POST['allocation_end_date'],
					'update_date'           => current_time('mysql')
				),
				'where' => array ( 'seq' => $n_shortener_id ),
			);
			global $wpdb;
			$wpdb->update ( "{$wpdb->prefix}sv_utm_shortener", $update['data'], $update['where'] );
			wp_redirect( admin_url( 'admin.php?page=' . UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE . '&shortener_id=' . $n_shortener_id ) );
			exit;
		}

		/**
		 * @brief insert shortener
		 **/
		public function proc_insert() {
			check_admin_referer( UTM_SHORTENER_CMD_ADMIN_PROC_INSERT );  // check nounce

			// begin - remove unnecessary params
			unset( $_POST['_wpnonce']);
			unset( $_POST['_wp_http_referer']);
			unset( $_POST['action']);
			unset( $_POST['submit']);
			// end - remove unnecessary params

			$_POST = stripslashes_deep($_POST);

			// insert shortener
			$s_service_type = '';
			
			global $a_utm_source_info;
				// 1 => 'naver', 2 => 'google', 3 => 'youtube', 
				// 4 => 'facebook', 5 => 'instagram', 6 => 'kakao', 7 => 'mobon' 
			global $a_utm_medium_info;
				// 1 => 'cpc', 2 => 'display', 3 => 'referal'
			global $a_influencer_type;
				// 1 => __( 'reviewer', UTM_SHORTENER_DOMAIN ), // 블로거 or 인스타 인플루언서
				// 2 => __( 'top_ranker', UTM_SHORTENER_DOMAIN ), // 상위노출
				// 3 => __( 'cafe_penetration', UTM_SHORTENER_DOMAIN ), // 카페활동
				// 4 => __( 'kin_penetration', UTM_SHORTENER_DOMAIN ), // 지식인활동

			// insert utm_shortener
			$n_utm_source = isset( $_POST['utm_source'] ) ? esc_sql( sanitize_text_field( $_POST['utm_source'] ) ) : 0;
			if( ! isset( $a_utm_source_info[ $n_utm_source ] ) ) {
				wp_die( __( 'msg_error_invalid_utm_source', UTM_SHORTENER_DOMAIN ) );
			}
			$s_service_type .= substr( $a_utm_source_info[ $n_utm_source ], 0, 2 );

			$n_utm_medium = isset( $_POST['utm_medium'] ) ? esc_sql( sanitize_text_field( $_POST['utm_medium'] ) ) : 0;
			if( ! isset( $a_utm_medium_info[ $n_utm_medium ] ) ) {
				wp_die( __( 'msg_error_invalid_utm_medium', UTM_SHORTENER_DOMAIN ) );
			}
			$s_service_type .= substr( $a_utm_medium_info[ $n_utm_medium ], 0, 2 );
			
			$n_influencer_type = isset( $_POST['influencer_type']) ? esc_sql( sanitize_text_field($_POST['influencer_type'])) : 0;
			if( ! isset( $a_influencer_type[ $n_influencer_type ] ) ) {
				wp_die( __( 'msg_error_invalid_influencer_type', UTM_SHORTENER_DOMAIN ) );
			}

			switch( $n_influencer_type ) {
				case 1:  // reviewer
					$s_service_type .= 'rv';
					break;
				case 2:  // top_ranker
					$s_service_type .= 'tr';
					break;
				case 3:  // penetration
					$s_service_type .= 'pt';
					break;
				default:  // paid search
					$s_service_type .= 'ps';
			}

			foreach( array( 'allocation_start_date', 'allocation_end_date' ) as $s_param_title ) {
				$o_rst = $this->_get_allocation_date_type( $_POST[$s_param_title] );
				if( ! $o_rst->bool ) {
					wp_die( __( 'msg_error_invalid_date_info', UTM_SHORTENER_DOMAIN ) );
				}
					
				$s_format_type = $o_rst->s_format_type;
				unset( $o_rst );
				if( $s_format_type == 'yyyymmdd' ) {
					$b_valid = $this->_validate_date_actual( $_POST[$s_param_title] );
				}
				elseif( $s_format_type == 'increment' ) {
					$_POST[$s_param_title] = str_replace( "d", " days", $_POST[$s_param_title] );
					$_POST[$s_param_title] = str_replace( "w", " week", $_POST[$s_param_title] );

					if( $s_param_title == 'allocation_start_date' ) {
						$o_timestamp = strtotime( $_POST[$s_param_title] );
					}
					elseif( $s_param_title == 'allocation_end_date' ) {
						$o_timestamp = strtotime( $_POST['allocation_start_date'].' '.$_POST[$s_param_title] );
					}

					$s_yyyymmdd = date( "Ymd", $o_timestamp );
					$b_valid = $this->_validate_date_actual( $s_yyyymmdd );
				}
				if(!$b_valid) {
					wp_die( __( 'msg_error_invalid_date_info', UTM_SHORTENER_DOMAIN ) );
				}
				else {
					$_POST[$s_param_title] = $s_yyyymmdd;
				}
			}

			if( (int)date( "Ymd" ) > (int)$_POST['allocation_start_date'] ) {
				wp_die( __( 'msg_error_invalid_date_info', UTM_SHORTENER_DOMAIN ) );
			}
			if( (int)$_POST['allocation_start_date'] > (int)$_POST['allocation_end_date'] ) {
				wp_die( __( 'msg_error_invalid_date_info', UTM_SHORTENER_DOMAIN ) );
			}

			$n_fixed_cost_incl_vat = isset($_POST['fixed_cost_incl_vat']) ? esc_sql(sanitize_text_field($_POST['fixed_cost_incl_vat'])) : 0;
			$s_memo                = isset($_POST['memo']) ? esc_sql(sanitize_text_field($_POST['memo'])) : '';
			$s_utm_content         = isset( $_POST['utm_content'] ) ? esc_sql( sanitize_text_field( $_POST['utm_content'] ) ) : '';
			$s_utm_campaign_id     = isset( $_POST['utm_campaign_id']) ? esc_sql( sanitize_text_field($_POST['utm_campaign_id'])) : '';

			global $wpdb;
			$o_cur_user = wp_get_current_user();
			if( isset( $_POST['utm_term_influencer_id'] ) ) {

				$a_line = explode( "\n", str_replace( "\r", "", $_POST['utm_term_influencer_id'] ) );
				foreach( $a_line as $s_val ) {
					$s_val        = str_replace( "\t", ";", $s_val );
					$a_Val        = explode( ';', $s_val );
					$s_utm_term   =  str_replace( ' ', '', $a_Val[0] );
					$s_utm_term   = trim( preg_replace( '/\s+/', '', $s_utm_term ) );
					$s_blogger_id = str_replace( ' ', '', $a_Val[1] );
					$s_blogger_id = trim( preg_replace( '/\s+/', '', $s_blogger_id ) );
					unset( $a_Val );
		
					if( strlen( $s_utm_term ) == 0 ) {
						wp_die( __( 'msg_error_invalid_utm_term', UTM_SHORTENER_DOMAIN ) );
					}
					
					$n_cur_max_seq = $this->_get_max_seq();
					$s_shorten_uri_value = $s_service_type . ++$n_cur_max_seq;

					if( $this->_is_duplicated_uri_value( $s_shorten_uri_value ) ) {
						wp_die( __( 'msg_error_existing_shorten_uri_value', UTM_SHORTENER_DOMAIN ) );
					}
					
					$s_current_time = current_time('mysql');
					$wpdb->insert(
						"{$wpdb->prefix}sv_utm_shortener",
						array(
							'shorten_uri_value'     => $s_shorten_uri_value,
							'author'                => $o_cur_user->ID,
							'utm_source'            => $n_utm_source,
							'utm_medium'            => $n_utm_medium,
							'utm_term'              => esc_sql( $s_utm_term ),
							'utm_content'           => $s_utm_content,
							'utm_campaign_id'       => $s_utm_campaign_id,
							'influencer_type'       => $n_influencer_type,
							'influencer_id'         => esc_sql( $s_blogger_id ),
							'fixed_cost_incl_vat'   => $n_fixed_cost_incl_vat,
							'memo'                  => $s_memo,
							'allocation_start_date' => $_POST['allocation_start_date'],
							'allocation_end_date'   => $_POST['allocation_end_date'],
							'update_date'           => $s_current_time,
							'regdate'               => $s_current_time
						),
						array( '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s' )
					);
				}
				unset( $a_line );
			}			
			unset( $o_cur_user );
				
			if( isset( $_POST['utm_term_influencer_id'] ) ) {  // bulk registration case
				wp_redirect( admin_url( 'admin.php?page='.UTM_SHORTENER_CMD_ADMIN_VIEW_LIST ) );
			}
			else {  // single registration case
				wp_redirect( admin_url( 'admin.php?page=' . UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE . '&shortener_id=' . $wpdb->insert_id ) );
			}	
			exit;
		}

		/**
		 *
		 */
		private function _get_max_seq() {
			global $wpdb;
			return intval( $wpdb->get_var( "select max(seq) from {$wpdb->prefix}sv_utm_shortener" ) );
		}

		/**
		 *
		 */
		private function _is_duplicated_uri_value( $s_value ) {
			global $wpdb;
			$n_count = intval( $wpdb->get_var( "select count(*) from {$wpdb->prefix}sv_utm_shortener WHERE `shorten_uri_value`= '{$s_value}'" ) );
			if( $n_count == 0 ) {  // 최초 입력시 ++인덱스가 0이 되도록
				return false;
			}
			return true;
		}

		/**
		 * @brief 비용 배분 날짜 형식 판단
		 */
		private function _get_allocation_date_type( $s_date ) {
			$a_date_format = ['yyyymmdd'=>'/^(19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[0-1])$/', 'increment'=>'/^[+][^0]?[1-9]+[0-9]*[dw]$/'];
			foreach( $a_date_format as $s_format_type => $s_regex ) {
				$a_matches = array();
				preg_match($s_regex, $s_date, $a_matches, PREG_OFFSET_CAPTURE, 0);
				if( array_key_exists( 0, $a_matches) )
					break;
				$a_matches = null;
			}

			$o_rst = new \stdClass();
			$o_rst->bool = false;
			$o_rst->s_format_type = null;
			if( $a_matches ) {
				$o_rst->bool = true;
				$o_rst->s_format_type = $s_format_type;
			}
			return $o_rst;
		}

		/**
		 * @brief yyyymmdd가 실제 날짜인지 검사
		 */	
		private function _validate_date_actual( $date, $format = 'Ymd' ) {
			$d = \DateTime::createFromFormat( $format, $date );
			// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
			return $d && $d->format($format) === $date;
		}
	}
}