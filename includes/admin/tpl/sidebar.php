<?php
/**
 * Sidebar
 *
 * @link  https://singleview.co.kr
 *
 * @package    singleview-google-analytics-utm-shortener
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div class="postbox-container">
	<!--- div id="donatediv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php echo __( 'cmd_support_singleview', UTM_SHORTENER_DOMAIN ); ?></span></h3>
			<div class="inside" style="text-align: center">
				<div id="donate-form">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="business" value="donate@singleview.co.kr">
						<input type="hidden" name="lc" value="IN">
						<input type="hidden" name="item_name" value="<?php echo __( 'lbl_donate_for_utm_shortener', UTM_SHORTENER_DOMAIN ); ?>">
						<input type="hidden" name="item_number" value="utm_shortener_plugin_settings">
						<strong><?php echo __( 'cmd_enter_amount_in_usd', UTM_SHORTENER_DOMAIN ); ?></strong>: <input name="amount" value="15.00" size="6" type="text"><br />
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="button_subtype" value="services">
						<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
						<input type="image" src="<?php echo esc_url( UTM_SHORTENER_URL . 'includes/admin/images/paypal_donate_button.gif' ); ?>" border="0" name="submit" alt="<?php echo __( 'cmd_support_utm_shortener', UTM_SHORTENER_DOMAIN ); ?>">
						<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
	</div --->
	<!-- /.postbox -->

	<div id="qlinksdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php echo __( 'lbl_quick_links', UTM_SHORTENER_DOMAIN ); ?></span></h3>
			<div class="inside">
				<div id="quick-links">
					<ul>
						<li>
							<a href="https://singleview.co.kr/plugins/utm_shortener/">
								<?php echo __( 'lbl_utm_shortener_plugin_homepage', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://wordpress.org/plugins/utm_shortener/faq/">
								<?php echo __( 'lbl_wp_faq', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="http://wordpress.org/support/plugin/utm_shortener">
								<?php echo __( 'lbl_wp_support', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://wordpress.org/support/view/plugin-reviews/utm_shortener">
								<?php echo __( 'lbl_wp_reviews', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li>
						<li>
							<a href="https://github.com/">
								<?php echo __( 'lbl_github', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li>
						<!-- <li>
							<a href="https://singleview.co.kr/plugins/">
								<?php // echo __( 'Other plugins', UTM_SHORTENER_DOMAIN ); ?>
							</a>
						</li> -->
					</ul>
				</div>
			</div>
			<!-- /.inside -->
	</div>
	<!-- /.postbox -->
</div>
