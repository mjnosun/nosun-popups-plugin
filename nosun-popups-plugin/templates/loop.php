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
$popupQuery = new WP_Query( $popupArgs );
$popupCounter = 0;
if ($popupQuery->have_posts()) :
	while ($popupQuery->have_posts()) : $popupQuery->the_post();
		$popupCounter++;
		// data and fields
		$post_id = get_the_ID();
		$content = apply_filters('the_content', get_the_content());
		$popup_aktivieren = false;
		$startDate = null;
		$endDate = null;

		if (class_exists('ACF')) {
			$nts_pop_show_everywhere = get_field('nts_pop_show_everywhere');
			$popup_aktivieren = get_field('nts_pop_active'); // Get popup activation status

			if (get_field('nts_pop_timeframe')) {
				$startDate = get_field('nts_pop_timeframe')['nts_pop_start_date'];
				$endDate = get_field('nts_pop_timeframe')['nts_pop_end_date'];

				// Convert dates to Y-m-d format
				$startDate = $startDate ? date('Y-m-d', strtotime($startDate)) : null;
				$endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : null;
			}
			$popup_posts_visibility = get_field('nts_pop_visibility');
			$popup_style = get_field('nts_pop_style');
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
		// include(WP_PLUGIN_DIR . '/nosun-popups-plugin/templates/default.php');
		include(WP_PLUGIN_DIR . '/nosun-popups-plugin/templates/'.$popup_style.'.php');
		$popupContent = ob_get_contents();
		ob_clean();

		/* conditions
		============================ */
		// Output content if conditions are met
		if (is_singular('nos_popups') || !class_exists('ACF')) {
			// Output popup for the singular 'nos_popups' post type or if ACF isn't available
			echo $popupContent;
		} elseif ($nts_pop_show_everywhere && $condition_date) {
			// Show everywhere if enabled and date condition is true
			echo $popupContent;
		} elseif (!$nts_pop_show_everywhere && $popup_posts_visibility && in_array($currentPagePostID, $popup_posts_visibility) && $condition_date) {
			// Show on specific posts where visibility matches and date condition is true
			echo $popupContent;
		}
	endwhile;
endif;
wp_reset_postdata();
?>