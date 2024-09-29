<?php
/**
 * Renders the settings page.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
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
 * Render the settings page.
 *
 * @return void
 */
function render_options_page() {
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( sanitize_key( wp_unslash( $_GET['tab'] ) ), get_settings_sections() ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'basic'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	
	ob_start();
	?>
	<div class="wrap">
		<?php include 'header.php'; ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">

					<ul class="nav-tab-wrapper" style="padding:0">
						<?php
						foreach ( get_settings_sections() as $tab_id => $tab_name ) {

							$active = $active_tab === $tab_id ? ' ' : '';

							echo '<li style="margin:0;"><a href="#' . esc_attr( $tab_id ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab ' . sanitize_html_class( $active ) . '">';
								echo esc_html( $tab_name );
							echo '</a></li>';

						}
						?>
					</ul>

					<form id="utm-shortener-setting-form" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
			<?php
			if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT ) {
				$s_action = UTM_SHORTENER_CMD_ADMIN_PROC_INSERT;
			}
			if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE ) {
				$s_action = UTM_SHORTENER_CMD_ADMIN_PROC_UPDATE;
			}
			wp_nonce_field( $s_action );
			?>
						<input type="hidden" name="action" value="<?php echo $s_action; ?>">
						<input type="hidden" name="shortener_id" value="<?php echo $_GET['shortener_id']; ?>">

						<?php foreach ( get_settings_sections() as $tab_id => $tab_name ) : ?>

						<div id="<?php echo esc_attr( $tab_id ); ?>">
							<table class="form-table">
							<?php
								// call section and fields that is registered from \includes\admin\tpl\register-settings.php::register_settings()
								do_settings_fields( UTM_SHORTENER_DOMAIN . '_settings_' . $tab_id, UTM_SHORTENER_DOMAIN . '_settings_' . $tab_id );
							?>
							</table>
							<p>
							<?php
								// Default submit button.
								submit_button(
									__( 'cmd_save_change', UTM_SHORTENER_DOMAIN ),
									'primary',
									'submit',
									false
								);
							?>
							</p>
						</div><!-- /#tab_id-->

						<?php endforeach; ?>

					</form>

				</div><!-- /#post-body-content -->

				<div id="postbox-container-1" class="postbox-container">

					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php include_once UTM_SHORTENER_PATH . 'includes'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'sidebar.php'; ?>
					</div><!-- /#side-sortables -->

				</div><!-- /#postbox-container-1 -->
			</div><!-- /#post-body -->
			<br class="clear" />
		</div><!-- /#poststuff -->

	</div><!-- /.wrap -->

	<?php
	echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Array containing the settings' sections.
 *
 * @return array Settings array
 */
function get_settings_sections() {
	if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT ) {
		$settings_sections = array(
			'basic' => __( 'lbl_bulk_insert', UTM_SHORTENER_DOMAIN ),
		);
	}
	if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE ) {
		$settings_sections = array(
			'basic' => __( 'lbl_single_update', UTM_SHORTENER_DOMAIN ),
		);
	}

	/**
	 * Filter the array containing the settings' sections.
	 *
	 * @param array $settings_sections Settings array
	 */
	return apply_filters( UTM_SHORTENER_DOMAIN . '_settings_sections', $settings_sections );
}


/**
 * Miscellaneous callback funcion
 *
 * @param array $args Arguments passed by the setting.
 * @return void
 */
function utm_shortener_missing_callback( $args ) {
	/* translators: %s: Setting ID. */
	printf( 'The callback function used for the <strong>%s</strong> setting is missing.', esc_html( $args['id'] ) );
}



/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @param array $args Array of arguments.
 * @return void
 */
function render_select_callback( $args ) {
	global $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING;

	if ( isset( $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ] ) ) {
		$value = $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ];
	} else {
		$value = isset( $args['default'] ) ? $args['default'] : '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="crp-chosen"';
	} else {
		$chosen = '';
	}

	$html = sprintf( '<select id="%1$s" name="%1$s" %2$s />', esc_attr( $args['id'] ), $chosen );
	$html .= sprintf( '<option value="" %1$s>%2$s</option>', selected( '', $value, false ), __( 'lbl_plz_select', UTM_SHORTENER_DOMAIN ) );

	foreach ( $args['options'] as $option => $name ) {
		$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option ), selected( $option, $value, false ), $name );
	}

	$html .= '</select>';
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( UTM_SHORTENER_DOMAIN . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display text fields.
 *
 * @param array $args Array of arguments.
 * @return void
 */
function render_text_callback( $args ) {

	// First, we read the options collection.
	global $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING;
	if ( isset( $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ] ) ) {
		$value = $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$size = sanitize_html_class( isset( $args['size'] ) ? $args['size'] : 'regular' );

	$class = sanitize_html_class( $args['field_class'] );

	$disabled = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
	$readonly = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';

	$attributes = $disabled . $readonly;

	foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
		$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
	}

	$html  = sprintf( '<input type="text" id="%1$s" name="%1$s" class="%2$s" value="%3$s" %4$s />', sanitize_key( $args['id'] ), $class . ' ' . $size . '-text', esc_attr( stripslashes( $value ) ), $attributes );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( UTM_SHORTENER_DOMAIN . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Display textarea.
 *
 * @param array $args Array of arguments.
 * @return void
 */
function render_textarea_callback( $args ) {

	// First, we read the options collection.
	global $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING;
// error_log(print_r($args, true));
// error_log(print_r($A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ], true));

	if ( isset( $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ] ) ) {
		$value = $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$class = sanitize_html_class( $args['field_class'] );

	$html  = sprintf( '<textarea class="%3$s" cols="50" rows="4" id="%1$s" name="%1$s">%2$s</textarea>', sanitize_key( $args['id'] ), esc_textarea( stripslashes( $value ) ), 'large-text ' . $class );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( UTM_SHORTENER_DOMAIN . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @param array $args Array of arguments.
 * @return void
 */
function render_number_callback( $args ) {
	global $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING;

	if ( isset( $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ] ) ) {
		$value = $A_UTM_SHORTENER_ADMIN_SINGLE_SETTING[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;
	$size = isset( $args['size'] ) ? $args['size'] : 'regular';

	$html  = sprintf( '<input type="number" step="%1$s" max="%2$s" min="%3$s" class="%4$s" id="%5$s" name="%5$s" value="%6$s"/>', esc_attr( $step ), esc_attr( $max ), esc_attr( $min ), sanitize_html_class( $size ) . '-text', sanitize_key( $args['id'] ), esc_attr( stripslashes( $value ) ) );
	$html .= '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>';

	/** This filter has been defined in settings-page.php */
	echo apply_filters( UTM_SHORTENER_DOMAIN . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
