<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/public
 * @author     Emonics Solution <emonics.dev@gmail.com>
 */
class Emn_Ai_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	private $admin_instance;

	public function __construct($plugin_name, $version, $admin_instance = null)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->admin_instance = $admin_instance;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Emn_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Emn_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/emn-ai-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Emn_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Emn_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/emn-ai-public.js', array('jquery'), $this->version, false);
	}
	public function register_routes()
	{
		register_rest_route('halal-ai/v1', '/jsons/products', [
			'methods' => 'GET',
			'callback' => array($this, 'api_list_json_files'),
			'permission_callback' => array($this, 'api_key_permission')

		]);

		register_rest_route('halal-ai/v1', '/jsons/products/(?P<id>\d+)', [
			'methods' => 'DELETE',
			'callback' => array($this, 'api_delete_json_file'),
			'permission_callback' => array($this, 'api_key_permission')
		]);
	}

	private function get_json_dir()
	{
		return WP_CONTENT_DIR . '/halal-ai/jsons/products/';
	}

	public function api_list_json_files($request)
	{


		$folder_path = $this->get_json_dir();
		$base_url = home_url('/wp-content/halal-ai/jsons/products/');

		$result = [];

		// ตรวจสอบว่าโฟลเดอร์มีอยู่หรือไม่
		if (is_dir($folder_path)) {
			// ใช้ glob เพื่ออ่านไฟล์ทั้งหมดที่เป็น product_*.json
			$files = glob($folder_path . 'product_*.json');

			foreach ($files as $file_path) {
				// ดึงเฉพาะชื่อไฟล์ เช่น product_1.json
				$filename = basename($file_path);

				// ใช้ regex ดึงหมายเลข id ออกจากชื่อไฟล์
				if (preg_match('/product_(\d+)\.json$/', $filename, $matches)) {
					$id = intval($matches[1]);

					$result[] = [
						'id'  => $id,
						'url' => $base_url . $filename,
					];
				}
			}
		}


		return rest_ensure_response($result);
	}




	public function api_delete_json_file($request)
	{
		$post_id = intval($request['id']);
		$file_path = $this->get_json_dir() . "product_{$post_id}.json";

		if (!file_exists($file_path)) {
			return rest_ensure_response(['deleted' => false, 'file' => basename($file_path)]);
			// return new WP_Error('file_not_found', 'File not found', ['status' => 200]);
		}

		if (!unlink($file_path)) {
			return rest_ensure_response(['deleted' => false, 'file' => basename($file_path)]);
			// return new WP_Error('delete_failed', 'Unable to delete file', ['status' => 200]);
		}

		return rest_ensure_response(['deleted' => true, 'file' => basename($file_path)]);
	}

	public function on_product_save($post_id, $post = null, $update = null)

	{
		if (is_null($post)) {
			$post = get_post($post_id);
		}

		if (! $post || $post->post_type !== 'product') return;

		// ป้องกัน autosave / revision
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if ($post->post_type !== 'product') return;

		// เรียก automation
		$this->admin_instance->emn_json_generate_single($post_id);

	}

	public function on_product_delete($post_id)
	{
		$post_type = get_post_type($post_id);
		if ($post_type !== 'product') return;
		$file_path = WP_CONTENT_DIR . "/halal-ai/jsons/products/product_{$post_id}.json";
		unlink($file_path);
	
	}

	public function on_product_status_change($new_status, $old_status, $post)
	{
		if ($post->post_type !== 'product') return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (wp_is_post_revision($post->ID)) return;

		if ($old_status === 'publish' && $new_status !== 'publish') {
			$file_path = WP_CONTENT_DIR . "/halal-ai/jsons/products/product_{$post->ID}.json";
			unlink($file_path);
		
		} 
		
	}
	
	public function api_key_permission($request)
	{
		global $wpdb;

		$consumer_key_raw = $request->get_header('emn_key');
		if (empty($consumer_key_raw)) {
			return new WP_Error('missing_key', 'Missing API Key', array('status' => 401));
		}

		// WooCommerce uses hashed consumer key
		if (!function_exists('wc_api_hash')) {
			return new WP_Error('missing_wc_api_hash', 'WooCommerce wc_api_hash function not available', array('status' => 500));
		}

		$consumer_key_hash = wc_api_hash($consumer_key_raw);

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE consumer_key = %s",
				$consumer_key_hash
			)
		);

		if (!$row) {
			return new WP_Error('invalid_key', 'Invalid or unauthorized API Key', array('status' => 403));
		}

		return true;
	}
}
