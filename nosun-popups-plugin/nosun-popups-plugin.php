<?php
/*
Plugin Name: NOSUN Popups
Plugin URI: https://github.com/mjnosun/nosun-popups-plugin
Description: Custom Popups.
Version: 0.0.5
Author: NOSUN MJ
Author URI: https://www.no-sun.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nosun-popups-plugin
Domain Path: /languages
Requires at least: 6.5
Tested up to: 6.7.2
Requires PHP: 7.4
Requires Plugins: advanced-custom-fields-pro
*/

/**
 * constants
 */
define( 'PLUGIN_VERSION', '0.0.3' );
define( 'PLUGIN_NAMESPACE', 'nosun-popups-plugin' );

/**
 * enqueue FRONTEND styles & scripts
 */
add_action( 'wp_enqueue_scripts', 'nos_popups_enqueue' );
function nos_popups_enqueue() {
	// styles
	wp_enqueue_style('nos-popups-plugin-css', plugin_dir_url( __FILE__ ) . 'assets/css/popups-main.css', array(), PLUGIN_VERSION, 'all');
	if (!wp_style_is('grid', 'enqueued')) {
		wp_enqueue_style('fallback-grid', plugin_dir_url(__FILE__) . 'assets/css/fallback-grid.css', array(), PLUGIN_VERSION, 'all');
	}
	// scripts
	wp_enqueue_script('nos-popups-plugin-js', plugin_dir_url( __FILE__ ) . 'assets/js/popups-main.js', array('jquery'), PLUGIN_VERSION, array('in_footer' => true));
}
/**
 * enqueue BACKEND styles & scripts
 */
function nos_popups_backend_enqueue() {
	// styles
	wp_enqueue_style('popups-backend-css', plugin_dir_url( __FILE__ ) . 'assets/css/popups-backend.css', false, PLUGIN_VERSION, 'all');
	// scripts
	wp_enqueue_script('nos-popups-backend-js', plugin_dir_url( __FILE__ ) . 'assets/js/popups-backend.js', array('jquery'), PLUGIN_VERSION, array('in_footer' => true));
}
add_action( 'admin_enqueue_scripts', 'nos_popups_backend_enqueue' );

/**
 * init
 */
function nos_popups_init() {
	/**
	 * register popups post type
	 */
	register_post_type(
		'nos_popups', 
		array(	
			'label' => 'Popups',
			'description' => 'Popups',
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'post',
			'exclude_from_search' => true,
			'hierarchical' => true,
			'rewrite' => array('slug' => 'nos_popups'),
			'query_var' => true,
			'has_archive' => false,
			'show_in_nav_menus' => false,
			'menu_icon' => 'dashicons-pressthis',
			'supports' => array(
				'title',
				'revisions',
				'custom-fields',
			),
			'labels' => array (
				'name' => 'Popups',
				'singular_name' => 'Popup',
				'menu_name' => 'Popups',
				'add_new' => 'Popups anlegen',
				'add_new_item' => 'Neues Popup anlegen',
				'edit' => 'Popups bearbeiten',
				'edit_item' => 'Popup bearbeiten',
				'new_item' => 'Neues Popup',
				'view' => 'Popups anzeigen',
				'view_item' => 'Popup anzeigen',
				'search_items' => 'Popups durchsuchen',
				'not_found' => 'Keine Popups gefunden',
				'not_found_in_trash' => 'Keine Popups im Papierkorb gefunden',
				'parent' => 'Eltern Popup'
			)
		)
	);
	
	if (class_exists('WPSEO_Options')) {
		
		/**
		* Exclude nos_popups cpt from XML sitemaps.
		*/
		function sitemap_exclude_post_type( $excluded, $post_type ) {
			return $post_type === 'nos_popups';
		}
		add_filter( 'wpseo_sitemap_exclude_post_type', 'sitemap_exclude_post_type', 10, 2 );
		
		/**
		 * remove YOAST SEO Meta Box for nos_popups cpts
		 */
		function my_remove_wp_seo_meta_box() {
			remove_meta_box('wpseo_meta', 'nos_popups', 'normal');
		}
		add_action('add_meta_boxes', 'my_remove_wp_seo_meta_box', 100);
	}
	
}
add_action( 'init', 'nos_popups_init');

/**
 * custom single post template if not found in theme folders
 */
function load_popups_single_template( $template ) {
	global $post;
	if ( 'nos_popups' === $post->post_type && locate_template( array( 'single-nos_popups.php' ) ) !== $template ) {
		return plugin_dir_path( __FILE__ ) . 'single-nos_popups.php';
	}
	return $template;
}
add_filter( 'single_template', 'load_popups_single_template' );

/**
 * get current language slug
 */
if (!function_exists('get_current_language_slug')) {
	function get_current_language_slug() {
		// Check if Polylang is active and use it to get the current language slug.
		if (function_exists('pll_current_language')) {
			return pll_current_language();  // Returns "de", "en", etc.
		}

		// Check if WPML is active and use it to get the current language slug.
		if (defined('ICL_SITEPRESS_VERSION') && function_exists('apply_filters')) {
			return apply_filters('wpml_current_language', NULL);  // Returns "de", "en", etc.
		}

		// Fallback to the site's default language when no plugin is active.
		// Retrieves the language code set in WordPress settings (e.g., "en_US").
		$site_language = get_locale();
		
		// Extract the two-character language code.
		return substr($site_language, 0, 2);
	}
}

/**
 * add backend admin columns
 */
if ( class_exists('ACF') ) {
	/**
	 * include acf fields
	 */
	require_once(plugin_dir_path( __FILE__ ) . 'popup-acf-fields.php');
	
	/**
	 * Add custom columns to popups post list admin column
	 */
	function nosun_posts_column_views( $columns ) {
		$columns['nts_popup_status'] = __('Popup Status', PLUGIN_NAMESPACE);
		$columns['nts_popup_from'] = __('Aktiv von', PLUGIN_NAMESPACE);
		$columns['nts_popup_to'] = __('Aktiv bis', PLUGIN_NAMESPACE);
		return $columns;
	}
	
	/**
	 * input the data into popup admin columns
	 */
	function nosun_posts_custom_column_manage_nos_popups( $column ) {
		$post_id = get_the_ID();
		$popup_aktivieren = get_field('nts_pop_active', $post_id);
		$aktiv_von = get_field('nts_pop_timeframe', $post_id)['nts_pop_start_date'];
		$aktiv_bis = get_field('nts_pop_timeframe', $post_id)['nts_pop_end_date'];
	
		if ( $column === 'nts_popup_status' ) {
			if ( $popup_aktivieren ) {
				$today = date('Y-m-d');
				if ( get_field('nts_pop_timeframe', $post_id) ) {
					$aktiv_von = get_field('nts_pop_timeframe', $post_id)['nts_pop_start_date'];
					$aktiv_bis = get_field('nts_pop_timeframe', $post_id)['nts_pop_end_date'];
				} else {
					$aktiv_von = $aktiv_bis = false;
				}
				$is_active = true;
				
				// If both dates are set
				if ( $popupDateBegin && $popupDateEnd ) {
					// Today is between aktiv_von and aktiv_bis (inclusive)
					if ( $today >= $popupDateBegin && $today <= $popupDateEnd ) {
						echo '<div style="color:#48C572;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#48C572" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> <span>Aktiv</span></div>';
					}
					// Today is before aktiv_von
					elseif ( $today < $popupDateBegin ) {
						echo '<div style="color:#FCC130;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FCC130" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span>Geplant</span></div>';
					}
					// Today is after aktiv_bis
					elseif ( $today > $popupDateEnd ) {
						echo '<div style="color:#C84630;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C84630" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> <span>Inaktiv</span></div>';
						$is_active = false;
					}
				}
				// Only aktiv_von is set, no aktiv_bis
				elseif ( $popupDateBegin && !$popupDateEnd ) {
					if ( $today >= $popupDateBegin ) {
						echo '<div style="color:#48C572;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#48C572" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> <span>Aktiv</span></div>';
					} else {
						echo '<div style="color:#FCC130;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FCC130" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> <span>Geplant</span></div>';
					}
				}
				// Only aktiv_bis is set, no aktiv_von
				elseif ( !$popupDateBegin && $popupDateEnd ) {
					if ( $today <= $popupDateEnd ) {
						echo '<div style="color:#48C572;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#48C572" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> <span>Aktiv</span></div>';
					} else {
						echo '<div style="color:#C84630;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C84630" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> <span>Inaktiv</span></div>';
						$is_active = false;
					}
				}
				// No dates set but popup_aktivieren is true
				elseif ( !$popupDateBegin && !$popupDateEnd ) {
					echo '<div style="color:#48C572;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#48C572" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> <span>Aktiv</span></div>';
				}
			} else {
				echo '<div style="color:#C84630;display:inline-flex;align-items:center;grid-gap:10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C84630" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> <span>Inaktiv</span></div>';
				$is_active = false;
			}
			if ( $is_active == true ) {
				$button_text = 'Deaktivieren';
				$button_class = 'button deactivate';
			} else {
				$button_text = 'Aktivieren';
				$button_class = 'button activate';
			}
			
			echo '<div><a href="' . admin_url('edit.php?post_type=nos_popups&toggle_activate=' . $post_id . '&_wpnonce=' . wp_create_nonce('toggle_activate_' . $post_id)) . '" class="' . $button_class . ' nos_popup_quick_btn"><span class="toggle-button-text">' . $button_text . '</span><div class="toggle-knob-holder"><div class="toggle-knob"></div></div></a></div>';
		} elseif ( $column === 'nts_popup_from' ) {
			$popupStartDateOutput = $aktiv_von ? date('d.m.Y', strtotime($aktiv_von)) : '-';
			echo ($aktiv_von && $popup_aktivieren ? $popupStartDateOutput : '-');
		} elseif ( $column === 'nts_popup_to' ) {
			$popupEndDateOutput = $aktiv_bis ? date('d.m.Y', strtotime($aktiv_bis)) : '-';
			echo ($aktiv_bis && $popup_aktivieren ? $popupEndDateOutput : '-');
		}
	}

	add_filter( 'manage_nos_popups_posts_columns', 'nosun_posts_column_views' );
	add_action( 'manage_nos_popups_posts_custom_column', 'nosun_posts_custom_column_manage_nos_popups' );
	
	// Handle the Activate/Deactivate button click
	add_action('admin_init', 'handle_activate_toggle');
	function handle_activate_toggle() {
		// Check if the toggle_activate parameter is present
		if (isset($_GET['toggle_activate']) && isset($_GET['_wpnonce'])) {
			$post_id = intval($_GET['toggle_activate']);
			
			// Verify the nonce for security
			if (!wp_verify_nonce($_GET['_wpnonce'], 'toggle_activate_' . $post_id)) {
				wp_die(__('Security check failed', PLUGIN_NAMESPACE));
			}
	
			// Get current value of the ACF field and toggle it
			$is_active = get_field('nts_pop_active', $post_id);
			$new_status = !$is_active;
	
			// Update the ACF field with the new value
			update_field('nts_pop_active', $new_status, $post_id);
	
			// Redirect back to the admin post list to avoid multiple toggles on refresh
			wp_redirect(admin_url('edit.php?post_type=nos_popups'));
			exit;
		}
	}
	// Add custom styles for the Activate/Deactivate button
	add_action('admin_head', 'custom_activate_button_styles');
	function custom_activate_button_styles() {
		echo '<style>
			.nos_popup_quick_btn {
				background: none !important;
				border: none !important;
				padding: 0 !important;
				margin: 0;
				display: inline-flex !important;
				align-items: center;
			}
			.nos_popup_quick_btn:hover {
				text-decoration: underline;
			}
			.toggle-knob-holder {
				display: inline-block;
				margin-left: 10px;
				position: relative;
				height: 17px;
				width: 34px;
				border-radius: 34px;
				background-color: #ccc;
			}
			.nos_popup_quick_btn:hover .toggle-knob-holder {
				background-color: #afafaf;
			}
			.toggle-knob {
				position: absolute;
				height: 13px;
				width: 13px;
				border-radius: 13px;
				top: 2px;
				left: 2px;
				background-color: #fff;
			}
			.nos_popup_quick_btn.deactivate .toggle-knob-holder {
				background-color: #48C572;
			}
			.nos_popup_quick_btn.deactivate .toggle-knob {
				transform: translateX(calc(100% + 4px));
			}
		</style>';
	}
}


/**
 * insert popup html into body
 */
function nos_popup_content_after_body_open_tag() {
	include_once(WP_PLUGIN_DIR . '/nosun-popups-plugin/templates/loop.php');
}
add_action('wp_body_open', 'nos_popup_content_after_body_open_tag');