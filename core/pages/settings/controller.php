<?php
/**
 * Settings Controller
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

global $wpdb;
$display = [];

// by including the page.php file, we can use 
// the $nastymaps_page object to render the page
include NASTYMAPS_INCLUDES_PATH . "/page/page.php";

// Settings related
if (isset($_POST['settings']) && is_array($_POST['settings']) && !empty($_POST['settings'])) {
	if ($result = nastymaps_update_settings($_POST['settings'])) {
		$display['messages'][] = [
			'type' => 'success',
			'message' => __("Settings saved successfully.", NASTYMAPS_TEXT_DOMAIN)
		];
	} else {
		$display['messages'][] = [
			'type' => 'danger',
			'message' => __("Error saving settings.", NASTYMAPS_TEXT_DOMAIN)
		];
	}
}
$settings = nastymaps_get_settings();
$display['settings'] = $settings;

// Metabox related
if (isset($_POST['metabox']) && is_array($_POST['metabox']) && !empty($_POST['metabox'])) {
    switch (array_extract_key($_POST['metabox'], 'action')) {
        case 'add':
            $result = nastymaps_add_metabox($_POST['metabox']);
            break;
        case 'edit':
            $result = nastymaps_edit_metabox($_POST['metabox']);
            break;
        case 'delete':
            $result = nastymaps_delete_metabox($_POST['metabox_id']);
            break;
        default:
            $result = false;
            break;
    }

	if ($result) {
		$display['messages'][] = [
			'type' => 'success',
			'message' => __("Metabox saved successfully.", NASTYMAPS_TEXT_DOMAIN)
		];
	} else {
		$display['messages'][] = [
			'type' => 'danger',
			'message' => __("Error saving metabox.", NASTYMAPS_TEXT_DOMAIN) . "<br><br><pre class=\"mb-0\">".print_r([$wpdb->last_error, $_POST['metabox']], true)."</pre>"
		];
	}
}
$metaboxes = nastymaps_get_metaboxes();
$display['metaboxes'] = $metaboxes;

// Custom field related
if (isset($_POST['custom_field']) && is_array($_POST['custom_field']) && !empty($_POST['custom_field'])) {
	switch (array_extract_key($_POST['custom_field'], 'action')) {
        case 'add':
            $result = nastymaps_add_custom_field($_POST['custom_field']);
            break;
        case 'edit':
            $result = nastymaps_edit_custom_field($_POST['custom_field']);
            break;
        case 'delete':
            $result = nastymaps_delete_custom_field($_POST['custom_field_id']);
            break;
        default:
            $result = false;
            break;
    }

	if ($result) {
		$display['messages'][] = [
			'type' => 'success',
			'message' => __("Custom field saved successfully.", NASTYMAPS_TEXT_DOMAIN)
		];
	} else {
		$display['messages'][] = [
			'type' => 'danger',
			'message' => __("Error saving custom field.", NASTYMAPS_TEXT_DOMAIN) . "<br><br><pre class=\"mb-0\">".print_r([$wpdb->last_error, $_POST['custom_field']], true)."</pre>"
		];
	}
}
$custom_fields = nastymaps_get_custom_fields();
$display['custom_fields'] = $custom_fields;

// Render the page
$nastymaps_page->render("/settings/view/settings.twig", $display);
?>
