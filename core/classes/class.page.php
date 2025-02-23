<?php
/**
 * Page class
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
 * NastyMaps_Page is a class that manages the page rendering
 * 
 * @author Nasty
 * @since 1.0.0
 */
class NastyMaps_Page {
    /**
     * Twig loader
     * 
     * @var \Twig\Loader\FilesystemLoader
     */
    private $loader;

    /**
     * Twig instance
     * 
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * Page name
     * 
     * @var string
     */
    public $page_name;

    /**
     * Data array
     * 
     * @var array
     */
    public $data = [];

    /**
     * Constructor
     */
    function __construct($page_name, $data = []) {
        $this->page_name = $page_name;

        if (!empty($data)) {
            $this->data = $data;
        }

        // Autoloader für Twig schreiben
        spl_autoload_register(function ($class) {
            $prefix = 'Twig\\';
            $base_dir = NASTYMAPS_VENDOR_PATH . "/Twig-3.20.0/src/";

            // Prüfen, ob die Klasse zu Twig gehört
            if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
                return;
            }

            // Namespace entfernen und Pfad erzeugen
            $relative_class = substr($class, strlen($prefix));
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });

        // Twig initialisieren
        $this->loader = new \Twig\Loader\FilesystemLoader([
            NASTYMAPS_CORE_PATH . "/templates",
            NASTYMAPS_PAGES_PATH,
        ]);
        $this->twig = new \Twig\Environment($this->loader, [
            'cache' => false,
        ]);
        $this->twig->addFilter(new \Twig\TwigFilter('__', function ($text) {
            return __($text, NASTYMAPS_TEXT_DOMAIN);
        }));
    }

    /**
     * Render page
     * 
     * @param string $template Template to render
     * @param array $data Data to pass to template
     * @return void
     */
    public function render($template, $data = []) {
        echo $this->twig->render($template, array_merge($this->data, $data));
    } 
}
?>
