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

		register_rest_route('halal-ai/v1', '/brochures/generate', [
			'methods'  => 'POST',
			'callback' => array($this, 'handle_brochure_generation_request'),
			// ใช้ฟังก์ชัน permission เดิมที่คุณมีอยู่แล้ว
			'permission_callback' => array($this, 'api_key_permission'),
			'args' => array(
				'product_ids' => array(
					'required'          => true,
					'description'       => 'An array of product IDs.',
					'type'              => 'array',
					'items'             => array(
						'type' => 'array',
					),
					/**
					 * UPDATED VALIDATION CALLBACK
					 * เราจะเปลี่ยนให้มันคืนค่าเป็น WP_Error object ซึ่งจะให้ข้อมูลที่ละเอียดกว่า
					 */
					'validate_callback' => function ($param, $request, $key) {
						// เช็คก่อนเลยว่าค่าที่ได้มาเป็น Array หรือไม่
						if (!is_array($param)) {
							// ถ้าไม่ใช่ Array ให้ส่ง Error ที่เจาะจงกลับไป
							return new WP_Error(
								'rest_invalid_param',
								'The product_ids parameter is not in a valid array format. Please ensure the Content-Type header is set to application/json.'
							);
						}
						// เช็คว่า Array ที่ส่งมาว่างเปล่าหรือไม่
						if (empty($param)) {
							return new WP_Error('rest_invalid_param', 'The product_ids array cannot be empty.');
						}
						// ถ้าผ่านทุกอย่าง แสดงว่าถูกต้อง
						return true;
					}
				),
				'email' => array(
					// ... การตั้งค่า email เหมือนเดิม ...
				),
			),
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
		if ($post->post_type !== 'product') return;
		if (is_null($post)) {
			$post = get_post($post_id);
		}

		// ป้องกัน autosave / revision
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (get_post_status($post_id) !== 'publish') {
			return;
		}


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
		if ($new_status === 'publish' && $old_status !== 'publish') {
			$this->emn_json_generate_single($post->ID);
		}
		// กรณีที่ 2: สินค้าถูกยกเลิกการเผยแพร่ หรือย้ายไปถังขยะ (เปลี่ยนจาก publish เป็นสถานะอื่น)
		elseif ($new_status !== 'publish' && $old_status === 'publish') {
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
	/**
	 * Handles the incoming API request to generate a brochure.
	 * This function only schedules a background job and returns a response immediately.
	 * * @param   WP_REST_Request     $request    The request object.
	 * @return  WP_REST_Response                The response object.
	 * @since   1.0.0
	 */
	public function handle_brochure_generation_request(WP_REST_Request $request)
	{
		$product_ids = $request->get_param('product_ids');
		$email       = sanitize_email($request->get_param('email'));
		$sanitized_ids = array_map('absint', $product_ids);

		// WP-Cron

		wp_schedule_single_event(
			time(),
			'emn_ai_trigger_brochure_generation', // ชื่อ Action ที่เราจะสร้างไว้รอรับ
			array(
				'product_ids' => $sanitized_ids,
				'email' => $email,
			)
		);
		wp_cron();
		// ตอบกลับทันทีว่า "รับเรื่องแล้ว"
		return new WP_REST_Response(array(
			'status'  => 202, // HTTP 202 Accepted
			'message' => 'Brochure generation job has been scheduled. It will be sent to the email shortly.'
		), 202);
	}

	/**
	 * Processes the brochure generation job via WP-Cron.
	 * This is the function that does the heavy lifting.
	 * * @param   array   $product_ids    Array of sanitized product IDs.
	 * @param   string  $email          Sanitized email address.
	 * @since   1.0.0
	 */
	public function process_brochure_generation_job($product_ids, $email)
	{
		// สายลับ #1: เช็คว่า Cron ทำงานและได้รับค่ามาถูกต้องหรือไม่
		error_log('--- EMN AI Brochure Job Started ---');
		error_log('Recipient Email: ' . $email);
		error_log('Product IDs: ' . print_r($product_ids, true));

		try {
			// ... โค้ดสร้าง HTML ...

			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($html);

			$tmp_file_path = get_temp_dir() . 'brochure-' . wp_generate_uuid4() . '.pdf';
			$mpdf->Output($tmp_file_path, \Mpdf\Output\Destination::FILE);

			// สายลับ #2: เช็คว่าไฟล์ PDF ถูกสร้างขึ้นจริงหรือไม่
			if (file_exists($tmp_file_path)) {
				error_log('PDF file created successfully at: ' . $tmp_file_path);
			} else {
				error_log('!!! PDF file creation FAILED. Path: ' . $tmp_file_path);
				return; // หยุดทำงานถ้าสร้างไฟล์ไม่ได้
			}

			$subject     = 'Your Requested Product Brochure';
			$message     = 'Please find your brochure attached.';
			$attachments = array($tmp_file_path);

			// สายลับ #3: เช็คก่อนส่ง
			error_log('Preparing to send email to: ' . $email);

			// ส่งอีเมล
			$sent = wp_mail($email, $subject, $message, array(), $attachments);

			// สายลับ #4: เช็คผลลัพธ์หลังส่ง
			if ($sent) {
				error_log('wp_mail() returned TRUE. Email should be sent.');
			} else {
				error_log('!!! wp_mail() returned FALSE. Email sending failed.');
			}

			unlink($tmp_file_path);
			error_log('Temp PDF file deleted.');
			error_log('--- EMN AI Brochure Job Finished ---');
		} catch (\Exception $e) {
			// สายลับ #5: ดักจับ Error ที่อาจเกิดขึ้น
			error_log('!!! CRITICAL ERROR in Brochure Job: ' . $e->getMessage());
		}
	}
}
