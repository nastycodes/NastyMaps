<?php
/**
 * Constants
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

// plugin tables
define('NASTYMAPS_DB_TABLES', [
	'nastymaps_setting' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'name'          => 'varchar(255) NOT NULL UNIQUE,',
        'value'         => 'text NOT NULL,',
        'PRIMARY'       => 'KEY (id)'
    ],
    'nastymaps_template' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'html'          => 'text NOT NULL,',
        'css'           => 'text NOT NULL,',
        'js'            => 'text NOT NULL,',
        'PRIMARY'       => 'KEY (id)'
    ],
    'nastymaps_map' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'template_id'   => 'mediumint(9) NOT NULL,',
        'name'          => 'varchar(255) NOT NULL UNIQUE,',
        'PRIMARY'       => 'KEY (id)',
        'FOREIGN'       => 'KEY (template_id)'
    ],
    'nastymaps_map_setting' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'map_id'        => 'mediumint(9) NOT NULL,',
        'name'          => 'varchar(255) NOT NULL UNIQUE,',
        'value'         => 'text NOT NULL,',
        'PRIMARY'       => 'KEY (id)',
        'FOREIGN'       => 'KEY (map_id)'
    ],
    'nastymaps_metabox' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'unique_id'     => 'varchar(128) NOT NULL UNIQUE,',
        'name'          => 'varchar(255) NOT NULL,',
        'callback'      => 'varchar(255) NOT NULL,',
        'PRIMARY'       => 'KEY (id)'
    ],
    'nastymaps_custom_field' => [
        'id'            => 'mediumint(9) NOT NULL AUTO_INCREMENT,',
        'metabox_id'    => 'mediumint(9) NOT NULL,',
        'unique_id'     => 'varchar(128) NOT NULL UNIQUE,',
        'name'          => 'varchar(255) NOT NULL,',
        'PRIMARY'       => 'KEY (id)',
        'FOREIGN'       => 'KEY (metabox_id)'
    ],
]);

// plugin tables data
define('NASTYMAPS_DB_DATA', []);

// plugin debug
define('NASTYMAPS_DEBUG', false);

// plugin version
define('NASTYMAPS_VERSION', "1.0");

// plugin text domain
define('NASTYMAPS_TEXT_DOMAIN', "nastymaps");

// plugin basename
define('NASTYMAPS_PLUGIN_BASENAME', plugin_basename(NASTYMAPS_PLUGIN));

// plugin name
define('NASTYMAPS_PLUGIN_NAME', trim(dirname(NASTYMAPS_PLUGIN_BASENAME), "/"));

// core path
define('NASTYMAPS_CORE_PATH', NASTYMAPS_PLUGIN_DIR . "/core");

// assets path
define('NASTYMAPS_ASSETS_PATH', NASTYMAPS_PLUGIN_DIR . "/assets");

// controller path
define('NASTYMAPS_CONTROLLER_PATH', NASTYMAPS_CORE_PATH . "/controller");

// WordPress path
define('NASTYMAPS_WORDPRESS_PATH', NASTYMAPS_CORE_PATH . "/wordpress");

// classes path
define('NASTYMAPS_CLASSES_PATH', NASTYMAPS_CORE_PATH . "/classes");

// view path
define('NASTYMAPS_VIEW_PATH', NASTYMAPS_CORE_PATH . "/view");

// Loaded
define('NASTYMAPS_CONSTANTS_LOADED', true);
?>
