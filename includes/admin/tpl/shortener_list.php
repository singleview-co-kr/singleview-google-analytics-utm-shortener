<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="wrap">
	<div class="singleview-header-logo"></div>
	<h1 class="wp-heading-inline"><?php echo UTM_SHORTENER_DOMAIN; ?> : <?php echo __( 'cmd_shortener_list', UTM_SHORTENER_DOMAIN ); ?></h1>
	
<?php if ( isset( $s_insert_shortener_url ) ) : ?>
	<a href="<?php echo $s_insert_shortener_url; ?>" class="page-title-action"> <?php echo __( 'cmd_insert', UTM_SHORTENER_DOMAIN ); ?></a>
<?php endif ?>
	<hr class="wp-header-end">
	
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<?php if( ! $b_shortener_query_designated )	: ?>
			<form id="utm-shortener-setting-form" action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="<?php echo UTM_SHORTENER_CMD_ADMIN_PROC_DECIDE_SHORTENER_QRY; ?>">
				<?php wp_nonce_field( UTM_SHORTENER_CMD_ADMIN_PROC_DECIDE_SHORTENER_QRY ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php echo __( 'lbl_shortener_query', UTM_SHORTENER_DOMAIN ) ?></th>
							<td>
								<input type="text" id="utm_shortener_query" name="utm_shortener_query" class=" regular-text" value="" 0="">
								<p class="description"><?php echo __( 'desc_shortener_query', UTM_SHORTENER_DOMAIN ) ?> </p>
							</td>
						</tr>
					</tbody>
				</table>
				<p>
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'cmd_save_change', UTM_SHORTENER_DOMAIN ) ?>">
				</p>
			</form>
			<?php endif; ?>
			<div id="post-body-content">
				<form method="get">
					<input type="hidden" name="page" value="<?php echo UTM_SHORTENER_CMD_ADMIN_VIEW_LIST ?>">
					<?php $o_shortener_list->search_box( __( 'lbl_search', UTM_SHORTENER_DOMAIN ), 'utm_shortener_list_search' ); ?>
				</form>
				<form method="post">
					<?php $o_shortener_list->display(); ?>
				</form>
			</div><!-- /#post-body-content -->
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php include_once UTM_SHORTENER_PATH . 'includes'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'sidebar.php'; ?>
				</div><!-- /#side-sortables -->
			</div><!-- /#postbox-container-1 -->
		</div><!-- /#post-body -->
	</div><!-- /#poststuff -->
</div>
