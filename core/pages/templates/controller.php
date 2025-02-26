<?php
/**
 * Templates Controller
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
$template = "/templates/view/templates.twig";

// by including the page.php file, we can use 
// the $nastymaps_page object to render the page
include NASTYMAPS_INCLUDES_PATH . "/page/page.php";

// Template related
if (isset($_POST['template']) && is_array($_POST['template']) && !empty($_POST['template'])) {
    switch (array_extract_key($_POST['template'], 'action')) {
        case 'add':
            $result = nastymaps_add_template($_POST['template']);
            break;
        case 'edit':
            $result = nastymaps_edit_template($_POST['template']);
            break;
        case 'delete':
            $result = nastymaps_delete_template($_POST['template_id']);
            break;
        default:
            $result = false;
            break;
    }

    if ($result) {
        $display['messages'][] = [
            'type' => 'success',
            'message' => __("Template saved successfully.", NASTYMAPS_TEXT_DOMAIN)
        ];
    } else {
        $display['messages'][] = [
            'type' => 'danger',
            'message' => __("Error saving template:", NASTYMAPS_TEXT_DOMAIN).print_r($_POST['template'], true)
        ];
    }
}
$templates = nastymaps_get_templates();
$display['templates'] = $templates;

$custom_fields = nastymaps_get_custom_fields();
$variables = nastymaps_get_variables();
$display['variables'] = array_merge($custom_fields, $variables);

// Render the page
$nastymaps_page->render($template, $display);
?>
