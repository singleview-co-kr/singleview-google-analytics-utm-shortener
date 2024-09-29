<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

namespace SV_Utm_Url_Shortener\Includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( '\\SV_Utm_Url_Shortener\\Includes\\Classes\\IpFilter' ) ) {

	class IpFilter {
		public static function filter( $ip_list, $ip = null ) {
			if ( ! $ip ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			$long_ip = ip2long( $ip );
			foreach ( $ip_list as $filter_ip ) {
				$range = explode( '-', $filter_ip );
				if ( ! $range[1] ) {  // single address type
					$star_pos = strpos( $filter_ip, '*' );
					if ( $star_pos !== false ) { // wild card exist
						if ( strncmp( $filter_ip, $ip, $star_pos ) === 0 ) {
							return true;
						}
					} elseif ( strcmp( $filter_ip, $ip ) === 0 ) {
						return true;
					}
				} elseif ( ip2long( $range[0] ) <= $long_ip && ip2long( $range[1] ) >= $long_ip ) {
					return true;
				}
			}
			return false;
		}
	}
}
/* End of file : IpFilter.class.php */
