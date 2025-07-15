<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.emonics.net/
 * @since      1.0.0
 *
 * @package    Emn_Ai
 * @subpackage Emn_Ai/admin/partials
 */
?>
<div class="wrap">
	<h1>EMN Automation</h1>
	<!-- Progress Container -->
	<div id="emn-progress-container">
		<div id="emn-progress-status">รอเริ่มต้นการสร้างไฟล์...</div>
		<div class="progress-box">
			<div id="emn-progress-bar">
				0%
			</div>
		</div>
	</div>
	<form method="post" id="emn-automation-form" action="">
		<?php wp_nonce_field('emn_automation_action', 'emn_automation_nonce'); ?>
		<select id="page_size">
			<option value="10">10</option>
			<option value="20">20</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select>
		<input type="submit" name="emn_automation_button" id="emn_automation_button" class="button button-primary" value="Generate All Product Data for AI" />
	</form>
	<?php
	// แสดงเวลาที่กดล่าสุด
	$last_run = get_option('emn_ai_last_run_time');
	if ($last_run) {
		echo '<p><strong>Last Run:</strong> ' . esc_html($last_run) . '</p>';
	}
	?>
</div>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->