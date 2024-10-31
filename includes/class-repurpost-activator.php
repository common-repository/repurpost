<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.repurpost.com/
 * @since      2.0.0
 *
 * @package    Repurpost
 * @subpackage Repurpost/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Repurpost
 * @subpackage Repurpost/includes
 * @author     Arturo Jerez <xerezeno33@gmail.com>
 */
class Repurpost_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {
		$exists_token = get_option('repurpostWP_token');
		if (empty($exists_token)) {
			if (class_exists('RepurpostWP_token')) {
				$token = new RepurpostWP_token();
				add_option( 'repurpostWP_token', $token->generateToken(), '', 'yes' );
			}
		}

		$exists_referrer_status = get_option('repurpostWP_referrer_status');
		if (empty($exists_referrer_status)) {
			add_option( 'repurpostWP_referrer_status', false, '', 'yes' );
		}

		$exists_blogusers = get_option('repurpostWP_blogusers');
		if (empty($exists_blogusers)) {
			add_option( 'repurpostWP_blogusers', false, '', 'yes' );
		}

		$role_list = wp_roles();
		$role_objects = $role_list->role_objects;
		if ($role_objects && !empty($role_objects)) {
			foreach ($role_objects as &$role) {
				$role->add_cap('unfiltered_html');
			}
		}
	}

}
