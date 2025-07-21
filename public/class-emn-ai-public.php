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
						'type' => 'integer',
					),

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
					'email'       => array(
					'required'          => true,
					'description'       => 'The email address to send the brochure to.',
					'type'              => 'string',
					'format'            => 'email',
					'validate_callback' => 'is_email'
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
	// public function handle_brochure_generation_request(WP_REST_Request $request)
	// {
	// 	$product_ids = $request->get_param('product_ids');
	// 	$email       = sanitize_email($request->get_param('email'));
	// 	$sanitized_ids = array_map('absint', $product_ids);

	// 	// WP-Cron

	// 	wp_schedule_single_event(
	// 		time(),
	// 		'emn_ai_trigger_brochure_generation', // ชื่อ Action ที่เราจะสร้างไว้รอรับ
	// 		array(
	// 			'product_ids' => $sanitized_ids,
	// 			'email' => $email,
	// 		)
	// 	);

	// 	// ตอบกลับทันทีว่า "รับเรื่องแล้ว"
	// 	return new WP_REST_Response(array(
	// 		'status'  => 202, // HTTP 202 Accepted
	// 		'message' => 'Brochure generation job has been scheduled. It will be sent to the email shortly.'
	// 	), 202);
	// }

	/**
	 * Processes the brochure generation job via WP-Cron.
	 * This is the function that does the heavy lifting.
	 * * @param   array   $product_ids    Array of sanitized product IDs.
	 * @param   string  $email          Sanitized email address.
	 * @since   1.0.0
	 */
	/**
	 * รับคำขอสร้างโบรชัวร์และนำเข้าคิวในฐานข้อมูล
	 */
	function handle_brochure_generation_request(WP_REST_Request $request)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'halal_ai_schedule_log';

		// ข้อมูลถูกตรวจสอบและกรองมาจาก 'args' ใน register_rest_route แล้ว
		$sanitized_ids = $request->get_param('product_ids');
		$recipient_email = $request->get_param('email');

		// แปลง array ของ ID ให้เป็น JSON string เพื่อเตรียมบันทึกลงฐานข้อมูล
		$product_ids_json = json_encode($sanitized_ids);

		$result = $wpdb->insert(
			$table_name,
			[
				// บันทึก JSON string ลงในคอลัมน์ใหม่
				'product_ids'     => $product_ids_json,
				'recipient_email' => $recipient_email,
				'brochure_data'   => '',
				'request_date'    => current_time('mysql', 1),
				'status'          => 'scheduled',
			]
		);

		if ($result) {
			return new WP_REST_Response(['message' => 'คำขอสร้างโบรชัวร์สำหรับ ' . count($sanitized_ids) . ' สินค้า ถูกจัดเก็บในคิวเรียบร้อยแล้ว'], 202);
		} else {
			return new WP_Error('no_jobs_created', 'ไม่สามารถสร้างงานในคิวได้', ['status' => 500]);
		}
	}
	public function process_brochure_generation_job($product_ids, $email)
	{
		error_log('--- EMN AI Brochure Job Started ---');
		error_log('Recipient Email: ' . $email);
		error_log('Product IDs: ' . print_r($product_ids, true));

		try {
			// --- START: รวบรวมข้อมูลสินค้าจากไฟล์ JSON ---
			$products_data = []; // สร้าง Array ว่างๆ เพื่อรอเก็บข้อมูลสินค้า
			$json_dir = WP_CONTENT_DIR . '/halal-ai/jsons/products/';

			foreach ($product_ids as $product_id) {
				$file_path = $json_dir . 'product_' . $product_id . '.json';
				error_log('Reading file: ' . $file_path);

				if (file_exists($file_path)) {
					$json_content = file_get_contents($file_path);
					$decoded_data = json_decode($json_content);

					// ถ้าแปลง JSON สำเร็จ ให้เพิ่มข้อมูลลงใน Array หลัก
					if (json_last_error() === JSON_ERROR_NONE) {
						$products_data[] = $decoded_data;
					} else {
						error_log('!!! Invalid JSON for product ID: ' . $product_id);
					}
				} else {
					error_log('!!! File not found for product ID: ' . $product_id);
				}
			}
			// --- END: รวบรวมข้อมูลสินค้า ---


			// --- สร้าง HTML โดยส่งข้อมูลที่รวบรวมได้ไปยังเทมเพลต ---
			ob_start();

			// ตรวจสอบก่อนว่ามีข้อมูลที่จะแสดงผลหรือไม่
			if (!empty($products_data)) {
				// ทำให้เทมเพลตสามารถเข้าถึงตัวแปร $products_data ได้
				include plugin_dir_path(__FILE__) . 'partials/emn-ai-brochure-template.php';
			} else {
				error_log('!!! No valid product data found to generate brochure.');
			}

			$html = ob_get_clean();

			if (empty(trim($html))) {
				error_log('!!! HTML content is empty. Check the template file or if product data was found.');
				return; // หยุดทำงานถ้าไม่มีเนื้อหา
			}
			// --- จบส่วนสร้าง HTML ---


			$mpdf = new \Mpdf\Mpdf(['default_font' => 'garuda']); // เพิ่มการกำหนด font ที่รองรับภาษาไทย
			$mpdf->WriteHTML($html);

			$tmp_file_path = get_temp_dir() . 'brochure-' . wp_generate_uuid4() . '.pdf';
			$mpdf->Output($tmp_file_path, \Mpdf\Output\Destination::FILE);

			if (file_exists($tmp_file_path)) {
				error_log('PDF file created successfully at: ' . $tmp_file_path);
			} else {
				error_log('!!! PDF file creation FAILED.');
				return;
			}

			$subject     = 'Your Requested Product Brochure';
			$message     = 'Please find your brochure attached.';
			$attachments = array($tmp_file_path);

			error_log('Preparing to send email to: ' . $email);
			$sent = wp_mail($email, $subject, $message, array(), $attachments);

			if ($sent) {
				error_log('wp_mail() returned TRUE. Email should be sent.');
			} else {
				error_log('!!! wp_mail() returned FALSE. Email sending failed.');
			}

			unlink($tmp_file_path);
			error_log('Temp PDF file deleted.');
			error_log('--- EMN AI Brochure Job Finished ---');
		} catch (\Exception $e) {
			error_log('!!! CRITICAL ERROR in Brochure Job: ' . $e->getMessage());
		}
	}

	public function generate_and_send_brochure_callback(WP_REST_Request $request)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'halal_ai_brochure_log';

		$product_id = (array) $request->get_param('product_ids');
		$recipient_email = sanitize_email($request->get_param('email'));

		if (empty($product_id) || !is_email($recipient_email)) {
			return new WP_Error('invalid_data', 'ข้อมูลที่ส่งมาไม่ถูกต้อง', ['status' => 400]);
		}

		// *** ส่วนที่เปลี่ยน ***
		// แทนที่จะสร้างไฟล์ทันที เราจะบันทึกคำขอลง DB
		$result = $wpdb->insert(
			$table_name,
			[
				'product_ids'      => $product_id,
				'recipient_email' => $recipient_email,
				'brochure_data'   => '', // ยังไม่มีข้อมูลไฟล์ในขั้นตอนนี้
				'request_date'    => current_time('mysql'),
				'status'          => 'scheduled', // สถานะเริ่มต้นคือ "รอคิว"
			]
		);

		if ($result === false) {
			return new WP_Error('db_error', 'ไม่สามารถบันทึกคำขอได้', ['status' => 500]);
		}

		return new WP_REST_Response(['message' => 'Brochure request has been queued successfully.'], 202); // 202 Accepted
	}
}
