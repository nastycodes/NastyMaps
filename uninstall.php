<?php
/**
 * Uninstalls plugin, sadly.
 * 
 * Copyright (C) 2025 nasty.codes
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

// delete tables
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_setting");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_template");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_map");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_map_setting");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_metabox");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}nastymaps_custom_field");

// delete posts
$wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE `post_type`='nastylocation'");

// unset installed option
delete_option('nastymaps_installed');
?>
