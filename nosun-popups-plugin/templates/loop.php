<?php
defined('ABSPATH') OR exit;

$today_time = current_time('Ymd');
$currentPagePostID = get_the_ID();
if ( is_singular('nos_popups') ) {
	$popupArgs = array(
		'post_type' => 'nos_popups',
		'post_status' => 'any',
		'p' => $currentPagePostID,
	);
} else {
	$popupArgs = array(
		'post_type' => 'nos_popups',
		'post_status' => 'publish',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'posts_per_page' => -1,
	);
	if ( class_exists('ACF') ) {
		$acf_meta_query = array(
			array(
				'key' => 'nts_pop_active',
				'value' => '1',
				'compare' => '=='
			),
		);
		$popupArgs['meta_query'] = $acf_meta_query;
	}
}
$popupQuery = new WP_Query($popupArgs);
$popupCounter = 0;

if ($popupQuery->have_posts()) :
	while ($popupQuery->have_posts()) : $popupQuery->the_post();
		$popupCounter++;
		// Data and fields
		$post_id = get_the_ID();
		$content = apply_filters('the_content', get_the_content());
		$popup_aktivieren = false;
		$startDate = null;
		$endDate = null;

		// Initialize ACF-related variables
		$nts_pop_show_everywhere = false;
		$nts_pop_include_posts = []; // Will store post IDs for inclusion
		$nts_pop_exclude_posts = [];  // Will store post IDs for exclusion
		$popup_style = 'default';     // Default template

		if (class_exists('ACF')) {
			$nts_pop_show_everywhere = get_field('nts_pop_show_everywhere');
			$popup_aktivieren = get_field('nts_pop_active'); // Get popup activation status

			// Only fetch these fields if $nts_pop_show_everywhere is false
			if (!$nts_pop_show_everywhere) {
				// Get included posts (if any)
				$included_posts = get_field('nts_pop_visibility'); // Assuming this is a post_object field
				if ($included_posts) {
					$nts_pop_include_posts = array_map(function($post) {
						return $post->ID; // Extract post IDs from post objects
					}, $included_posts);
				}
			}
			// Process exclusion rules (repeater field)
			$exclusion_rules = get_field('nts_pop_exclusion_rules');
			if ($exclusion_rules) {
				foreach ($exclusion_rules as $rule) {
					
					$exclusion_type = $rule['exclusion_type'];
					$exclusion_value = $rule['exclusion_value'];
			
					// Handle specific_pages exclusion type
					if ($exclusion_type === 'specific_pages') {
						$excluded_posts = $rule['nts_pop_posts_excluded']; // Get excluded posts
						
						if ($excluded_posts) {
							// Add excluded post IDs to the exclusion array
							$nts_pop_exclude_posts = array_merge($nts_pop_exclude_posts, $excluded_posts);
						}
					}
			
					// Add logic for other exclusion types (e.g., archives, post_types, etc.) if needed
				}
			}

			// Handle date range
			if (get_field('nts_pop_timeframe')) {
				$startDate = get_field('nts_pop_timeframe')['nts_pop_start_date'];
				$endDate = get_field('nts_pop_timeframe')['nts_pop_end_date'];

				// Convert dates to Y-m-d format
				$startDate = $startDate ? date('Y-m-d', strtotime($startDate)) : null;
				$endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : null;
			}

			$popup_style = get_field('nts_pop_style') ?: 'default'; // Fallback to default template
		}

		// Prepare date condition
		$today = date('Y-m-d');
		$condition_date = false;

		// Logic for date validation
		if ($popup_aktivieren) {
			if ($startDate && $endDate) {
				// If both dates are set, check if today is within the range
				$condition_date = ($today >= $startDate && $today <= $endDate);
			} elseif ($startDate && !$endDate) {
				// If only startDate is set, show popup if today is past or equal to startDate
				$condition_date = ($today >= $startDate);
			} elseif (!$startDate && $endDate) {
				// If only endDate is set, show popup if today is before or equal to endDate
				$condition_date = ($today <= $endDate);
			} elseif (!$startDate && !$endDate) {
				// If no dates are set, consider popup always active
				$condition_date = true;
			}
		}

		// Prepare popup content
		ob_start();
		include(WP_PLUGIN_DIR . '/nosun-popups-plugin/templates/' . $popup_style . '.php');
		$popupContent = ob_get_contents();
		ob_clean();

		/* Conditions
		============================ */
		// Check if the current post is in the excluded posts list
		$is_excluded = !empty($nts_pop_exclude_posts) && in_array($currentPagePostID, $nts_pop_exclude_posts);

		// Output content if conditions are met
		if (is_singular('nos_popups') || !class_exists('ACF')) {
			// Output popup for the singular 'nos_popups' post type or if ACF isn't available
			echo $popupContent;
		} elseif (!$is_excluded && $nts_pop_show_everywhere && $condition_date) {
			// Show everywhere if enabled, date condition is true, and not excluded
			echo $popupContent;
		} elseif (!$is_excluded && !$nts_pop_show_everywhere && !empty($nts_pop_include_posts) && in_array($currentPagePostID, $nts_pop_include_posts) && $condition_date) {
			// Show on specific posts where visibility matches, date condition is true, and not excluded
			echo $popupContent;
		}
	endwhile;
endif;
wp_reset_postdata();
?>