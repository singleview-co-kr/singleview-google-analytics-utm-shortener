<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 

if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_INSERT ) {
	$s_action = __( 'cmd_insert', UTM_SHORTENER_DOMAIN );
}
if ( $_GET['page'] == UTM_SHORTENER_CMD_ADMIN_VIEW_UPDATE ) {
	$s_action = __( 'cmd_update', UTM_SHORTENER_DOMAIN );
}

?>
<div class="singleview-header-logo"></div>
<h1 class="wp-heading-inline"><?php echo UTM_SHORTENER_DOMAIN . ' : ' . $s_action; ?></h1>
<a href="https://singleview.co.kr" class="page-title-action" onclick="window.open(this.href);return false;">Home</a>
<a href="https://singleview.co.kr/community" class="page-title-action" onclick="window.open(this.href);return false;"><?php echo __( 'cmd_goto_community', UTM_SHORTENER_DOMAIN ); ?></a>
<a href="https://singleview.co.kr/qna" class="page-title-action" onclick="window.open(this.href);return false;"><?php echo __( 'cmd_goto_support', UTM_SHORTENER_DOMAIN ); ?></a>
<hr class="wp-header-end">
