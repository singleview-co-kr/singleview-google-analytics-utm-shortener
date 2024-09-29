<?php
/**
 * Register settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @package singleview-google-analytics-utm-shortener
 */
namespace SV_Utm_Url_Shortener\Includes\Admin\Tpl;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * load settings function
 *
 * @return $a_board_settings
 * return setting title together
 */
function load_settings( $shortener_id ) {
	$o_rst                                = new \stdClass();
	$o_rst->b_ok                          = false;
	$o_rst->a_shortener_settings          = null;

	$n_shortener_id = intval( sanitize_text_field( $shortener_id ) );
	if ( $n_shortener_id == 0 ) {
		return $o_rst;
	}

	$a_shortener_settings = null;
	global $wpdb;
	$n_shortener_id   = esc_sql( $n_shortener_id );
	$o_shortener_info = $wpdb->get_row( "SELECT * FROM `{$wpdb->prefix}sv_utm_shortener` WHERE `seq`='$n_shortener_id'" );
	
	$o_rst->b_ok             = true;
	$o_rst->a_shortener_info = (array)$o_shortener_info;
	return $o_rst;
}

/**
 * Register settings function
 *
 * @return void
 */
function register_settings() {

	// First, we write the options collection.
	global $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING;

	if ( isset( $_GET['shortener_id'] ) ) {  // update shortener configuration
		$o_rst = load_settings( $_GET['shortener_id'] );
		if ( false === $o_rst->b_ok ) { // for creating a new board
			$A_UTM_SHORTENER_ADMIN_SINGLE_SETTING = get_settings_defaults();
		} else {  // update a old shortener
			$A_UTM_SHORTENER_ADMIN_SINGLE_SETTING = $o_rst->a_shortener_info;
		}
	} else {  // create new shortener
		$_GET['shortener_id']           = null;  // prevent PHP Notice:  Undefined index: board_id
		$A_UTM_SHORTENER_ADMIN_SINGLE_SETTING = get_settings_defaults();
	}
	unset( $o_rst );

	// will be executed in \includes\admin\tpl\settings-page.php::options_page()
	foreach ( get_registered_settings() as $section => $settings ) {
		add_settings_section(
			UTM_SHORTENER_DOMAIN . '_settings_' . $section, // ID used to identify this section and with which to register options
			__return_empty_string(), // No title, we will handle this via a separate function.
			'__return_false', // No callback function needed. We'll process this separately.
			UTM_SHORTENER_DOMAIN . '_settings_' . $section  // Page on which these options will be added.
		);

		foreach ( $settings as $setting ) {
			$args = wp_parse_args(
				$setting,
				array(
					'section'          => $section,
					'id'               => null,
					'name'             => '',
					'desc'             => '',
					'type'             => null,
					'options'          => '',
					'max'              => null,
					'min'              => null,
					'step'             => null,
					'size'             => null,
					'field_class'      => '',
					'field_attributes' => '',
					'placeholder'      => '',
				)
			);
			add_settings_field(
				UTM_SHORTENER_DOMAIN . '_settings[' . $args['id'] . ']', // ID of the settings field. We save it within the settings array.
				$args['name'],     // Label of the setting.
				function_exists( '\SV_Utm_Url_Shortener\Includes\Admin\Tpl\render_' . $args['type'] . '_callback' ) ?
								'\SV_Utm_Url_Shortener\Includes\Admin\Tpl\render_' . $args['type'] . '_callback' :
								'\SV_Utm_Url_Shortener\Includes\Admin\Tpl\render_missing_callback', // Function to handle the setting.
								UTM_SHORTENER_DOMAIN . '_settings_' . $section,    // Page to display the setting. In our case it is the section as defined above.
								UTM_SHORTENER_DOMAIN . '_settings_' . $section,    // Name of the section.
				$args
			);
		}
	}
	// Register the settings into the options table.
	register_setting(
		UTM_SHORTENER_DOMAIN . '_settings',
		UTM_SHORTENER_DOMAIN . '_settings',
		array(
			'sanitize_callback' => UTM_SHORTENER_DOMAIN . '_settings_sanitize',
		)
	);
}


/**
 * Default settings.
 *
 * @return array Default settings
 */
function get_settings_defaults() {
	$options = array();

	// Populate some default values.
	foreach ( get_registered_settings() as $tab => $settings ) {

		foreach ( $settings as $option ) {

			// When checkbox is set to true, set this to 1.
			if ( 'checkbox' === $option['type'] && ! empty( $option['options'] ) ) {
				$options[ $option['id'] ] = 1;
			} else {
				$options[ $option['id'] ] = '';
			}
			// If an option is set.
			// 'csv', 'numbercsv', 'posttypes', 'css',
			if ( in_array( $option['type'], array( 'textarea', 'text', 'number' ), true ) && isset( $option['options'] ) ) {
				$options[ $option['id'] ] = $option['options'];
			}
			// , 'radiodesc', 'thumbsizes'
			if ( in_array( $option['type'], array( 'select' ), true ) && isset( $option['default'] ) ) {
				$options[ $option['id'] ] = $option['default'];
			}
		}
	}

	/**
	 * Filters the default settings array.
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_settings_defaults', $options );
}


/**
 * Get the default option for a specific key
 *
 * @param string $key Key of the option to fetch.
 * @return mixed
 */
function get_default_option( $key = '' ) {
	$default_settings = get_settings_defaults();
	if ( array_key_exists( $key, $default_settings ) ) {
		return $default_settings[ $key ];
	} else {
		return false;
	}
}
