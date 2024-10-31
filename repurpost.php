<?php

/**
 *
 * @link              https://www.repurpost.com/
 * @since             1.0.0
 * @package           Repurpost
 *
 * @wordpress-plugin
 * Plugin Name:       Repurpost
 * Plugin URI:        https://www.repurpost.com/
 * Description:       This plugin enables the integration between the Repurpost Platform and Wordpress.
 * Version:           1.1.0
 * Author: 			  Repurpost
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       repurpost
 * Domain Path:       /languages
 */

/*
* If this file is called directly, abort.
*/
defined( 'WPINC' ) or die( 'No script kiddies please!' );
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Currently plugin version.
 */
define( 'REPURPOST_VERSION', '1.1.0' );

// Class RepurpostWP_token
class RepurpostWP_token {
	public function generateToken() { // Generates new token when activating the plugin
		$token = bin2hex(random_bytes(64));
		return $token; // Returns value of the generated token
	}
}


if ( is_admin() ) {
    // we are in admin mode
	require_once( dirname( __FILE__ ) . '/admin/class-repurpost-admin.php' );
	require_once( dirname( __FILE__ ) . '/admin/partials/repurpost-admin-display.php' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-repurpost-activator.php
 */
function activate_repurpost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-repurpost-activator.php';
	Repurpost_Activator::activate();
}


/*
* Add new routes to the URL with differents methods
*/
add_action('rest_api_init', function () {
	require_once plugin_dir_path( __FILE__ ) . 'api/rest_api/routes.php';
});


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-repurpost-deactivator.php
 */
function deactivate_repurpost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-repurpost-deactivator.php';
	Repurpost_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_repurpost' );
register_deactivation_hook( __FILE__, 'deactivate_repurpost' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-repurpost.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_repurpost() {

	$plugin = new Repurpost();
	$plugin->run();

}
run_repurpost();