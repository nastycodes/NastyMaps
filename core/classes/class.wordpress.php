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
 * Example usage is documented, hence why it is not found here.
 * 
 * @author Nasty
 * @version $Revision: 1.0 $
 * @access public
 */
class NastyMaps_WordPress {
	/**
	 * @var NastyMaps_Controller $controller
	 */
	private $controller;

	/**
	 * @var array $custom_fields
	 */
	private $custom_fields = array(
		'nastymaps-address-company',
		'nastymaps-address-company-extra',
		'nastymaps-address-street',
		'nastymaps-address-zipcode',
		'nastymaps-address-city',
		'nastymaps-address-country',

		'nastymaps-contact-phone',
		'nastymaps-contact-fax',
		'nastymaps-contact-email',
		'nastymaps-contact-web',
		
		'nastymaps-general-latitude',
		'nastymaps-general-longitude'
	);

	/**
	 * @var array $pages
	 */
	private $pages = [
		['title' => 'Dashboard', 'slug' => 'dashboard', 'view' => 'toplevel_page_nastymaps'],
		['title' => 'Maps', 'slug' => 'maps', 'view' => 'nastymaps_page_nastymaps-maps'],
		['title' => 'Templates', 'slug' => 'templates', 'view' => 'nastymaps_page_nastymaps-templates'],
		['title' => 'Settings', 'slug' => 'settings', 'view' => 'nastymaps_page_nastymaps-settings'],
		['title' => 'Extensions', 'slug' => 'extensions', 'view' => 'nastymaps_page_nastymaps-extensions'],
		// ['title' => 'Debug', 'slug' => 'debug', 'view' => 'nastymaps_page_nastymaps-debug']
	];

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function __construct() {
		$this->controller = new NastyMaps_Controller();
		
		add_shortcode(NASTYMAPS_TEXT_DOMAIN . '-get-shortcodes', 'get_shortcodes');

		add_action('admin_menu', array($this, 'admin_entry'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_backend_assets'));

		add_action('add_meta_boxes', array($this, 'add_post_type_metaboxes'));
		add_action('save_post', array($this, 'save_metaboxes'));

		add_action('init', array($this, 'add_custom_post_type'));
		add_action('init', array($this, 'db'));
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
	 * Adds the admin entry.
	 * 
	 * @return void
	 */
	public function admin_entry() {
		add_menu_page('nastymaps', 'NastyMaps', 'manage_options', 'nastymaps', array($this, 'load_view'), NASTYMAPS_PLUGIN_URL . 'assets/img/logo.svg');

		foreach ((array) $this->pages as $page) {
			$slug = "nastymaps";
			if ($page['slug'] !== 'dashboard') {
				$slug .= '-' . $page['slug'];
			}

			add_submenu_page(
				'nastymaps',
				$page['title'],
				$page['title'],
				'manage_options',
				$slug,
				[$this, 'load_view']
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
		wp_register_style('nastymaps-styles', NASTYMAPS_PLUGIN_URL . 'assets/css/styles.css');
		wp_enqueue_style('nastymaps-styles');

		$views = array_column($this->pages, 'view');
		
		if (!in_array($hook, $views)) {
			return;
		}
		
		wp_register_style('nastymaps-bootstrap-css', NASTYMAPS_PLUGIN_URL . 'assets/vendor/bootstrap-5.2.3/css/bootstrap.min.css');
		wp_enqueue_style('nastymaps-bootstrap-css');
		wp_register_style('nastymaps-datatables-css', NASTYMAPS_PLUGIN_URL . 'assets/vendor/datatables-bs5-2.2.2/css/datatables.min.css');
		wp_enqueue_style('nastymaps-datatables-css');

		wp_enqueue_media();

		wp_enqueue_script('nastymaps-jquery-js', NASTYMAPS_PLUGIN_URL . 'assets/vendor/jquery-3.7.1/jquery.min.js', [], '3.7.1', true);
		wp_enqueue_script('nastymaps-bootstrap-js', NASTYMAPS_PLUGIN_URL . 'assets/vendor/bootstrap-5.2.3/js/bootstrap.min.js', [], '5.2.3', true);
		wp_enqueue_script('nastymaps-datatables-js', NASTYMAPS_PLUGIN_URL . 'assets/vendor/datatables-bs5-2.2.2/js/datatables.min.js', [], '2.2.2', true);

		wp_enqueue_script('nastymaps-scripts', NASTYMAPS_PLUGIN_URL . 'assets/js/scripts.js', [], '1.0.0', true);
	}

	/**
	 * Adds the custom post type.
	 * 
	 * @return void
	 */
	public function add_custom_post_type() {
		$args = [
			'labels' => [
				'name'               => __('Standorte', NASTYMAPS_TEXT_DOMAIN),
				'singular_name'      => __('Standort', NASTYMAPS_TEXT_DOMAIN),
				'menu_name'          => __('Standorte', NASTYMAPS_TEXT_DOMAIN),
				'add_new'            => __('Standort hinzufügen', NASTYMAPS_TEXT_DOMAIN),
				'add_new_item'       => __('Neuen Standort hinzufügen', NASTYMAPS_TEXT_DOMAIN),
				'new_item'           => __('Neuer Standort', NASTYMAPS_TEXT_DOMAIN),
				'edit_item'          => __('Standort bearbeiten', NASTYMAPS_TEXT_DOMAIN),
				'view_item'          => __('Standort ansehen', NASTYMAPS_TEXT_DOMAIN),
				'all_items'          => __('Alle Standorte', NASTYMAPS_TEXT_DOMAIN),
				'search_items'       => __('Nach Standort suchen', NASTYMAPS_TEXT_DOMAIN),
				'parent_item_colon'  => __('Übergeordneter Standort:', NASTYMAPS_TEXT_DOMAIN),
				'not_found'          => __('Keinen Standort gefunden.', NASTYMAPS_TEXT_DOMAIN),
				'not_found_in_trash' => __('Keinen Standort im Papierkorb gefunden.', NASTYMAPS_TEXT_DOMAIN)
			],
			'description'        => __('Standorte, welche für das NastyMaps-Plugin benötigt werden', NASTYMAPS_TEXT_DOMAIN),
			'public'             => true,
			'publicly_queryable' => true,
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
	 * Adds the plugin metaboxes to the custom post type.
	 * 
	 * @return void
	 */
	public function add_post_type_metaboxes() {
		$screens = ['nastylocation'];

		$metaboxes = [
			'location-address' => [
				'unique-id' => 'location-address',
				'title'     => __('Adresse', NASTYMAPS_TEXT_DOMAIN),
				'callback'  => [$this, 'metabox_location_address']
			],
			'location-contact' => [
				'unique-id' => 'location-contact',
				'title' => __('Kontakt', NASTYMAPS_TEXT_DOMAIN),
				'callback' => [$this, 'metabox_location_contact']
			],
			'location-general' => [
				'unique-id' => 'location-general',
				'title' => __('Koordinaten', NASTYMAPS_TEXT_DOMAIN),
				'callback' => [$this, 'metabox_location_general']
			]
		];

		foreach ((array) $screens as $screen) {
			foreach ((array) $metaboxes as $metabox) { 
				add_meta_box($metabox['unique-id'], $metabox['title'], $metabox['callback'], $screen);
			}
		}
	}

	/**
     * Outputs the general metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_general($post) {
		?><div class="input-group meta-options">
			<label for="nastymaps-general-latitude" class="nastymaps-label mr-1">Latitude</label>
			<input id="nastymaps-general-latitude" type="text" name="nastymaps-general-latitude" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-general-latitude', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-general-longitude" class="nastymaps-label mr-1">Longitude</label>
			<input id="nastymaps-general-longitude" type="text" name="nastymaps-general-longitude" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-general-longitude', true)); ?>">
		</div><?php
	}

	/**
     * Outputs the address metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_address($post) {
		?><div class="input-group meta-options">
			<label for="nastymaps-address-company" class="nastymaps-label mr-1">Name</label>
			<input id="nastymaps-address-company" type="text" name="nastymaps-address-company" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-company', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-address-company-extra" class="nastymaps-label mr-1">Zusatz</label>
			<input id="nastymaps-address-company-extra" type="text" name="nastymaps-address-company-extra" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-company-extra', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-address-street" class="nastymaps-label mr-1">Straße</label>
			<input id="nastymaps-address-street" type="text" name="nastymaps-address-street" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-street', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-address-zipcode" class="nastymaps-label mr-1">PLZ</label>
			<input id="nastymaps-address-zipcode" type="text" name="nastymaps-address-zipcode" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-zipcode', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-address-city" class="nastymaps-label mr-1">Stadt</label>
			<input id="nastymaps-address-city" type="text" name="nastymaps-address-city" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-city', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-address-country" class="nastymaps-label mr-1">Land</label>
			<input id="nastymaps-address-country" type="text" name="nastymaps-address-country" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-address-country', true)); ?>">
		</div><?php
	}

	/**
     * Outputs the contact metabox.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function metabox_location_contact($post) {
		?><div class="input-group meta-options">
			<label for="nastymaps-contact-phone" class="nastymaps-label mr-1">Telefon</label>
			<input id="nastymaps-contact-phone" type="text" name="nastymaps-contact-phone" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-contact-phone', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-contact-fax" class="nastymaps-label mr-1">Fax</label>
			<input id="nastymaps-contact-fax" type="text" name="nastymaps-contact-fax" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-contact-fax', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-contact-email" class="nastymaps-label mr-1">E-Mail</label>
			<input id="nastymaps-contact-email" type="text" name="nastymaps-contact-email" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-contact-email', true)); ?>">
		</div>
		<div class="input-group meta-options">
			<label for="nastymaps-contact-web" class="nastymaps-label mr-1">Web</label>
			<input id="nastymaps-contact-web" type="text" name="nastymaps-contact-web" value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'nastymaps-contact-web', true)); ?>">
		</div><?php
	}

	/**
	 * Saves the metaboxes.
	 * 
	 * @param mixed $post
	 * @return void
	 */
	public function save_metaboxes($post) {
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
	public function db() {
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
}
?>
