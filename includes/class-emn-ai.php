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

		// --- 1. ดึงงานจากคิว ---
		// ดึงงานที่เก่าที่สุดซึ่งมีสถานะ 'scheduled' ออกมาจากคิวเพียง 1 รายการ
		$job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE status = %s ORDER BY log_id ASC LIMIT 1", 'scheduled'));

		// ถ้าไม่มีงานในคิว ให้หยุดทำงานทันที
		if (is_null($job)) {
			return;
		}

		// --- 2. ดึงเนื้อหาจาก Template ---
		// เตรียมข้อมูลที่จำเป็นเพื่อส่งไปให้ไฟล์ Template
		$product_post = get_post($job->product_id);
		// คุณสามารถประกาศตัวแปรอื่นๆ เพิ่มเติมได้ตามต้องการ
		// เช่น $company_info = get_option('your_company_info');

		// ใช้ Output Buffering เพื่อ "ดักจับ" เนื้อหา HTML จากไฟล์ Template
		ob_start();

		// กำหนด Path ไปยังไฟล์ Template ในโฟลเดอร์ public
		$template_path = plugin_dir_path(dirname(__FILE__)) . 'public/partials/emn-ai-brochure-template.php';

		if (file_exists($template_path)) {
			// เมื่อ include ไฟล์ ตัวแปรที่ประกาศไว้ก่อนหน้า ($product_post) จะสามารถใช้งานได้ใน Template ทันที
			include $template_path;
		}

		// นำเนื้อหา HTML ทั้งหมดมาเก็บในตัวแปร
		$html_content = ob_get_contents();
		ob_end_clean(); // สิ้นสุดและล้างการบัฟเฟอร์

		// ตรวจสอบว่าได้เนื้อหาจาก Template มาหรือไม่
		if (empty($html_content)) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI Error: Failed to get content from brochure template for job ID: ' . $job->log_id);
			return;
		}

		// --- 3. สร้างไฟล์ PDF ---
		$upload_dir = wp_upload_dir();
		$brochure_dir = $upload_dir['basedir'] . '/halal-ai/brochures';

		// สร้างโฟลเดอร์ /wp-content/uploads/halal-ai/brochures/ หากยังไม่มี
		if (! file_exists($brochure_dir)) {
			wp_mkdir_p($brochure_dir);
		}

		$file_name = 'brochure-' . $job->product_id . '-' . $job->log_id . '.pdf';
		$file_path = $brochure_dir . '/' . $file_name;
		$file_url = $upload_dir['baseurl'] . '/halal-ai/brochures/' . $file_name;

		// ส่งเนื้อหา HTML ที่ได้ไปให้ Library เพื่อสร้างไฟล์ PDF
		try {
			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($html_content);
			$mpdf->Output($file_path, 'F'); // 'F' คือสั่งให้บันทึกเป็นไฟล์
		} catch (\Mpdf\MpdfException $e) {
			// หาก Library สร้าง PDF ไม่สำเร็จ
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI mPDF Error for job ID ' . $job->log_id . ': ' . $e->getMessage());
			return;
		}

		// ตรวจสอบอีกครั้งว่าไฟล์ PDF ถูกสร้างขึ้นจริงหรือไม่
		if (! file_exists($file_path) || filesize($file_path) === 0) {
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			error_log('Emn AI Error: Failed to create a valid PDF file for job ID: ' . $job->log_id);
			return;
		}

		// --- 4. ส่งอีเมล ---
		$to = $job->recipient_email;
		$subject = 'เอกสารโบรชัวร์สำหรับสินค้า (ID: ' . $job->product_id . ')';
		$body = '<p>สวัสดีครับ,</p><p>นี่คือเอกสารโบรชัวร์ที่คุณร้องขอจากเราครับ</p>';
		$body .= '<p>ขอแสดงความนับถือ<br>ทีมงาน</p>';
		// สร้าง URL สำหรับ Tracking Pixel เพื่อใช้ตรวจสอบการเปิดอ่านอีเมล
		$tracking_url = get_rest_url(null, "halal-ai/v1/track/{$job->log_id}");
		$body .= "<img src='{$tracking_url}' width='1' height='1' alt='' style='display:none;' />";

		$headers = ['Content-Type: text/html; charset=UTF-8'];
		$attachments = [$file_path]; // ระบุ Path ของไฟล์ PDF ที่จะแนบไปกับอีเมล

		$sent = wp_mail($to, $subject, $body, $headers, $attachments);

		// --- 5. อัปเดตฐานข้อมูล ---
		if ($sent) {
			// หากส่งอีเมลสำเร็จ
			$brochure_data = [
				'name' => basename($file_path),
				'size' => filesize($file_path),
				'url'  => $file_url,
			];
			// อัปเดตสถานะเป็น 'sent' พร้อมบันทึกข้อมูลไฟล์และเวลาที่ส่ง
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
			// หากส่งอีเมลไม่สำเร็จ
			
			$wpdb->update($table_name, ['status' => 'failed'], ['log_id' => $job->log_id]);
			// ลบไฟล์ PDF ที่สร้างขึ้นทิ้ง เพื่อไม่ให้เปลืองพื้นที่
			if (file_exists($file_path)) {
				unlink($file_path);
			}
			error_log('Emn AI Error: Failed to send brochure email for job ID: ' . $job->log_id);
		}
	}
}
