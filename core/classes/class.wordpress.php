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
		['title' => "Settings", 'slug' => "settings", 'view' => "nastymaps_page_nastymaps-settings"],
		['title' => "Extensions", 'slug' => "extensions", 'view' => "nastymaps_page_nastymaps-extensions"],
		// ['title' => "Debug", 'slug' => "debug", 'view' => "nastymaps_page_nastymaps-debug"]
	];

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
		add_action('init', [$this, "add_plugin_db_tables"]);

		add_action('activated_plugin', [$this, "add_plugin_db_data"]);

		// plugins list page actions links.
		add_filter('plugin_action_links', [$this, 'add_plugin_action_links'], 10, 2);

		$custom_fields = nastymaps_get_custom_fields();
		$this->custom_fields = array_map(function($field) {
			return $field->unique_id;
		}, $custom_fields);
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
	 * Enqueues the backend assets.
	 * 
	 * @param mixed $hook
	 * @return void
	 */
	public function enqueue_backend_assets($hook) {
		// NastyMaps styles
		wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-styles", NASTYMAPS_PLUGIN_URL . 'assets/css/styles.css');
		wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-styles");

		// Check if the current hook is in the views array
		$views = array_column($this->pages, 'view');
		if (!in_array($hook, $views)) {
			return;
		}
		
		// Vendor styles
		wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-bootstrap-css", NASTYMAPS_PLUGIN_URL . "assets/vendor/bootstrap-5.2.3/css/bootstrap.min.css");
		wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-bootstrap-css");
		wp_register_style(NASTYMAPS_TEXT_DOMAIN . "-datatables-css", NASTYMAPS_PLUGIN_URL . "assets/vendor/datatables-bs5-2.2.2/css/datatables.min.css");
		wp_enqueue_style(NASTYMAPS_TEXT_DOMAIN . "-datatables-css");

		// WordPress media
		wp_enqueue_media();

		// Vendor scripts
		wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-jquery-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/jquery-3.7.1/jquery.min.js", [], "3.7.1", true);
		wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-bootstrap-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/bootstrap-5.2.3/js/bootstrap.min.js", [], "5.2.3", true);
		wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-datatables-js", NASTYMAPS_PLUGIN_URL . "assets/vendor/datatables-bs5-2.2.2/js/datatables.min.js", [], "2.2.2", true);

		// NastyMaps scripts
		wp_enqueue_script(NASTYMAPS_TEXT_DOMAIN . "-scripts", NASTYMAPS_PLUGIN_URL . "assets/js/scripts.js", [], "1.0.0", true);
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
			$this->controller->include_view($page['slug']);
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
				// check if row exists by checking for the unique_id
				$exists = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . $tableKey . "` WHERE `name` = '" . $row['name'] . "'");
				if ($exists) {
					continue;
				}

				$wpdb->insert($wpdb->prefix . $tableKey, $row);
			}
		}
	}
}
?>
