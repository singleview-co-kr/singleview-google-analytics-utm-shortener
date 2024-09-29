<?php
/**
 * The user-specific functionality of the plugin.
 *
 * @author  https://singleview.co.kr/
 */

namespace SV_Utm_Url_Shortener\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly.
}

function woocommerce_ga_gtag( $s_script ) {  // Google Analytics for WooCommerce
    $s_shortener_query = get_option( UTM_SHORTENER_DOMAIN . '_shortener_query' );
    if( ! $s_shortener_query ) {
        return $s_script;
    }
    if( ! isset( $_GET[$s_shortener_query] ) ) {
        return $s_script;
    }
    if( _is_crawler() ) {
        return $s_script;
    }
    $o_rst = _get_shortener_info($_GET[$s_shortener_query]);
    if( ! $o_rst->bool ) {
        return $s_script;
    }
    $o_utm_info = _get_utm_info( $o_rst->o_single_shortener );
    
    // begin - build gtag
    $a_gtag_script = array();
    foreach( explode("\n", $s_script) as $s_line ) {
        if( strpos( $s_line, 'Date()' ) === false ) {
            $a_gtag_script[] = $s_line;
        }
        else {
            $a_gtag_script[] = $s_line;
            $a_gtag_script[] = "					gtag('set', {";
            $a_gtag_script[] = "						'campaign_name': '" . $o_utm_info->s_campaign_name . "',";
            $a_gtag_script[] = "						'campaign_source': '" . $o_utm_info->s_source_name . "',";
            $a_gtag_script[] = "						'campaign_medium': '" . $o_utm_info->s_medium_name . "',";
            $a_gtag_script[] = "						'campaign_term': '" . $o_utm_info->s_campaign_term . "',";
            // $a_gtag_script[] = "						'campaign_id': '',"
            // $a_gtag_script[] = "						'campaign_content': '',";
            $a_gtag_script[] = "					});";
        }
    }
    unset( $o_utm_info );
    $s_script = implode( PHP_EOL, $a_gtag_script );
    unset( $a_gtag_script );
    // end - build gtag
    unset( $o_rst );
    return $s_script;
}
add_filter('woocommerce_gtag_snippet', 'SV_Utm_Url_Shortener\Includes\woocommerce_ga_gtag');

/**
 *
 */
function _get_utm_info( $o_single_shortener ) {
    global $a_utm_source_info;
    global $a_utm_source_mapper;
    global $a_utm_medium_mapper;

    $o_rst = new \stdClass();
    $o_rst->s_source_name = $a_utm_source_info[ $o_single_shortener->utm_source ];
    $o_rst->s_medium_name = '';
    $o_rst->s_campaign_name = '';
    $o_rst->s_campaign_term = '';

    $s_source_abbreviation = $a_utm_source_mapper[ $o_single_shortener->utm_source ];  // NV
    $s_medium_abbreviation = $a_utm_medium_mapper[ $o_single_shortener->utm_medium ];  // CPC
    
    if( intval( $o_single_shortener->influencer_type ) ) {  // reviewer, top_ranker, penetration
        $s_regdate = str_replace( '-', '', $o_single_shortener->regdate );  // 20240927
        $o_rst->s_medium_name = 'organic';
        $o_rst->s_campaign_name = $s_source_abbreviation . '_PNS_' . $s_medium_abbreviation . '_BL_' . $s_regdate;
        $o_rst->s_campaign_term = '파블_' . $o_single_shortener->utm_term . '_' . $o_single_shortener->influencer_id . '_' . $s_regdate;
    }

    // begin - increase click count
    global $wpdb;
    $query = "UPDATE `{$wpdb->prefix}sv_utm_shortener` SET `click`=`click`+1 WHERE `seq`='" . esc_sql( intval( $o_single_shortener->seq ) ) . "'";
    if ( $wpdb->query( $query ) === false ) {
        error_log(print_r( UTM_SHORTENER_DOMAIN . ' update click failed', true));
    }
    unset( $query );
    // end - increase click count
    // begin - add hit log
    $result = $wpdb->insert(
        "{$wpdb->prefix}sv_utm_shortener_log",
        array(
            'shortener_seq' => $o_single_shortener->seq,
            'is_mobile'     => wp_is_mobile() ? 'Y' : 'N',
            'ipaddress'     => _get_remote_ip(),
            'regdate'       => current_time('mysql')
        ),
        array( '%d', '%s', '%s', '%s' )
    );
    if ( $result < 0 || $result === false ) {
        error_log( print_r( UTM_SHORTENER_DOMAIN . ' ' . $wpdb->last_error, true ) );
    }
    // end - add hit log
    return $o_rst;
}

/**
 *
 */
function _get_shortener_info( $s_value ) {
    global $wpdb;

    $o_rst = new \stdClass();
    $o_rst->bool = false;
    $o_rst->o_single_shortener = null;

    $s_value = esc_sql( sanitize_text_field( $s_value ) );
    $s_columns = "`seq`, `utm_source`, `utm_medium`, `utm_term`, `utm_content`, `utm_campaign_id`, `influencer_type`, `influencer_id`, `regdate`";
    $o_single_shortener = $wpdb->get_row( "select {$s_columns} from {$wpdb->prefix}sv_utm_shortener WHERE `shorten_uri_value`= '{$s_value}'" );
    if( $o_single_shortener ) {
        $o_rst->bool = true;
        $o_rst->o_single_shortener = $o_single_shortener;
    }
    unset( $o_single_shortener );
    return $o_rst;
}

/**
 * 사용자 IP 주소를 반환한다.
 *
 * @return string
 */
function _get_remote_ip() {
	static $s_ip;
	if ( $s_ip === null ) {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$s_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$s_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$s_ip = $_SERVER['REMOTE_ADDR'];
		}
	}
	return $s_ip;
}

/**
 * Get is current user crawler
 *
 * @param string $agent if set, use this value instead HTTP_USER_AGENT
 * @return bool
 */
function _is_crawler( $agent = null, $a_check_ip = array() ) {
	if ( ! $agent ) {
		$agent = $_SERVER['HTTP_USER_AGENT'];
	}

	$a_check_agent = array( 'bot', 'spider', 'spyder', 'crawl', 'http://', 'google', 'yahoo', 'slurp', 'yeti', 'daum', 'teoma', 'fish', 'hanrss', 'facebook', 'yandex', 'infoseek', 'askjeeves', 'stackrambler', 'python' );
	foreach ( $a_check_agent as $str ) {
		if ( stristr( $agent, $str ) != false ) {
            unset( $a_check_agent );
			return true;
		}
	}
    require_once UTM_SHORTENER_PATH . 'includes/classes/security/IpFilter.class.php';
    // $a_check_ip    = array(
		/*'211.245.21.110-211.245.21.119' mixsh is closed */
	// );
	return \SV_Utm_Url_Shortener\Includes\Classes\IpFilter::filter( $a_check_ip );
}
