<?php
defined('ABSPATH') OR exit;
// popup number
$itemCount = $popupCounter;
// data and fields
$post_id = get_the_ID();
$content = apply_filters('the_content', get_the_content());
$today = date('Y-m-d');
$today = date('Y-m-d', strtotime($today));
$popup_style = get_field('nts_pop_style');
$nts_pop_trigger = get_field('nts_pop_trigger');
if ( $nts_pop_trigger == 'time-delay' ) {
	$triggerType = $nts_pop_trigger;
	$triggerConAttr = ' data-time-delay="'.get_field('nts_pop_time_delay').'000"';
} elseif ( $nts_pop_trigger == 'scroll-amount' ) {
	$triggerType = $nts_pop_trigger;
	$triggerConAttr = ' data-scroll-amount="'.get_field('nts_pop_scroll_amount').'"';
} elseif ( $nts_pop_trigger == 'click' ) {
	$triggerType = $nts_pop_trigger;
	$triggerConAttr = ' data-clicked-elements="'.get_field('nts_pop_clicked_element').'"';
} elseif ( $nts_pop_trigger == 'element-visible' ) {
	$triggerType = $nts_pop_trigger;
	$triggerConAttr = ' data-visible-element="'.get_field('nts_pop_visible_element_id').'"';
} else {
	$triggerType = 'time-delay';
	$triggerConAttr = ' data-time-delay="1000"';
}
// design options and contents
$text = get_field('nos_popup_content_sticky_bar_text');
$text_color = get_field('nos_popup_content_sticky_bar_text_color');
$bg_color = get_field('nos_popup_content_sticky_bar_bg_color');
?>
<div class="popup-bg <?php echo ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-bg-<?php the_ID(); ?>"></div>
<div class="popup-wrapper popup-count-<?php echo $itemCount.' popup-style-'.$popup_style.'' . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-<?php the_ID(); ?>" data-trigger-type="<?php echo $triggerType; ?>"<?php echo $triggerConAttr; ?>>
	<div class="popup"<?php echo ( $bg_color ? 'style="background-color:'.$bg_color.';"' : '' ); ?>>
		<div class="popup-inner">
			<button aria-label="<?php echo __('Popup schließen', 'nosun'); ?>" class="popup-close"></button>
			<div class="popup-content-wrap">
				<div class="popup-content-inner">
					<div class="wysiwyg popup-content"<?php echo ( $text_color ? 'style="color:'.$text_color.';"' : '' ); ?>>
						<?php
						if ( $text ) {
							echo $text;
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>