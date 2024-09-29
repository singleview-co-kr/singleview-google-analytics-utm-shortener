<?php
/**
 * Default settings.
 *
 * Functions to register the default settings of the plugin.
 *
 * @link https://singleview.co.kr
 *
 * @package singleview-google-analytics-utm-shortener
 */

namespace SV_Utm_Url_Shortener\Includes\Admin\Tpl;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Retrieve the array of plugin settings
 *
 * @return array Settings array
 */
function get_registered_settings() {

	$a_utm_shortener_settings = array(
		'basic' => settings_basic(),
	);

	/**
	 * Filters the settings array
	 *
	 * @param array   $a_utm_shortener_settings Settings array
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_get_registered_settings', $a_utm_shortener_settings );
}

/**
 * Retrieve the array of basic settings
 *
 * @return array basic settings array
 */
function settings_basic() {
	$a_settings = array(
		'utm_source'  => array(
			'id'      => 'utm_source',
			'name'    => __( 'lbl_utm_source', UTM_SHORTENER_DOMAIN ),
			'desc'    => __( 'desc_utm_source', UTM_SHORTENER_DOMAIN ),
			'type'    => 'select',
			'options' => get_sources(),
			'default' => '',
		),
		'utm_medium'  => array(
			'id'      => 'utm_medium',
			'name'    => __( 'lbl_utm_medium', UTM_SHORTENER_DOMAIN ),
			'desc'    => __( 'desc_utm_medium', UTM_SHORTENER_DOMAIN ),
			'type'    => 'select',
			'options' => get_medium(),
		),
		
		'utm_content' => array(
			'id'      => 'utm_content',
			'name'    => __( 'lbl_utm_content', UTM_SHORTENER_DOMAIN ),
			'desc'    => __( 'desc_utm_content', UTM_SHORTENER_DOMAIN ),
			'type'    => 'text',
		),
		'utm_campaign_id' => array(
			'id'   => 'utm_campaign_id',
			'name' => __( 'lbl_utm_campaign_id', UTM_SHORTENER_DOMAIN ),
			'desc' => __( 'desc_utm_campaign_id', UTM_SHORTENER_DOMAIN ),
			'type' => 'text',
		),
		'influencer_type' => array(
			'id'      => 'influencer_type',
			'name'    => __( 'lbl_influencer_type', UTM_SHORTENER_DOMAIN ),
			'desc'    => __( 'desc_influencer_type', UTM_SHORTENER_DOMAIN ),
			'type'    => 'select',
			'options' => get_influencer_type(),
		),
	);

	if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT ) {
		$a_settings['utm_term_influencer_id'] = array(
			'id'   => 'utm_term_influencer_id',
			'name' => __( 'lbl_utm_term_influencer_id', UTM_SHORTENER_DOMAIN ),
			'desc' => __( 'desc_utm_term_influencer_id', UTM_SHORTENER_DOMAIN ),
			'type' => 'textarea',
		);
	}
	elseif ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE ) {
		$a_settings['utm_term'] = array(
			'id'   => 'utm_term',
			'name' => __( 'lbl_utm_term', UTM_SHORTENER_DOMAIN ),
			'desc' => __( 'desc_utm_term', UTM_SHORTENER_DOMAIN ),
			'type' => 'text',
		);
		$a_settings['influencer_id'] = array(
			'id'   => 'influencer_id',
			'name' => __( 'lbl_influencer_id', UTM_SHORTENER_DOMAIN ),
			'desc' => __( 'desc_influencer_id', UTM_SHORTENER_DOMAIN ),
			'type' => 'text',
		);
	}

	$a_settings['fixed_cost_incl_vat'] = array(
		'id'   => 'fixed_cost_incl_vat',
		'name' => __( 'lbl_fixed_cost_incl_vat', UTM_SHORTENER_DOMAIN ),
		'desc' => __( 'desc_fixed_cost_incl_vat', UTM_SHORTENER_DOMAIN ),
		'type' => 'number',
		'min'  => 100000,
		'max'  => 20000000
	);
	$a_settings['memo']	= array(
		'id'   => 'memo',
		'name' => __( 'lbl_memo', UTM_SHORTENER_DOMAIN ),
		'desc' => __( 'desc_memo', UTM_SHORTENER_DOMAIN ),
		'type' => 'textarea',
	);
	$a_settings['allocation_start_date'] = array(
		'id'      => 'allocation_start_date',
		'name'    => __( 'lbl_allocation_start_date', UTM_SHORTENER_DOMAIN ),
		'desc'    => __( 'desc_allocation_start_date', UTM_SHORTENER_DOMAIN ),
		'type'    => 'text',
		'options' => '+2w',
	);
	$a_settings['allocation_end_date'] = array(
		'id'   => 'allocation_end_date',
		'name' => __( 'lbl_allocation_end_date', UTM_SHORTENER_DOMAIN ),
		'desc' => __( 'desc_allocation_end_date', UTM_SHORTENER_DOMAIN ),
		'type' => 'text',
		'options' => '+4w',
	);

	/**
	 * Filters the General settings array
	 *
	 * @param array $settings General settings array
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . 'settings_basic', $a_settings );
}


/**
 * Get the various utm sources.
 *
 * @return array utm sources options.
 */
function get_sources() {
	global $a_utm_source_info;

	/**
	 * Filter the array containing the utm_sources to add your own.
	 *
	 * @param array $utm_sources Different utm_sources.
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_get_source', $a_utm_source_info );
}

/**
 * Get the various utm medium.
 *
 * @return array utm medium options.
 */
function get_medium() {
	global $a_utm_medium_info;

	/**
	 * Filter the array containing the utm_medium to add your own.
	 *
	 * @param array $utm_medium Different utm_medium.
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_get_medium', $a_utm_medium_info );
}

/**
 * Get the effective a_influencer_type types.
 *
 * @return array effective a_influencer_type types.
 */
function get_influencer_type() {
	global $a_influencer_type;

	/**
	 * Filter the array containing the influencer types to add your own.
	 *
	 * @param array $influencer_types Different influencer_types.
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_get_influencer_type', $a_influencer_type );
}
