<?php
/**
 * These are nice to have, since we can exactly define which kind
 * of error WordPress shall display.
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

// Archive exception
class NastyMaps_Archive_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Backup exception
class NastyMaps_Backup_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Import exception
class NastyMaps_Import_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Export exception
class NastyMaps_Export_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Not found exception
class NastyMaps_Not_Found_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Not loaded exception
class NastyMaps_Not_Loaded_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Loader exception
class NastyMaps_Loader_Requirement_Exception extends Exception {
    public function __construct($message = '', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

// Loaded
define('NASTYMAPS_EXCEPTIONS_LOADED', true);
?>