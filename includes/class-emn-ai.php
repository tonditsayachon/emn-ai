<?php

class Emn_Ai
{
	protected $loader;
	protected $plugin_name;
	protected $version;

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

		$this->loader->add_action('init', $this, 'brochure_preview_trigger');
		// --- [แก้ไข] ย้ายการตั้งเวลา Cron มาจัดการตอน Activate/Deactivate ปลั๊กอิน ---
		// หมายเหตุ: คุณต้องมีไฟล์หลักของปลั๊กอินที่ระบุ EMN_AI_PLUGIN_FILE
		if (defined('EMN_AI_PLUGIN_FILE')) {
			register_activation_hook(EMN_AI_PLUGIN_FILE, array($this, 'schedule_cron_events'));
			register_deactivation_hook(EMN_AI_PLUGIN_FILE, array($this, 'clear_scheduled_events'));
		}
	}

	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-emn-ai-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-emn-ai-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-emn-ai-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-emn-ai-public.php';
		$this->loader = new Emn_Ai_Loader();
	}

	private function set_locale()
	{
		$plugin_i18n = new Emn_Ai_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	private function define_admin_hooks()
	{
		$plugin_admin = new Emn_Ai_Admin($this->get_plugin_name(), $this->get_version());
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'emn_ai_menu');
		$this->loader->add_action('wp_ajax_emn_ajax_clear_json_directory', $plugin_admin, 'emn_ajax_clear_json_directory');
		$this->loader->add_action('wp_ajax_emn_ajax_get_total_products', $plugin_admin, 'emn_ajax_get_total_products');
		$this->loader->add_action('wp_ajax_emn_ajax_process_batch', $plugin_admin, 'emn_ajax_process_batch');
	}

	private function define_public_hooks()
	{
		$plugin_admin = new Emn_Ai_Admin($this->get_plugin_name(), $this->get_version());
		$plugin_public = new Emn_Ai_Public($this->get_plugin_name(), $this->get_version(), $plugin_admin);
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('rest_api_init', $plugin_public, 'register_routes');
		$this->loader->add_action('save_post_product', $plugin_public, 'on_product_save', 10, 3);
		$this->loader->add_action('before_delete_post', $plugin_public, 'on_product_delete');
		$this->loader->add_action('transition_post_status', $plugin_public, 'on_product_status_change', 10, 3);
	}

	public function run()
	{
		$this->loader->run();
	}

	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	public function get_loader()
	{
		return $this->loader;
	}

	public function get_version()
	{
		return $this->version;
	}

	private function define_cron_hooks()
	{
		$this->loader->add_action('halal_ai_process_brochure_queue_hook', $this, 'process_brochure_queue');

		// --- [เพิ่มส่วนนี้] Hook ใหม่ ---
		$this->loader->add_action('halal_ai_cleanup_old_brochures_hook', $this, 'cleanup_old_brochures');
		$this->loader->add_filter('cron_schedules', $this, 'add_ten_minute_cron_interval');
	}

	/**
	 * ฟังก์ชันสำหรับตั้งเวลา Cron Job ตอนเปิดใช้งานปลั๊กอิน
	 */
	public function schedule_cron_events()
	{
		if (!wp_next_scheduled('halal_ai_process_brochure_queue_hook')) {
			wp_schedule_event(time(), 'ten_minutes', 'halal_ai_process_brochure_queue_hook');
		}

		// --- [เพิ่มส่วนนี้] Cron Job ใหม่สำหรับลบไฟล์เก่า ---
		if (!wp_next_scheduled('halal_ai_cleanup_old_brochures_hook')) {
			wp_schedule_event(time(), 'daily', 'halal_ai_cleanup_old_brochures_hook');
		}
	}

	/**
	 * ฟังก์ชันสำหรับลบ Cron Job ตอนปิดใช้งานปลั๊กอิน
	 */
	public function clear_scheduled_events()
	{
		wp_clear_scheduled_hook('halal_ai_process_brochure_queue_hook');

		// --- [เพิ่มส่วนนี้] ล้าง Cron Job ใหม่ ---
		wp_clear_scheduled_hook('halal_ai_cleanup_old_brochures_hook');
	}

	public function add_ten_minute_cron_interval($schedules)
	{
		$schedules['ten_minutes'] = [
			'interval' => 600,
			'display'  => esc_html__('Every 10 Minutes'),
		];
		return $schedules;
	}


	/**
	 * ดึงข้อมูลสินค้าทั้งหมดที่จำเป็นสำหรับสร้างโบรชัวร์
	 * @param array $product_ids_array อาร์เรย์ของ ID สินค้า
	 * @return array ข้อมูลสินค้าสำหรับส่งให้ Template
	 */
	public function get_brochure_products_data($product_ids_array)
	{
		$products_data_for_template = [];

		if (!is_array($product_ids_array) || empty($product_ids_array)) {
			return [];
		}

		foreach ($product_ids_array as $product_id) {
			$product_id = (int) $product_id;
			$product = wc_get_product($product_id);

			if (!$product || $product->get_status() !== 'publish') {
				//error_log('Emn AI [Data Fetch]: Could not find or product is not published for product ID: ' . $product_id);
				continue;
			}

			// ดึงข้อมูล Product Gallery
			$gallery_image_urls = [];
			$gallery_ids = $product->get_gallery_image_ids();
			if (!empty($gallery_ids)) {
				foreach (array_slice($gallery_ids, 0, 6) as $gallery_id) {
					$gallery_image_urls[] = wp_get_attachment_url($gallery_id);
				}
			}

			// B2C visible for logged out users (Guest)
			$tiered_price_data_string = get_post_meta($product_id, 'b2bking_product_pricetiers_group_b2c', true);

			$tiers_prices = [];
			if (!empty($tiered_price_data_string)) {
				// B2BKing stores data like "10:90;20:80;"
				$tiers_raw = explode(';', trim($tiered_price_data_string, ';'));
				foreach ($tiers_raw as $tier) {
					$parts = explode(':', $tier);
					if (count($parts) === 2) {
						// แปลงข้อมูลให้อยู่ในรูปแบบที่ Template เราต้องการ
						$tiers_prices[] = ['quantity' => $parts[0], 'price' => $parts[1]]; // B2BKing อาจไม่มีส่วนลดเป็น % ตรงๆ
					}
				}
			}
			// ดึงข้อมูล ACF Fields ผ่านคลาส Admin
			$plugin_admin = new Emn_Ai_Admin($this->get_plugin_name(), $this->get_version());

			// รวบรวมข้อมูลทั้งหมด
			$products_data_for_template[] = (object)[
				'id'                => $product->get_id(),
				'name'              => $product->get_name(),
				'description'       => $product->get_description(),
				'featured_image'    => get_the_post_thumbnail_url($product_id, 'full'),
				'vendor_info'       => Emn_Ai_Public::get_vendor_info_by_product_id($product_id),
				'regular_price' => $product->get_regular_price(),
				'sale_price'    => $product->get_sale_price(),
				'tiers_prices'      => $tiers_prices,
				'product_gallery'   => $gallery_image_urls,
				'acf_fields'        => (object) $plugin_admin->emn_get_acf($product_id),
			];

			//error_log('Emn AI [Data Fetch]: Successfully fetched data for product ID: ' . $product_id);
		}

		return $products_data_for_template;
	}
	public function process_brochure_queue()
	{



		global $wpdb;
		$table_name = $wpdb->prefix . 'halal_ai_schedule_log';
		//error_log('Emn AI Cron: Starting brochure queue processing.');

		$job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE status = %s ORDER BY log_id ASC LIMIT 1", 'scheduled'));
		if (is_null($job)) {
			return;
		}
		// --- [เพิ่ม] ดึง cover style จาก job data ---
		$brochure_meta = json_decode($job->brochure_data, true);
		$cover_style = isset($brochure_meta['cover_style']) ? absint($brochure_meta['cover_style']) : 1;
		//error_log('Emn AI Cron: Processing job ID: ' . $job->log_id . ' for email: ' . $job->recipient_email);

		$product_ids_array = json_decode($job->product_ids);

		// --- [แก้ไข] เรียกใช้ฟังก์ชันใหม่เพื่อดึงข้อมูล ---
		$products_data_for_template = $this->get_brochure_products_data($product_ids_array);

		if (empty($products_data_for_template)) {
			$wpdb->update($table_name, ['status' => 'failed', 'brochure_data' => 'Error: No valid products found.'], ['log_id' => $job->log_id]);
			//error_log('Emn AI Error: Job ID ' . $job->log_id . ' failed. Reason: No valid product data could be compiled.');
			return;
		}
		// --- [เพิ่ม Log] ตรวจสอบก่อนสร้าง HTML ---
		//error_log('Emn AI Cron: Data compiled. Attempting to generate HTML from template.');
		ob_start();
		$products_data = $products_data_for_template;
		include plugin_dir_path(dirname(__FILE__)) . 'public/partials/emn-ai-brochure-template.php';
		$html_content = ob_get_clean();

		if (empty($html_content) || ob_get_length() > 0) { // ตรวจสอบว่ามี output ที่ไม่ต้องการหรือไม่
			ob_end_clean(); // เคลียร์ output ที่อาจค้างอยู่
			$wpdb->update($table_name, ['status' => 'failed', 'brochure_data' => 'Error: Failed to get content from template or buffer had output.'], ['log_id' => $job->log_id]);
			//error_log('Emn AI Error: Failed to get content from template for job ID: ' . $job->log_id);
			return;
		}

		// --- [เพิ่ม Log] ตรวจสอบก่อนสร้าง PDF ---
		//error_log('Emn AI Cron: HTML generated. Attempting to create PDF with mPDF...');
		try {
			$brochure_dir = WP_CONTENT_DIR . '/halal-ai/jsons/brochures';
			if (!file_exists($brochure_dir)) {
				wp_mkdir_p($brochure_dir);
			}

			$file_name = 'brochure-' . $job->log_id . '-' . time() . '.pdf';
			$file_path = $brochure_dir . '/' . $file_name;

			$mpdf = new \Mpdf\Mpdf([
				'tempDir' => $brochure_dir,
				'fontDir' => [$brochure_dir . '/ttfontdata'],
				'fontdata' => [
					'inter' => [
						'R' => 'Inter-Regular.ttf',
						'B' => 'Inter-Bold.ttf',
					],
				],
				'default_font' => 'inter'
			]);
			$mpdf->WriteHTML($html_content);
			$mpdf->Output($file_path, 'F');
		} catch (\Mpdf\MpdfException $e) {
			$wpdb->update($table_name, ['status' => 'failed', 'brochure_data' => 'mPDF Error: ' . $e->getMessage()], ['log_id' => $job->log_id]);
			//error_log('Emn AI mPDF Error for job ID ' . $job->log_id . ': ' . $e->getMessage());
			return;
		}

		// --- [เพิ่ม Log] ตรวจสอบหลังสร้าง PDF ---
		if (!file_exists($file_path) || filesize($file_path) === 0) {
			$wpdb->update($table_name, ['status' => 'failed', 'brochure_data' => 'Error: PDF file was not created or is empty.'], ['log_id' => $job->log_id]);
			//error_log('Emn AI Error: PDF file was not created or is empty for job ID: ' . $job->log_id);
			return;
		}
		//error_log('Emn AI Cron: PDF generated successfully for job ID: ' . $job->log_id . '. Attempting to send email...');

		// --- [เพิ่ม Log] ตรวจสอบก่อนส่งอีเมล ---
		$to = $job->recipient_email;
		$subject = 'เอกสารโบรชัวร์สำหรับสินค้าที่คุณร้องขอ';
		$file_url = content_url('/halal-ai/jsons/brochures/' . $file_name);
		$body = '<p>สวัสดีครับ,</p><p>เอกสารโบรชัวร์สำหรับสินค้าที่คุณร้องขอพร้อมให้ดาวน์โหลดแล้วครับ</p>';
		$body .= '<p style="margin: 20px 0;"><a href="' . esc_url($file_url) . '" style="background-color: #0073aa; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px;" download><strong>คลิกที่นี่เพื่อดาวน์โหลดโบรชัวร์</strong></a></p>';
		$headers = ['Content-Type: text/html; charset=UTF-8'];

		$sent = wp_mail($to, $subject, $body, $headers);

		// --- [เพิ่ม Log] ตรวจสอบหลังส่งอีเมล ---
		if ($sent) {
			//error_log('Emn AI Cron: wp_mail() returned TRUE. Email sent successfully for job ID: ' . $job->log_id);
			$brochure_data = ['name' => basename($file_path), 'size' => filesize($file_path), 'url' => $file_url];
			$wpdb->update($table_name, ['status' => 'sent', 'sent_timestamp' => current_time('mysql', 1), 'brochure_data' => json_encode($brochure_data)], ['log_id' => $job->log_id]);
		} else {
			//error_log('Emn AI Cron: wp_mail() returned FALSE. Failed to send email for job ID: ' . $job->log_id);
			$wpdb->update($table_name, ['status' => 'failed', 'brochure_data' => 'Error: wp_mail() returned false.'], ['log_id' => $job->log_id]);
			if (file_exists($file_path)) {
				unlink($file_path);
			}
		}
	}
	/**
	 * สร้างหน้า Preview สำหรับ Template ของ Brochure โดยไม่ต้องรอ Cron
	 * URL: /?preview_brochure=true&p_ids=123,456
	 */

	public function brochure_preview_trigger()
	{
		if (!isset($_GET['preview_brochure']) || $_GET['preview_brochure'] !== 'true') {
			return;
		}

		if (!current_user_can('manage_options')) {
			wp_die('You do not have permission to view this page.');
		}

		if (empty($_GET['p_ids'])) {
			wp_die('Please provide product IDs. Example: ?preview_brochure=true&p_ids=123,456');
		}

		// --- [ส่วนที่ 1: ดึงข้อมูลเหมือนเดิม] ---
		$product_ids_string = sanitize_text_field($_GET['p_ids']);
		$product_ids_array = explode(',', $product_ids_string);
		$product_ids_array = array_map('intval', $product_ids_array);
		$cover_style = isset($_GET['cover']) ? absint($_GET['cover']) : 1;
		$products_data = $this->get_brochure_products_data($product_ids_array);

		if (empty($products_data)) {
			wp_die('Could not find product data for the given IDs.');
		}

		// --- [ส่วนที่ 2: แก้ไขให้สร้างเป็น PDF] ---

		// 1. ดักจับ Output ของ HTML Template
		ob_start();
		$template_path = plugin_dir_path(dirname(__FILE__)) . 'public/partials/emn-ai-brochure-template.php';
		if (file_exists($template_path)) {
			include $template_path;
		} else {
			ob_end_clean();
			wp_die('Template file not found!');
		}
		$html_content = ob_get_clean();

		if (empty($html_content)) {
			wp_die('HTML content is empty. Cannot generate PDF.');
		}

		// 2. สร้าง PDF ด้วย mPDF
		try {
			@ini_set('memory_limit', '512M'); // เพิ่มหน่วยความจำเผื่อไว้

			$mpdf = new \Mpdf\Mpdf([
				'fontdata' => [
					'inter' => [
						'L' => 'Inter-Light.ttf',
						'R' => 'Inter-Regular.ttf',
						'M' => 'Inter-Medium.ttf',
						'B' => 'Inter-Bold.ttf',
					]
				],
				'default_font' => 'inter'


			]);

			$mpdf->WriteHTML($html_content);

			// 3. ส่งไฟล์ PDF ไปที่เบราว์เซอร์โดยตรง
			// 'I' = Inline: แสดงผลในเบราว์เซอร์
			// 'D' = Download: บังคับดาวน์โหลด
			$mpdf->Output('brochure-preview.pdf', 'I');
		} catch (\Mpdf\MpdfException $e) {
			wp_die('mPDF Error: ' . $e->getMessage());
		}

		// หยุดการทำงานทั้งหมด
		exit;
	}
	// ... ข้างใน class Emn_Ai ...

	/**
	 * Cleanup old brochure files and update their status in the database.
	 * Runs daily via WP-Cron.
	 */
	public function cleanup_old_brochures()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'halal_ai_schedule_log';
		$brochure_dir = WP_CONTENT_DIR . '/halal-ai/jsons/brochures/';

		// กำหนดเวลา 15 วันที่แล้ว
		$threshold_date = date('Y-m-d H:i:s', strtotime('-15 days'));

		// ค้นหา Jobs ทั้งหมดที่ถูกส่งไปแล้วและเก่ากว่า 15 วัน
		$old_jobs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT log_id, brochure_data FROM $table_name WHERE status = %s AND sent_timestamp < %s",
				'sent',
				$threshold_date
			)
		);

		if (empty($old_jobs)) {
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				error_log('Emn AI Cleanup: No old brochures to clean up.');
			}
			return; // ไม่มีงานเก่าให้ลบ
		}

		$cleaned_count = 0;

		foreach ($old_jobs as $job) {
			$brochure_data = json_decode($job->brochure_data, true);

			// ตรวจสอบว่ามีข้อมูลไฟล์ใน log หรือไม่
			if (isset($brochure_data['name'])) {
				$file_path = $brochure_dir . $brochure_data['name'];

				// 1. ลบไฟล์ออกจากเซิร์ฟเวอร์
				if (file_exists($file_path)) {
					unlink($file_path);
				}
			}

			// 2. อัปเดตสถานะในฐานข้อมูล
			$new_brochure_data = json_encode(['message' => 'File archived and deleted on ' . current_time('mysql')]);
			$wpdb->update(
				$table_name,
				[
					'status'        => 'archived', // กำหนดสถานะใหม่
					'brochure_data' => $new_brochure_data
				],
				['log_id' => $job->log_id]
			);

			$cleaned_count++;
		}

		if ($cleaned_count > 0 && defined('WP_DEBUG') && WP_DEBUG === true) {
			error_log("Emn AI Cleanup: Successfully cleaned up and archived {$cleaned_count} old brochure files.");
		}
	}

	// ฟังก์ชัน brochure_preview_trigger() จะอยู่ต่อจากตรงนี้...

}
