<?php
/**
 * Extensions Controller
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

$extensions = $NASTYMAPS_WP_CLASS->get_extensions();
$display['extensions'] = $extensions;

// Render the page
$nastymaps_page->render("/extensions/view/extensions.twig", $display);
?>
