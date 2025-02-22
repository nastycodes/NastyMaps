<?php
/**
 * Header
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
?>
<div class="header-wrap nastymaps-wrap">
	<div class="title">
		<div class="outer-title-wrap">
			<a class="nastymaps-logo" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>">
				<img class="logo-img" src="<?php echo NASTYMAPS_PLUGIN_URL; ?>assets/img/logo.svg" alt="Händlersuche Logo" draggable="false">
			</a>
			<div class="title-wrap">
				<div class="inner-title-wrap mb-2">
					<h1 class="nastymaps-title"><?php echo NASTYMAPS_STATIC_PLUGIN_NAME; ?></h1>
					<span class="nastymaps-version"><?php echo NASTYMAPS_VERSION; ?></span>
				</div>
				<p class="description">Made with <img class="emoji" src="https://s.w.org/images/core/emoji/14.0.0/svg/2764.svg" alt="❤" draggable="false"> by <a href="https://nasty.codes" class="text-light text-decoration-none" target="_blank">nasty.codes</a></p>
			</div>
		</div>
		<div class="link-wrap">
			<a class="support-link" href="mailto:info@nasty.codes">Feedback / Support</a>
		</div>
	</div>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "dashboard" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>"><?php echo __("Dashboard", NASTYMAPS_TEXT_DOMAIN); ?></a>
		<a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "maps" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>-maps"><?php echo __("Maps", NASTYMAPS_TEXT_DOMAIN); ?></a>
		<a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "templates" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>-templates"><?php echo __("Templates", NASTYMAPS_TEXT_DOMAIN); ?></a>
		<a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "settings" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>-settings"><?php echo __("Settings", NASTYMAPS_TEXT_DOMAIN); ?></a>
		<a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "extensions" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>-extensions"><?php echo __("Extensions", NASTYMAPS_TEXT_DOMAIN); ?></a>
		<!-- <a class="nav-tab<?php echo ($NASTYMAPS_PAGE_NAME == "customization" ? " nav-tab-active" : ""); ?>" href="<?php echo admin_url("admin.php"); ?>?page=<?php echo NASTYMAPS_TEXT_DOMAIN; ?>-customization"><?php echo __("Customization", NASTYMAPS_TEXT_DOMAIN); ?></a> -->
	</h2>
</div>
<div id="toasts"></div>