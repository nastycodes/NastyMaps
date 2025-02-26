<?php
/**
 * WordPress class
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
 * NastyMaps_WordPress is the WordPress class of NastyMaps.
 * 
 * NastyMaps_WordPress adds menu entries and handles all
 * WordPress related actions.
 * 
 * @author Nasty
 * @since 1.0.0
 */
class NastyMaps_WordPress {
	/**
	 * @var NastyMaps_Controller $controller
	 */
	private $controller;

	/**
	 * @var array $custom_fields
	 */
	private $custom_fields = [];

	/**
	 * @var array $pages
	 */
	private $pages = [
		['title' => "Dashboard", 'slug' => "dashboard", 'view' => "toplevel_page_nastymaps"],
		['title' => "Maps", 'slug' => "maps", 'view' => "nastymaps_page_nastymaps-maps"],
		['title' => "Templates", 'slug' => "templates", 'view' => "nastymaps_page_nastymaps-templates"],
		['title' => "Extensions", 'slug' => "extensions", 'view' => "nastymaps_page_nastymaps-extensions"],
		['title' => "Settings", 'slug' => "settings", 'view' => "nastymaps_page_nastymaps-settings"],
		['title' => "About", 'slug' => "about", 'view' => "nastymaps_page_nastymaps-about"],
		// ['title' => "Debug", 'slug' => "debug", 'view' => "nastymaps_page_nastymaps-debug"]
	];

	/**
	 * @var string $subpage_prefix
	 */
	public $subpage_prefix = "nastymaps_page_nastymaps-";

	/**
	 * @var array $extensions
	 */
	private $extensions = [];

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct() {
		$this->controller = new NastyMaps_Controller();

		add_action('admin_menu', [$this, "extend_wordpress_menu"]);
		add_action('admin_enqueue_scripts', [$this, "enqueue_backend_assets"]);

		add_action('add_meta_boxes', [$this, "add_post_type_metaboxes"]);
		add_action('save_post', [$this, "save_post_type_metaboxes"]);

		add_action('init', [$this, "add_custom_post_type"]);

		add_action('init', [$this, "install_plugin"]);

		// plugins list page actions links.
		add_filter('plugin_action_links', [$this, 'add_plugin_action_links'], 10, 2);

		// get custom fields
		$custom_fields = nastymaps_get_custom_fields();
		$this->custom_fields = array_map(function($field) {
			return $field->unique_id;
		}, $custom_fields);

		// get extensions
		$extensions = $this->get_extensions();
		$this->init_extensions();
	}

	/**
	 * Adds the plugin action links.
	 * 
	 * @param mixed $links
	 * @param mixed $file
	 * @return void
	 */
	public function add_plugin_action_links($links, $file) {
		if ($file !== NASTYMAPS_PLUGIN_BASENAME) {
			return $links;
		}

		// Add the settings link to the plugins list page
		$link = "<a title=\"" . NASTYMAPS_STATIC_PLUGIN_NAME . " " . __("Settings", NASTYMAPS_TEXT_DOMAIN) . "\" href=\"".admin_url("admin.php?page=".NASTYMAPS_TEXT_DOMAIN."-settings")."\">" . __("Settings", NASTYMAPS_TEXT_DOMAIN) . "</a>";
		array_unshift($links, $link);

		// Add the cpt link to the plugins list page
		$link = "<a title=\"" . NASTYMAPS_STATIC_PLUGIN_NAME . " " . __("Locations", NASTYMAPS_TEXT_DOMAIN) . "\" href=\"".admin_url("edit.php?post_type=nastylocation")."\">" . __("Locations", NASTYMAPS_TEXT_DOMAIN) . "</a>";
		array_unshift($links, $link);

		return $links;
	}

	/**
	 * Adds the admin entry.
	 * 
	 * @return void
	 */
	public function extend_wordpress_menu() {
		add_menu_page(NASTYMAPS_TEXT_DOMAIN, NASTYMAPS_STATIC_PLUGIN_NAME, "manage_options", NASTYMAPS_TEXT_DOMAIN, [$this, "load_view"], NASTYMAPS_PLUGIN_URL . "assets/img/logo.svg");

		foreach ((array) $this->pages as $page) {
			$slug = NASTYMAPS_TEXT_DOMAIN;
			if ($page['slug'] !== "dashboard") {
				$slug .= '-' . $page['slug'];
			}

			add_submenu_page(
				NASTYMAPS_TEXT_DOMAIN,
				$page['title'],
				$page['title'],
				"manage_options",
				$slug,
				[$this, "load_view"]
			);
		}
	}

	/**
	 * Gets all extensions
	 * 
	 * @return array
	 * @throws NastyMaps_Not_Found_Exception
	 */
	public function get_extensions() {
		if (!empty($this->extensions)) {
			return $this->extensions;
		}

		// Get the extension file
		$extensions_file = NASTYMAPS_EXTENSIONS_PATH . "/extensions.json";
		if (!file_exists($extensions_file)) {
			throw new NastyMaps_Not_Found_Exception("Extensions file not found.");
		}

		// Get the extensions
		$extensions = json_decode(file_get_contents($extensions_file), true);

		// Assign the extensions
		$this->extensions = $extensions;
		return $this->extensions;
	}

	/**
	 * Initializes all extensions
	 * 
	 * @return void
	 */
	public function init_extensions() {
		$slug = NASTYMAPS_TEXT_DOMAIN . '-extension-';
		$menuIndex = 4;
		foreach ((array) $this->extensions as $extension) {
			if (!isset($extension['controller']) || !file_exists(NASTYMAPS_EXTENSIONS_PATH . $extension['controller'])) {
				continue;
			}

			add_action('admin_menu', function() use ($slug, $extension, $menuIndex) {
				add_submenu_page(
					NASTYMAPS_TEXT_DOMAIN,
					$extension['title'],
					"<b>EXT</b> ".$extension['title'],
					"manage_options",
					$slug . $extension['slug'],
					[$this, "load_view"],
					$menuIndex
				);
			});

			$menuIndex++;
		}
	}

	/**
	 * Enqueues the backend assets.
	 * 
	 * @param mixed $hook
	 * @return void
	 */
	public function enqueue_backend_assets($hook) {
		// Check if the current hook is in the views array
		if (in_array($hook, array_column($this->pages, 'view')) || in_array($hook, array_column($this->extensions, 'view'))) {
			// Vendor styles
			wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-bootstrap", NASTYMAPS_PLUGIN_URL . "assets/vendor/bootstrap-5.2.3/css/bootstrap.min.css");
			wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-bootstrap");
			wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-datatables", NASTYMAPS_PLUGIN_URL . "assets/vendor/datatables-bs5-2.2.2/css/datatables.min.css");
			wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-datatables");
			wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/codemirror.min.css");
			wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror");
			/* wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror-theme", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/theme/vscode-dark.css");
			wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror-theme"); */
			wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror-addon-fullscreen", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/addon/display/fullscreen.css");
			wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-codemirror-addon-fullscreen");

			// WordPress media
			wp_enqueue_media();

			// Vendor scripts
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-jquery-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/jquery-3.7.1/jquery.min.js", [], "3.7.1", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-bootstrap-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/bootstrap-5.2.3/js/bootstrap.min.js", [], "5.2.3", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-datatables-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/datatables-bs5-2.2.2/js/datatables.min.js", [], "2.2.2", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/codemirror.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-addon-comment", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/addon/comment/comment.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-addon-fullscreen", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/addon/display/fullscreen.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-clike", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/clike/clike.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-css", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/css/css.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-htmlmixed", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/htmlmixed/htmlmixed.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-javascript", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/javascript/javascript.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-php", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/php/php.min.js", [], "5.65.18", true);
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-codemirror-mode-xml", NASTYMAPS_PLUGIN_URL . "assets/vendor/codemirror-5.65.18/mode/xml/xml.min.js", [], "5.65.18", true);

			// NastyMaps scripts
			wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-scripts", NASTYMAPS_PLUGIN_URL . "assets/js/scripts.js", [], "1.0.0", true);
		}
		
		// NastyMaps styles
		wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-styles", NASTYMAPS_PLUGIN_URL . 'assets/css/styles.css');
		wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-styles");
	}

	/**
	 * Adds the custom post type.
	 * 
	 * @return void
	 */
	public function add_custom_post_type() {
		$args = [
			'labels' => [
				'name'               => __('Locations', NASTYMAPS_TEXT_DOMAIN),
				'singular_name'      => __('Location', NASTYMAPS_TEXT_DOMAIN),
				'menu_name'          => __('Locations', NASTYMAPS_TEXT_DOMAIN),
				'add_new'            => __('Add location', NASTYMAPS_TEXT_DOMAIN),
				'add_new_item'       => __('Add new location', NASTYMAPS_TEXT_DOMAIN),
				'new_item'           => __('New location', NASTYMAPS_TEXT_DOMAIN),
				'edit_item'          => __('Edit location', NASTYMAPS_TEXT_DOMAIN),
				'view_item'          => __('View location', NASTYMAPS_TEXT_DOMAIN),
				'all_items'          => __('All locations', NASTYMAPS_TEXT_DOMAIN),
				'search_items'       => __('Search for location...', NASTYMAPS_TEXT_DOMAIN),
				'parent_item_colon'  => __('Parent location:', NASTYMAPS_TEXT_DOMAIN),
				'not_found'          => __('No location found.', NASTYMAPS_TEXT_DOMAIN),
				'not_found_in_trash' => __('No location found in trash.', NASTYMAPS_TEXT_DOMAIN)
			],
			'description'        => __('Locations, which are needed for the NastyMaps-Plugin', NASTYMAPS_TEXT_DOMAIN),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_admin_bar'  => true,
			'query_var'          => true,
			'map_meta_cap' 		 => true,
			'has_archive'        => false,
			'rewrite'            => ['slug' => 'nastylocation'],
			'hierarchical'       => true,
			'menu_icon'          => 'dashicons-location',
			'menu_position'      => 15,
			'supports'           => ['title', 'revisions', 'thumbnail']
		];
	
		register_post_type('nastylocation', $args);
	}

	/**
	 * Loads the view.
	 * 
	 * @return void
	 */
	public function load_view() {
		$filtered_pages = array_filter($this->pages, function($page) {
			return $page['view'] === current_filter();
		});
		$page = reset($filtered_pages);
		if ($page) {
			$this->controller->include_view($this, $page['slug']);
			return;
		}

		$filtered_extensions = array_filter($this->extensions, function($extension) {
			return $extension['view'] == current_filter();
		});
		$extension = reset($filtered_extensions);
		if ($extension) {
			$this->controller->include_view($this, $extension['slug']);
			return;
		}
	}

	/**
	 * Adds the plugin metaboxes to the custom post type.
	 * 
	 * @return void
	 */
	public function add_post_type_metaboxes() {
		$screens = ['nastylocation'];

		$metaboxes = nastymaps_get_metaboxes();
		$metaboxes = array_map(function($metabox) {
			return [
				'unique-id' => $metabox->unique_id,
				'title'     => $metabox->name,
				'callback'  => $metabox->callback
			];
		}, $metaboxes);

		foreach ((array) $screens as $screen) {
			foreach ((array) $metaboxes as $metabox) { 
				add_meta_box($metabox['unique-id'], $metabox['title'], [$this, $metabox['callback']], $screen);
			}
		}
	}

	/**
     * Outputs the address metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_address($post) {
		$metaboxId = 1;
		$customFields = nastymaps_get_custom_fields($metaboxId);
		// echo "<pre>".print_r($customFields, true)."</pre>";
		foreach ((array) $customFields as $field) {
			?><div class="input-group meta-options">
				<label for="<?php echo $field->unique_id; ?>" class="nastymaps-label mr-1"><?php echo $field->name; ?></label>
				<input id="<?php echo $field->unique_id; ?>" type="text" name="<?php echo $field->unique_id; ?>" value="<?php echo esc_attr(get_post_meta(get_the_ID(), $field->unique_id, true)); ?>">
			</div><?php
		}
	}

	/**
     * Outputs the contact metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_contact($post) {
		$metaboxId = 2;
		$customFields = nastymaps_get_custom_fields($metaboxId);
		// echo "<pre>".print_r($customFields, true)."</pre>";
		foreach ((array) $customFields as $field) {
			?><div class="input-group meta-options">
				<label for="<?php echo $field->unique_id; ?>" class="nastymaps-label mr-1"><?php echo $field->name; ?></label>
				<input id="<?php echo $field->unique_id; ?>" type="text" name="<?php echo $field->unique_id; ?>" value="<?php echo esc_attr(get_post_meta(get_the_ID(), $field->unique_id, true)); ?>">
			</div><?php
		}
	}

	/**
     * Outputs the geolocation metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_geolocation($post) {
		$metaboxId = 3;
		$customFields = nastymaps_get_custom_fields($metaboxId);
		// echo "<pre>".print_r($customFields, true)."</pre>";
		foreach ((array) $customFields as $field) {
			?><div class="input-group meta-options">
				<label for="<?php echo $field->unique_id; ?>" class="nastymaps-label mr-1"><?php echo $field->name; ?></label>
				<input id="<?php echo $field->unique_id; ?>" type="text" name="<?php echo $field->unique_id; ?>" value="<?php echo esc_attr(get_post_meta(get_the_ID(), $field->unique_id, true)); ?>">
			</div><?php
		}	
	}

	/**
	 * Saves the metaboxes.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function save_post_type_metaboxes($post) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if ($parent_id = wp_is_post_revision($post)) {
			$post = $parent_id;
		}

		foreach ((array) $this->custom_fields as $field) { 
			if (array_key_exists($field, $_POST)) {
				update_post_meta($post, $field, sanitize_text_field($_POST[$field]));
			}
		}
	}
	
	/**
	 * Installs the plugin.
	 * 
	 * @return void
	 */
	public function install_plugin() {
		if (get_option('nastymaps_installed')) {
			return;
		}

		$this->add_plugin_db_tables();
		$this->add_plugin_db_data();

		add_option('nastymaps_installed', true);
	}

	/**
	 * Creates the database tables.
	 * 
	 * @return void
	 */
	public function add_plugin_db_tables() {
		global $wpdb;

		if (!defined('NASTYMAPS_DB_TABLES') || !is_array(NASTYMAPS_DB_TABLES) || empty(NASTYMAPS_DB_TABLES)) return;

		require_once ABSPATH . "wp-admin/includes/upgrade.php";

		foreach ((array) NASTYMAPS_DB_TABLES as $tableK => $tableV) {
			$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . $tableK . " (\n";

			foreach( (array) $tableV as $fieldK => $fieldV) {
				$sql .= (array_key_first($tableV) !== $fieldK ? ' ' : '') . "$fieldK $fieldV\n";
			}

			$sql .= ") " . $wpdb->get_charset_collate() . ";";

			dbDelta($sql);
		}
	}

	/**
	 * Fills the database tables with data.
	 * 
	 * @return void
	 */
	public function add_plugin_db_data() {
		global $wpdb;

		if (!defined('NASTYMAPS_DB_DATA') || !is_array(NASTYMAPS_DB_DATA) || empty(NASTYMAPS_DB_DATA)) return;

		foreach ((array) NASTYMAPS_DB_DATA as $tableKey => $rows) {
			foreach ((array) $rows as $row) {
				if ($tableKey == "nastymaps_setting") {
					$exists = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . $tableKey . "` WHERE `name` = '" . $row['name'] . "'");
				} else {
					$exists = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . $tableKey . "` WHERE `unique_id` = '" . $row['unique_id'] . "'");
				}

				if ($exists) {
					continue;
				}

				$wpdb->insert($wpdb->prefix . $tableKey, $row);
			}
		}
	}
}
?>
