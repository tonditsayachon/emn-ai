<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 * @author     Emonics Solution <emonics.dev@gmail.com>
 */
class Emn_Ai_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
   
            self::create_required_folders();
    
    }

        private static function create_initial_directories() {
        // ใช้ค่าคงที่ WP_CONTENT_DIR จะแม่นยำกว่า
        $base_path = WP_CONTENT_DIR . '/halal-ai/jsons/';

        // 1. กำหนด Path สำหรับโฟลเดอร์ products
        $products_dir = $base_path . 'products';

        // 2. กำหนด Path สำหรับโฟลเดอร์ brochures (ที่เพิ่มเข้ามาใหม่)
        $brochures_dir = $base_path . 'brochures';

        // 3. สร้างโฟลเดอร์ products ถ้ายังไม่มี
        if (!file_exists($products_dir)) {
            wp_mkdir_p($products_dir);
        }

        // 4. สร้างโฟลเดอร์ brochures ถ้ายังไม่มี
        if (!file_exists($brochures_dir)) {
            wp_mkdir_p($brochures_dir);
            
        
        }
    }

}
