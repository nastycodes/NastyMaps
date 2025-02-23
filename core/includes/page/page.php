<?php
/**
 * Page
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

// require page class
require_once NASTYMAPS_CLASSES_PATH . "/class.page.php";

// initialize it.
$nastymaps_page = new NastyMaps_Page($NASTYMAPS_PAGE_NAME, [
    'page_plugin' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN),
    'plugin_url' => NASTYMAPS_PLUGIN_URL,
    'nastymaps_title' => NASTYMAPS_STATIC_PLUGIN_NAME,
    'nastymaps_version' => NASTYMAPS_VERSION,
    'page_dashboard_active' => ($NASTYMAPS_PAGE_NAME == "dashboard" ? " nav-tab-active" : ""),
    'page_dashboard_url' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN),
    'page_dashboard_title' => __("Dashboard", NASTYMAPS_TEXT_DOMAIN),
    'page_maps_active' => ($NASTYMAPS_PAGE_NAME == "maps" ? " nav-tab-active" : ""),
    'page_maps_url' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN."-maps"),
    'page_maps_title' => __("Maps", NASTYMAPS_TEXT_DOMAIN),
    'page_templates_active' => ($NASTYMAPS_PAGE_NAME == "templates" ? " nav-tab-active" : ""),
    'page_templates_url' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN."-templates"),
    'page_templates_title' => __("Templates", NASTYMAPS_TEXT_DOMAIN),
    'page_settings_active' => ($NASTYMAPS_PAGE_NAME == "settings" ? " nav-tab-active" : ""),
    'page_settings_url' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN."-settings"),
    'page_settings_title' => __("Settings", NASTYMAPS_TEXT_DOMAIN),
    'page_extensions_active' => ($NASTYMAPS_PAGE_NAME == "extensions" ? " nav-tab-active" : ""),
    'page_extensions_url' => admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN."-extensions"),
    'page_extensions_title' => __("Extensions", NASTYMAPS_TEXT_DOMAIN),
]);
?>
