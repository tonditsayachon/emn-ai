<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 * @author     Emonics Solution <emonics.dev@gmail.com>
 */
class Emn_Ai_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{


		// clear transient
		delete_transient('emn_ai_api_cache');

		// optional: ถ้าต้อง clean file/folder
		// self::delete_plugin_folders();
	}
}
