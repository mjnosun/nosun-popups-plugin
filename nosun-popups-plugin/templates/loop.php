<?php
defined('ABSPATH') OR exit;

$today_time = current_time('Ymd');
global $post;
$currentPagePostID = isset($_POST['post_id']) ? intval($_POST['post_id']) :
					 (isset($post->ID) ? intval($post->ID) :
					 (get_the_ID() ? intval(get_the_ID()) : false));

if ( is_singular('nos_popups') ) {
	$popupArgs = array(
		'post_type' => 'nos_popups',
		'post_status' => 'any',
		'p' => $currentPagePostID,
	);
	// Add language parameter if Polylang is active
	if (function_exists('pll_current_language')) {
		$popupArgs['lang'] = pll_current_language();
	}
} else {
	$popupArgs = array(
		'post_type' => 'nos_popups',
		'post_status' => 'publish',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'posts_per_page' => -1,
	);
	
	// Add language parameter if Polylang is active
	if (function_exists('pll_current_language')) {
		$popupArgs['lang'] = pll_current_language();
	}
	
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
		$nts_pop_include_posts = [];
		$nts_pop_exclude_posts = [];
		$popup_style = 'default';

		if (class_exists('ACF')) {
			$nts_pop_show_everywhere = get_field('nts_pop_show_everywhere');
			$popup_aktivieren = get_field('nts_pop_active');

			if (!$nts_pop_show_everywhere) {
				$inclusion_rules = get_field('field_nts_pop_visibility_rules');
				if ($inclusion_rules) {
					foreach ($inclusion_rules as $rule) {
						$inclusion_type = $rule['rule_type'];
						if ($inclusion_type === 'specific_pages') {
							$included_posts = $rule['nts_pop_posts_included'];
							if ($included_posts) {
								$nts_pop_include_posts = array_map('intval', $included_posts);
							}
						}
					}
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

			$popup_style = get_field('nts_pop_style') ?: 'default';
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
		if (is_singular('nos_popups')) {
			// Always show on single popup pages, ignore all other conditions
			echo $popupContent;
		} elseif (!class_exists('ACF')) {
			// Fallback if ACF is not active
			echo $popupContent;
		} elseif (!$is_excluded && $nts_pop_show_everywhere && $condition_date) {
			// Show everywhere if enabled, date condition is true, and not excluded
			echo $popupContent;
		} elseif (!$is_excluded && !$nts_pop_show_everywhere && !empty($nts_pop_include_posts) && in_array($currentPagePostID, $nts_pop_include_posts) && $condition_date) {
			// Show on specific posts where visibility matches, date condition is true, and not excluded
			echo $popupContent;
		}
		// 1. ALWAYS show on singular popup pages (ignore all other conditions)
		// if (is_singular('nos_popups')) {
		// 	echo $popupContent;
		// } else {
		// 	// 2. Fallback if ACF is not installed
		// 	if (!class_exists('ACF')) {
		// 		echo $popupContent;
		// 	}
		// 	// 3. Normal behavior for other pages
		// 	elseif (!$is_excluded && $popup_aktivieren && $condition_date) {
		// 		// Show if:
		// 		// - "Show everywhere" is ON, **OR**
		// 		// - Current post is in the included list
		// 		if ($nts_pop_show_everywhere || in_array($currentPagePostID, $nts_pop_include_posts)) {
		// 			echo $popupContent;
		// 		}
		// 	}
		// }
		
	endwhile;
endif;
wp_reset_postdata();
?>