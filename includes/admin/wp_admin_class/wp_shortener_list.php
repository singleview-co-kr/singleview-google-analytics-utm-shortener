<?php
/* Copyright (C) singleview.co.kr <https://singleview.co.kr> */

/**
 * @class  WpShortenerList
 * @author singleview.co.kr
 * @brief  shortener module admin tpl
 **/
namespace SV_Utm_Url_Shortener\Includes\Modules\Shortener\WpAdminClass;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\SV_Utm_Url_Shortener\\Includes\\Modules\\Shortener\\WpAdminClass\\WpShortenerList')) {

	class WpShortenerList extends \WP_List_Table {
		private $_n_list_per_page = 20;
		public $items = null;  // list to display by WP_List_Table

		public function __construct(){
			parent::__construct();
			// https://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
			// https://supporthost.com/wp-list-table-tutorial/
			$this->prepare_shortener_list();
		}

		/**
		 * @brief 
		 **/
		public function prepare_shortener_list(){
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = array();
			$this->_column_headers = array($columns, $hidden, $sortable);
			
			$keyword = isset($_GET['s'])?esc_attr($_GET['s']):'';
			
			$cur_page = $this->get_pagenum();
			global $wpdb;
			if($keyword){
				$keyword = esc_sql($keyword);
				$where = "WHERE `utm_source` LIKE '%{$keyword}%'";
			}
			else {
				$where = null;
			}
			$n_total = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}sv_utm_shortener` {$where}");
			$this->items = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}sv_utm_shortener` {$where} ORDER BY `seq` DESC LIMIT " . ($cur_page-1)*$this->_n_list_per_page . ",{$this->_n_list_per_page}");
			
			$this->set_pagination_args(array('total_items'=>$n_total, 'per_page'=>$this->_n_list_per_page));
		}

		/**
		 * @brief 
		 **/
		public function get_columns(){
			return array(
				//'cb' => '<input type="checkbox">',
				'shorten_uri_value' => __('lbl_shorten_uri', UTM_SHORTENER_DOMAIN),
				'utm_info' => __('lbl_utm_info', UTM_SHORTENER_DOMAIN),
				'click' => __('lbl_click_cnt', UTM_SHORTENER_DOMAIN),
				'fixed_cost_incl_vat' => __('lbl_fixed_cost_incl_vat', UTM_SHORTENER_DOMAIN),
				'allocation_period' => __('lbl_allocation_period', UTM_SHORTENER_DOMAIN),
				'regdate' => __('lbl_regdate', UTM_SHORTENER_DOMAIN),
			);
		}
		
		/**
		 * @brief 
		 **/
		protected function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'shorten_uri_value':
					return '<A HREF="'. get_site_url() . '/?'. get_option( UTM_SHORTENER_DOMAIN . '_shortener_query' ) .'=' . $item->$column_name . '" target="_new">' . $item->$column_name . '</A>';
				case 'utm_info':
					global $a_utm_source_mapper;
    				global $a_utm_medium_mapper;
					global $a_influencer_type;

					$s_shortener_desc = $a_utm_source_mapper[$item->utm_source] . ' ' . $a_utm_medium_mapper[$item->utm_medium] . ' ' . $item->utm_content . ' ' . $item->utm_term . ' ' . $item->utm_campaign_id . ' ' . $a_influencer_type[$item->influencer_type] . ' ' . $item->influencer_id;
					return '<A HREF=' . admin_url( 'admin.php?page=' . UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE . '&shortener_id=' . $item->seq ) . '>' . $s_shortener_desc . '</A>';
				case 'fixed_cost_incl_vat':
					return number_format( $item->$column_name );
				case 'allocation_period':
					return $item->allocation_start_date . '~' . $item->allocation_end_date;
				case 'regdate':
					return $item->$column_name;
				default:
					return $item->$column_name;
			}
		}
	} // END CLASS
}