<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Emn_Ai
 * @subpackage Emn_Ai/includes
 * @author     Emonics Solution <emonics.dev@gmail.com>
 */
class Emn_Ai
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Emn_Ai_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('EMN_AI_VERSION')) {
			$this->version = EMN_AI_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'emn-ai';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_cron_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Emn_Ai_Loader. Orchestrates the hooks of the plugin.
	 * - Emn_Ai_i18n. Defines internationalization functionality.
	 * - Emn_Ai_Admin. Defines all hooks for the admin area.
	 * - Emn_Ai_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */


	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-emn-ai-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-emn-ai-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-emn-ai-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-emn-ai-public.php';

		$this->loader = new Emn_Ai_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Emn_Ai_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Emn_Ai_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Emn_Ai_Admin($this->get_plugin_name(), $this->get_version());


		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		// Register the admin menu
		$this->loader->add_action('admin_menu', $plugin_admin, 'emn_ai_menu');

		// UPDATED AJAX hooks
		$this->loader->add_action('wp_ajax_emn_ajax_clear_json_directory', $plugin_admin, 'emn_ajax_clear_json_directory');
		$this->loader->add_action('wp_ajax_emn_ajax_get_total_products', $plugin_admin, 'emn_ajax_get_total_products');
		$this->loader->add_action('wp_ajax_emn_ajax_process_batch', $plugin_admin, 'emn_ajax_process_batch');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_admin = new Emn_Ai_Admin($this->get_plugin_name(), $this->get_version());
		$plugin_public = new Emn_Ai_Public($this->get_plugin_name(), $this->get_version(), $plugin_admin);

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('rest_api_init', $plugin_public, 'register_routes');
		// // Hook into product save and delete actions
		$this->loader->add_action('save_post_product', $plugin_public, 'on_product_save', 10, 3);
		$this->loader->add_action('before_delete_post', $plugin_public, 'on_product_delete');
		$this->loader->add_action('transition_post_status', $plugin_public, 'on_product_status_change', 10, 3);
		$this->loader->add_action('emn_ai_trigger_brochure_generation', $plugin_public, 'process_brochure_generation_job', 10, 2);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Emn_Ai_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	private function define_cron_hooks()
	{
		// เพิ่ม hook สำหรับตัวประมวลผลคิว
		$this->loader->add_action('halal_ai_process_brochure_queue_hook', $this, 'process_brochure_queue');
		// เพิ่ม filter เพื่อสร้าง schedule "ทุก 10 นาที"
		$this->loader->add_filter('cron_schedules', $this, 'add_ten_minute_cron_interval');
	}

	// เพิ่มฟังก์ชันนี้เข้าไปในคลาส
	public function add_ten_minute_cron_interval($schedules)
	{
		$schedules['ten_minutes'] = [
			'interval' => 600, // 10 นาที = 600 วินาที
			'display'  => esc_html__('Every 10 Minutes'),
		];
		return $schedules;
	}

	// ในไฟล์ includes/class-emn-ai.php ภายในคลาส Emn_Ai

	/**
	 * ประมวลผลคิวสร้างโบรชัวร์ (ฟังก์ชันฉบับสมบูรณ์)
	 *
	 * ฟังก์ชันนี้จะถูกเรียกโดย Cron Job เพื่อดึงงานที่รอคิวมาประมวลผลทีละรายการ
	 * โดยมีขั้นตอนคือ: ดึงข้อมูล -> สร้าง PDF จาก Template -> ส่งอีเมล -> อัปเดตสถานะ
	 */
	
	public function process_brochure_queue()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'halal_ai_schedule_log';

		// 1. ดึงงานจากคิว (ยังคงดึงทีละงานเหมือนเดิม)
		$job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE status = %s ORDER BY log_id ASC LIMIT 1", 'scheduled'));

		if (is_null($job)) {
			return;
		}

		// --- 2. รวบรวมข้อมูลสินค้าทั้งหมดสำหรับงานนี้ ---
		// แปลง JSON string จากฐานข้อมูลกลับเป็น PHP Array
		$product_ids_array = json_decode($job->product_ids);
		$products_data_for_template = []; // เตรียม array ว่างสำหรับเก็บข้อมูลสินค้าทั้งหมด

		// ตรวจสอบว่ามี ID ให้ทำงานหรือไม่
		if (is_array($product_ids_array) && !empty($product_ids_array)) {
			// วนลูปตาม ID ที่ได้มา
			foreach ($product_ids_array as $product_id) {
				$json_file_path = WP_CONTENT_DIR . '/halal-ai/jsons/products/product_' . $product_id . '.json';

				if (!file_exists($json_file_path)) {
					// ถ้าไฟล์ JSON ไม่มีอยู่ ให้พยายามสร้างขึ้นมา
					error_log('Emn AI Notice: JSON file for product ' . $product_id . ' not found. Generating...');
					$this->plugin_admin->emn_json_generate_single($product_id);
				}

				// อ่านไฟล์อีกครั้ง (หลังจากอาจจะเพิ่งสร้างเสร็จ)
				if (file_exists($json_file_path)) {
					$json_content = file_get_contents($json_file_path);
					$decoded_data = json_decode($json_content);

					if (json_last_error() === JSON_ERROR_NONE) {
						// เพิ่มข้อมูลสินค้าที่สมบูรณ์ลงใน array ที่จะส่งให้ template
						$products_data_for_template[] = $decoded_data;
					}
				} else {
					error_log('Emn AI Error: Failed to find or generate JSON for product ID: ' . $product_id);
				}
			}
		}

		// ถ้าไม่มีข้อมูลสินค้าที่ถูกต้องเลย ให้จบการทำงาน
		if (empty($products_data_for_template)) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI Error: No valid product data could be compiled for job ID: ' . $job->log_id);
			return;
		}

		// --- 3. สร้าง HTML จาก Template ---
		ob_start();
		// ส่งตัวแปร $products_data ที่มีข้อมูลหลายสินค้าไปยัง Template
		// (เปลี่ยนชื่อตัวแปรให้สื่อความหมายมากขึ้น)
		$products_data = $products_data_for_template;
		include plugin_dir_path(dirname(__FILE__)) . 'public/partials/emn-ai-brochure-template.php';
		$html_content = ob_get_contents();
		ob_end_clean();

		// ส่วนที่เหลือของโค้ด (สร้าง PDF, ส่งอีเมล, อัปเดตสถานะ) จะทำงานเหมือนเดิม
		// แต่ตอนนี้ $html_content จะมีข้อมูลของสินค้าทุกชิ้นเรียบร้อยแล้ว

		if (empty($html_content)) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI Error: Failed to get content from brochure template for job ID: ' . $job->log_id);
			return;
		}

		// --- 4. สร้างไฟล์ PDF ---
		// --- 4. สร้างไฟล์ PDF ---
		// กำหนด Path สำหรับบันทึกไฟล์โดยตรง
		$brochure_dir = WP_CONTENT_DIR . '/halal-ai/jsons/brochures';
		if (! file_exists($brochure_dir)) {
			wp_mkdir_p($brochure_dir);
		}

		// ใช้ log_id ในการตั้งชื่อไฟล์เพื่อความ unique
		$file_name = 'brochure-' . $job->log_id . '.pdf';
		$file_path = $brochure_dir . '/' . $file_name;

		// สร้าง URL สำหรับดาวน์โหลดให้ถูกต้อง
		$file_url = content_url('/halal-ai/jsons/brochures/' . $file_name);

		try {
			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($html_content);
			$mpdf->Output($file_path, 'F');
		} catch (\Mpdf\MpdfException $e) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI mPDF Error for job ID ' . $job->log_id . ': ' . $e->getMessage());
			return;
		}

		if (! file_exists($file_path) || filesize($file_path) === 0) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI Error: Failed to create a valid PDF file for job ID: ' . $job->log_id);
			return;
		}

		// --- 5. ส่งอีเมล ---
		$to = $job->recipient_email;
		$subject = 'เอกสารโบรชัวร์สำหรับสินค้าที่คุณร้องขอ';

		// สร้างเนื้อหาอีเมลใหม่พร้อมลิงก์ดาวน์โหลด
		$body = '<p>สวัสดีครับ,</p><p>เอกสารโบรชัวร์สำหรับสินค้าที่คุณร้องขอพร้อมให้ดาวน์โหลดแล้วครับ</p>';
		$body .= '<p style="margin: 20px 0;"><a href="' . esc_url($file_url) . '" style="background-color: #0073aa; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px;" download><strong>คลิกที่นี่เพื่อดาวน์โหลดโบรชัวร์</strong></a></p>';
		$body .= '<p>ขอแสดงความนับถือ<br>ทีมงาน</p>';


		$headers = ['Content-Type: text/html; charset=UTF-8'];
		$attachments = []; //  <-- กำหนดให้เป็น array ว่างเพื่อไม่ให้แนบไฟล์

		$sent = wp_mail($to, $subject, $body, $headers, $attachments);

		// --- 6. อัปเดตฐานข้อมูล ---
		if ($sent) {
			$brochure_data = [
				'name' => basename($file_path),
				'size' => filesize($file_path),
				'url'  => $file_url,
			];
			$wpdb->update(
				$table_name,
				[
					'status'          => 'sent',
					'sent_timestamp'  => current_time('mysql', 1),
					'brochure_data'   => json_encode($brochure_data),
				],
				['log_id' => $job->log_id]
			);
		} else {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			if (file_exists($file_path)) {
				unlink($file_path);
			}
			error_log('Emn AI Error: Failed to send brochure email for job ID: ' . $job->log_id);
		}
	}
}
