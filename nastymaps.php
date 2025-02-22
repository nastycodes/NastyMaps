<?php
/**
 * Plugin Name: NastyMaps
 * Description: NastyMaps is a WordPress-Plugin that allows you to create and manage maps with ease.
 * Author: Justin Lochner
 * Author URI: https://nasty.codes
 * Version: 1.1
 * Text Domain: nastymaps
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

// exit if accessed directly
defined('ABSPATH') || exit();

// static plugin name
define('NASTYMAPS_STATIC_PLUGIN_NAME', "NastyMaps");

// plugin file
define('NASTYMAPS_PLUGIN', __FILE__);

// plugin directory
define('NASTYMAPS_PLUGIN_DIR', untrailingslashit(dirname(NASTYMAPS_PLUGIN)));

// plugin url
define('NASTYMAPS_PLUGIN_URL', plugin_dir_url(NASTYMAPS_PLUGIN));

// require constants
require_once NASTYMAPS_PLUGIN_DIR . "/constants.php";

// require exceptions
require_once NASTYMAPS_PLUGIN_DIR . "/exceptions.php";

// require functions
require_once NASTYMAPS_PLUGIN_DIR . "/functions.php";

// require loader
require_once NASTYMAPS_PLUGIN_DIR . "/loader.php";
?>
