<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.emonics.net/
 * @since             1.0.0
 * @package           Emn_Ai
 *
 * @wordpress-plugin
 * Plugin Name:       AI Automate by emonics
 * Plugin URI:        https://www.emonics.net/
 * Description:       Just do it!
 * Version:           1.0.0
 * Author:            Emonics Solution
 * Author URI:        https://www.emonics.net//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       emn-ai
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EMN_AI_VERSION', '1.0.0' );
define('EMN_AI_PLUGIN_FILE', __FILE__);
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-emn-ai-activator.php
 */
function activate_emn_ai() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-emn-ai-activator.php';
	Emn_Ai_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-emn-ai-deactivator.php
 */
function deactivate_emn_ai() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-emn-ai-deactivator.php';
	Emn_Ai_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_emn_ai' );
register_deactivation_hook( __FILE__, 'deactivate_emn_ai' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-emn-ai.php';


// If you're using Composer, require the autoloader.
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_emn_ai() {

	$plugin = new Emn_Ai();
	$plugin->run();

}
run_emn_ai();
