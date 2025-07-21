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
        global $wpdb;

        // {prefix}_halal_ai_schedule_log
        $table_name = $wpdb->prefix . 'halal_ai_schedule_log';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
        log_id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_ids text NOT NULL,
        recipient_email varchar(100) NOT NULL,
        brochure_data text NOT NULL, -- เก็บข้อมูลไฟล์เป็น JSON
        request_date datetime NOT NULL,
        sent_timestamp datetime DEFAULT NULL,
        status varchar(20) NOT NULL DEFAULT 'scheduled', -- สถานะ: scheduled, sent, failed, deleted
        PRIMARY KEY  (log_id)
    ) $charset_collate;";

        // ใช้ dbDelta เพื่อสร้างหรืออัปเดตตารางอย่างปลอดภัย
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // กำหนดค่าเริ่มต้นสำหรับการตั้งเวลา
         if ( ! wp_next_scheduled( 'halal_ai_process_brochure_queue_hook' ) ) {
        wp_schedule_event( time(), 'ten_minutes', 'halal_ai_process_brochure_queue_hook' );
    }
    }

    private static function create_required_folders()
    {
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
