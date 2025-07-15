<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/admin
 * @author     Emonics Solution <emonics.dev@gmail.com>
 */
class Emn_Ai_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/emn-ai-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/emn-ai-admin.js', array('jquery'), $this->version, false);
	}

	public function emn_ai_menu()
	{
		add_menu_page(
			__('EMN AI', 'emn-ai'),
			__('EMN AI', 'emn-ai'),
			'manage_options',
			'emn-ai',
			array($this, 'emn_ai_settings_page'),
			'dashicons-admin-generic'
		);
	}
	public function emn_automation_nonce()
	{
		// Nonce สำหรับความปลอดภัย
		return wp_create_nonce('emn_automation_action');
	}
	public function emn_ai_settings_page()
	{

		include_once plugin_dir_path(__FILE__) . 'partials/emn-ai-admin-display.php';
		if (isset($_POST['emn_automation_button'])) {
			if (check_admin_referer('emn_automation_action', 'emn_automation_nonce')) {
				try {
					$this->emn_run_automation();

					// Save current datetime
					update_option('emn_ai_last_run_time', current_time('mysql'));
				} catch (Throwable $e) {
					error_log('เกิดข้อผิดพลาด: ' . $e->getMessage());
					error_log('ไฟล์: ' . $e->getFile() . ' บรรทัด: ' . $e->getLine());
				}
			} else {
				echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
			}
		}
	}


	public function emn_run_automation()
	{
		$this->emn_query_product(); // เรียกใช้ฟังก์ชันที่คุณต้องการทำงาน


	} //end of function
	/// get max page (arg page size,page index) 
	//	delete all json file
	public function emn_query_product()
	{
		$args = [
			'post_type' => 'product',
			'post_status' => 'publish',
			'fields' => 'ids',
			'posts_per_page' => 100,
			'paged' => 1,
			'orderby' => 'ids',
		];

		do {
			$query = new WP_Query($args);
			if ($query->have_posts()) {
				foreach ($query->posts as $product_id) {
					$this->emn_json_generate_single($product_id);
				}
				$args['paged']++;
			} else {
				break;
			}
			wp_reset_postdata();
		} while (true);
	}


	public function emn_json_generate_single($product_id)
	{
		try {
			$product = wc_get_product($product_id);
			if (!$product) return;

			$post_modified_time = get_post_modified_time('U', true, $product_id); // GMT timestamp
			$output_dir = WP_CONTENT_DIR . '/halal-ai/jsons/products/';
			$filename = $output_dir . 'product_'.$product_id . '.json';

			// เช็คว่ามีไฟล์อยู่แล้วและไม่จำเป็นต้องเขียนใหม่
			if (file_exists($filename)) {
				$file_modified_time = filemtime($filename);
				if ($file_modified_time >= $post_modified_time) {
					// ไม่ต้องเขียนซ้ำ เพราะไฟล์ใหม่กว่าโพสต์
					return;
				}
			}

			// [ข้อมูล JSON ถูกเตรียมเหมือนเดิม...]
			$data = [
				'id' => $product->get_id(),
				'name' => $product->get_name(),
				'price' => $product->get_price(),
				'regular_price' => $product->get_regular_price(),
				'sale_price' => $product->get_sale_price(),
				'sku' => $product->get_sku(),
				'description' => $product->get_description(),
				'short_description' => $product->get_short_description(),
				'permalink' => get_permalink($product_id),
				'categories' => wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']),
				'tags' => wp_get_post_terms($product_id, 'product_tag', ['fields' => 'names']),
				'acf_fields' => $this->emn_get_acf($product_id),
				'updated_at' => date('Y-m-d H:i:s', $post_modified_time),
			];

			if (!file_exists($output_dir)) {
				wp_mkdir_p($output_dir); // แนะนำให้ใช้ฟังก์ชันนี้แทน mkdir
			}

			file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	
		} catch (Exception $e) {
			error_log("EMN JSON Generate Error: " . $e->getMessage());
		}
	}


	

	public function emn_get_acf($postid)
	{

		$acf_fields = [
			"product_volume" => "",
			"industry_specific_attributes" => [
				"type" => "",
				"shape" => "",
				"packaging" => "",
			],
			"other_attributes" => [
				"storage_type" => "",
				"specification" => "",
				"manufacturer" => "",
				"ingredients" => "",
				"address" => "",
				"place_of_origin" => "",
				"product_type" => "",
				"color" => "",
				"feature" => "",
				"brand_name" => "",
				"shelf_life" => "",
				"hs_code" => "",
			],
			"packaging_and_delivery" => [
				"packaging_details" => "",
				"port" => "",
				"selling_units" => "",
				"single_package_size" => "",
				"single_gross_weight" => "",
			],
			"supply_ability" => [
				"supply_ability" => "",

			],
		];
		$acf_data = array();
		foreach ($acf_fields as $key => $tempData) {
			$value = get_field($key, $postid);

			if (is_array($tempData)) {
				$acf_data[$key] = array_merge($tempData, $value ?? []);
			} else {
				$acf_data[$key] = $value ?? '';
			}
		}
		return $acf_data;
	}

	/**
	 * Returns the plugin name.
	 *
	 * @since    1.0.0
	 * @return   string    The name of the plugin.
	 */
} ///end of class
