<?php
/**
* Plugin Name: User Registration Date And Last Login Date
* Plugin URI: https://wordpress.org/plugins/
* Description: This plugin shows the user registration date and user Last Login in the table of the Users section in the WordPress dashboard.
* Version: 1.0.7
* Author: Rajesh Kumar
* Author URI: https://profiles.wordpress.org/krajesh0018/
* Text Domain: User Registration Date Last Login
* License: GPLv2 or later
*
* @package User Registration Date And Last Login Date
*/
// First Update User Login Data
function user_last_login( $user_login, $user ) {
	update_user_meta( $user->ID, 'kraj_last_login', time() );
}
add_action( 'wp_login', 'user_last_login', 10, 2 );

// Starts the plugin
add_action( 'plugins_loaded', 'kraj_function' );
function kraj_function(){
	/* User Registration Date */
	add_filter('manage_users_columns', 'adduser_col');
	function adduser_col($columns) {
	    $columns['user_registered'] = 'Registered On';
	    return $columns;
	}
	 
	add_action('manage_users_custom_column',  'adddate_col', 10, 3);
	function adddate_col($value, $column_name, $user_id) {
	    $user = get_userdata( $user_id );
	    if ( 'user_registered' == $column_name ){
			//$value = $user->user_registered.' ( '.date( "d F Y", strtotime( $user->user_registered ) ).' )';
			$value = date( "d F Y", strtotime( $user->user_registered ) );
			return $value;
		}
	    return $value;
	}

	/* User Loging Date */
	add_filter('manage_users_columns', 'adduserlogin_col');
	function adduserlogin_col($columns) {
	    $columns['user_login'] = 'Last Login';
	    return $columns;
	}

	add_action('manage_users_custom_column',  'krajmanage_users_login_column', 10, 3);
	function krajmanage_users_login_column( $value, $column_name, $user_id ) {
		if ( 'user_login' === $column_name ) {
			$value      = __( '--No Record--', 'last-login' );
			//$value = get_current_user_id();
			$last_login = (int) get_user_meta( $user_id, 'kraj_last_login', true );

			if ( $last_login ) {
				$format = apply_filters( 'wpll_date_format', get_option( 'date_format' ) );
				//$value  = get_date_from_gmt( date( 'Y-m-d H:i:s', $last_login ), $format );
				//$value = date( 'Y-m-d H:i:s', $last_login );
				$value = date( 'Y-m-d', $last_login ).' ( '.human_time_diff($last_login).' )';
			}
		}

		return $value;
	}
}