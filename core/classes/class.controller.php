<?php
/**
 * Controller class
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

/**
 * NastyMaps_Controller is the controller class of NastyMaps.
 * 
 * @author Nasty
 * @version $Revision: 1.0 $
 * @access public
 */
class NastyMaps_Controller {
	/**
	 * Include a view file
	 * 
	 * @param string $slug The slug of the view
	 * @param array $files The files to include
	 * @return void
	 */
	private static function real_include($slug, $files) {
		global $NASTYMAPS_PAGE_NAME; $NASTYMAPS_PAGE_NAME = $slug;

		if (is_array($files)) {
			foreach ((array) $files as $file) {
				if (!file_exists($file)) {
					throw new NastyMaps_Not_Found_Exception();
				}
				include $file;
			}
		}
	}

	/**
	 * Include a view
	 * 
	 * @param string $slug The slug of the view
	 * @return void
	 */
	public static function include_view($slug) {
		if (!$slug) {
			return;
		}

		self::real_include($slug, [
			implode(DIRECTORY_SEPARATOR, [NASTYMAPS_PAGES_PATH, $slug, "controller.php"]),
		]);
	}
}
?>
