<?php
if ( is_user_logged_in() ) {
	get_header();
	if ( have_posts() ) : while ( have_posts() ) : the_post();
	echo '<div class="single-popup-content-container">';
		echo '<div class="container" style="text-align:center;">';
			echo '<div class="nosun-widget widget-spacer spacer-size-xxl"></div>';
			echo '<h1>Popup Vorschau: ' . get_the_title() . '</h1>';
			echo '<div class="nosun-widget widget-spacer spacer-size-s"></div>';
			?>
			<div class="nosun-widget nosun-button-widget">
				<div class="button-wrapper align-center two-buttons">
					<a href="#" target="_self" class="show-popup-again button secondary icon-button icon-pos-icon_right inView">
						<span class="button-text">Popup erneut anzeigen</span>
					</a>
					<a href="/wp-admin/post.php?post=<?php echo get_the_ID();?>&action=edit" target="_self" class="button secondary outline inView">
						<span class="button-text">Popup Inhalte bearbeiten</span>
					</a>
				</div>
			</div>
			<?php
			echo '<div class="nosun-widget widget-spacer spacer-size-l"></div>';
		echo '</div>';
	echo '</div>';
	endwhile; endif;
	get_footer();
} else {
	header("Location: /", true, 301);
	exit();
}
?>