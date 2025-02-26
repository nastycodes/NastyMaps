<?php
/**
 * Maps Controller
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
$template = "/maps/view/maps.twig";

// by including the page.php file, we can use 
// the $nastymaps_page object to render the page
include NASTYMAPS_INCLUDES_PATH . "/page/page.php";

// Maps related
if (isset($_POST['map']) && is_array($_POST['map']) && !empty($_POST['map'])) {
    switch (array_extract_key($_POST['map'], 'action')) {
        case 'add':
            $result = nastymaps_add_map($_POST['map']);
            break;
        case 'edit':
            $result = nastymaps_edit_map($_POST['map']);
            break;
        case 'delete':
            $result = nastymaps_delete_map($_POST['map_id']);
            break;
        default:
            $result = false;
            break;
    }

    if ($result) {
        $display['messages'][] = [
            'type' => 'success',
            'message' => __("Map saved successfully.", NASTYMAPS_TEXT_DOMAIN)
        ];
    } else {
        $display['messages'][] = [
            'type' => 'danger',
            'message' => __("Error saving map:", NASTYMAPS_TEXT_DOMAIN).print_r($_POST['map'], true)
        ];
    }
}
$maps = nastymaps_get_maps();
$display['maps'] = $maps;

// Render the page
$nastymaps_page->render($template, $display);
?>
