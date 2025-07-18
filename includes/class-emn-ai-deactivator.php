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


		// ลบ Cron ที่ตั้งเวลาไว้ออกเมื่อปิดใช้งานปลั๊กอิน
		wp_clear_scheduled_hook('halal_ai_process_brochure_queue_hook');
	}
}
