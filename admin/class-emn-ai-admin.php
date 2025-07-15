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
 */
class Emn_Ai_Admin
{

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/emn-ai-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/emn-ai-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'emn_ai_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('emn_automation_nonce')
        ));
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

    public function emn_ai_settings_page()
    {
        include_once plugin_dir_path(__FILE__) . 'partials/emn-ai-admin-display.php';
    }

    /**
     * AJAX action to get the total number of products.
     */
    public function emn_ajax_get_total_products()
    {
        check_ajax_referer('emn_automation_nonce', 'nonce');

        $query = new WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => -1,
        ]);

        wp_send_json_success(['total' => $query->post_count]);
    }

    /**
     * AJAX action to process a single batch of products.
     */
    public function emn_ajax_process_batch()
    {
        check_ajax_referer('emn_automation_nonce', 'nonce');

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $page_size = isset($_POST['page_size']) ? intval($_POST['page_size']) : 100;

        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'fields' => 'ids',
            'posts_per_page' => $page_size,
            'paged' => $page,
            'orderby' => 'ID',
            'order' => 'ASC',
        ];

        $query = new WP_Query($args);
        $processed_count = 0;

        if ($query->have_posts()) {
            foreach ($query->posts as $product_id) {
                $this->emn_json_generate_single($product_id);
                $processed_count++;
            }
        }
        wp_reset_postdata();
        
        // Update last run time at the end of the process (the JS will tell us when)
        if (isset($_POST['is_last_batch']) && $_POST['is_last_batch'] === 'true') {
            update_option('emn_ai_last_run_time', current_time('mysql'));
        }

        wp_send_json_success(['processed' => $processed_count]);
    }

    public function emn_json_generate_single($product_id)
    {
		try {
			$product = wc_get_product($product_id);
			if (!$product) return;

			$post_modified_time = get_post_modified_time('U', true, $product_id); // GMT timestamp
			$output_dir = WP_CONTENT_DIR . '/halal-ai/jsons/products/';
			$filename = $output_dir . 'product_' . $product_id . '.json';

			if (file_exists($filename)) {
				$file_modified_time = filemtime($filename);
				if ($file_modified_time >= $post_modified_time) {
					return;
				}
			}

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
				wp_mkdir_p($output_dir);
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
			"industry_specific_attributes" => ["type" => "","shape" => "","packaging" => "",],
			"other_attributes" => ["storage_type" => "","specification" => "","manufacturer" => "","ingredients" => "","address" => "","place_of_origin" => "","product_type" => "","color" => "","feature" => "","brand_name" => "","shelf_life" => "","hs_code" => "",],
			"packaging_and_delivery" => ["packaging_details" => "","port" => "","selling_units" => "","single_package_size" => "","single_gross_weight" => "",],
			"supply_ability" => ["supply_ability" => "",],
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
}