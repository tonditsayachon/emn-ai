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

    private static function create_required_folders() {

    $base_dir = WP_CONTENT_DIR . '/halal-ai/jsons/products';

    if ( ! file_exists( $base_dir ) ) {
        // สร้างโฟลเดอร์ทีละขั้น
        $halal_dir = WP_CONTENT_DIR . '/halal-ai';
        if ( ! file_exists( $halal_dir ) ) {
            wp_mkdir_p( $halal_dir );
        }

        $json_dir = $halal_dir . '/jsons';
        if ( ! file_exists( $json_dir ) ) {
            wp_mkdir_p( $json_dir );
        }

        $products_dir = $json_dir . '/products';
        if ( ! file_exists( $products_dir ) ) {
            wp_mkdir_p( $products_dir );
        }
    }
}

}
