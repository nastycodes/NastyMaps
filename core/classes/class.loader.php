<?php
/**
 * Loader class
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
 * NastyMaps_Loader is the loading class of NastyMaps.
 * 
 * NastyMaps_Loader is used to load relevant files for NastyMaps
 * to work properly.
 * 
 * @author Nasty
 * @version $Revision: 1.0 $
 * @access public
 */
class NastyMaps_Loader {
	/**
	 * @var boolean $fulfills_requirements
	 */
	private $fulfills_requirements;

	/**
	 * @var array $files
	 */
	private $files = [
		'NASTYMAPS_CONTROLLER' => NASTYMAPS_CONTROLLER_PATH . DIRECTORY_SEPARATOR . "controller.php",
		'NASTYMAPS_WORDPRESS' => NASTYMAPS_WORDPRESS_PATH . DIRECTORY_SEPARATOR . "wordpress.php"
	];

	/**
	 * @var array $userFiles
	 */
	private $userFiles;

	/**
	 * Constructor
	 * 
	 * @param array $conf
	 * @return void
	 */
	function __construct($conf = '') {
		// requirements
		if (!NastyMaps_Loader::fulfills_requirements()) {
			throw new NastyMaps_Loader_Requirement_Exception(); 
		}

		// custom files
		if ($this->fulfills_requirements && !empty($conf) && is_array($conf) && true === NastyMaps_Loader::check_files($conf['files'])) {
			$this->userFiles = $conf['files'];
		}

		// merge into files
		if ($this->fulfills_requirements && !empty($conf) && is_array($conf)) {
			$this->files = array_merge($this->files, $this->userFiles);
		}
		
		// load files
		if ($this->fulfills_requirements) {
			NastyMaps_Loader::load_files($this->files);
		}
	}

	/**
	 * Checks if all requirements are fulfilled.
	 * 
	 * @param int $flags
	 * @return boolean True if all requirements are fulfilled, otherwise false
	 */
	private function fulfills_requirements($flags = 0) {
		// constants
		if (!defined('NASTYMAPS_CONSTANTS_LOADED')) {
			throw new NastyMaps_Not_Loaded_Exception();
			return false;
		}

		// exceptions
		if (!defined('NASTYMAPS_EXCEPTIONS_LOADED')) {
			throw new NastyMaps_Not_Loaded_Exception();
			return false;
		}

		// functions
		if (!defined('NASTYMAPS_FUNCTIONS_LOADED')) {
			throw new NastyMaps_Not_Loaded_Exception();
			return false;
		}

		$this->fulfills_requirements = 0 === $flags ? true : false;
        return $this->fulfills_requirements;
    }

	/**
	 * Checks if all files exist.
	 *
	 * @param array $arr Array of files to check
	 * @return boolean True if all files exist, otherwise false
	 */
	private function check_files($arr) {
		$errors = 0;
		foreach ((array) $arr as $file) {
			if (!file_exists($file)) {
				$errors++;
			}
		}

		return $errors === 0 ? true : false;
	}

	/**
	 * Loads all files.
	 * 
	 * @param array $arr Array of files to load
	 * @return void
	 */
	private function load_files($arr) {
		foreach ((array) $arr as $file) {
			require_once $file;
		}
	}
}
?>
