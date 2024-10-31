<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.repurpost.com/
 * @since      2.0.0
 *
 * @package    Repurpost
 * @subpackage Repurpost/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    Repurpost
 * @subpackage Repurpost/includes
 * @author     Arturo Jerez <xerezeno33@gmail.com>
 */
class Repurpost_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
		$role_list = wp_roles();
		$role_objects = $role_list->role_objects;
		if ($role_objects && !empty($role_objects)) {
			foreach ($role_objects as &$role) {
				if ($role->name != 'administrator') {
					$role->remove_cap('unfiltered_html');
				}
			}
		}
	}

}
