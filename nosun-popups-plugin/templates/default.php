<?php
defined('ABSPATH') OR exit;
// popup number
$itemCount = $popupCounter;
// data and fields
$post_id = get_the_ID();
// $content = apply_filters('the_content', get_the_content());
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
$content_blocks = get_field('nos_popup_contents_pop_up');
?>
<div class="popup-bg popup-bg-<?php echo $itemCount . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-bg-<?php the_ID(); ?>"></div>
<div class="popup-wrapper popup-count-<?php echo $itemCount.' popup-style-'.$popup_style.'' . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-<?php the_ID(); ?>" data-trigger-type="<?php echo $triggerType; ?>"<?php echo $triggerConAttr; ?>>
	<div tabindex="0" class="trap-focus"></div>
	<div class="popup" role="dialog" aria-modal="true" aria-label="<?= get_the_title() ?>">
		<div class="popup-inner">
			<button aria-label="<?php echo __('Popup schließen', 'nosun'); ?>" class="popup-close"></button>
			<div class="popup-content-wrap">
				<div class="popup-content-inner">
					<div class="wysiwyg popup-content">
						<?php if ( $content_blocks ) { ?>
							<?php foreach ( $content_blocks as $content_block ) { ?>
								<div class="popup-content-block">
									<?php if ( $content_block['acf_fc_layout'] == 'nos_popup_layout_image' ) { ?>
										<?php
										$image = $content_block['nos_popup_layout_image_image'];
										?>
										<?php if ( $image ) { ?>
											<div class="popup-image-wrapper">
												<?php echo wp_get_attachment_image($image, 'large'); ?>
											</div>
										<?php } ?>
									<?php } elseif ( $content_block['acf_fc_layout'] == 'nos_popup_layout_image_text' ) { ?>
										<?php
										$colsizes = $content_block['nos_popup_layout_colsizes'];
										$col_positions = $content_block['nos_popup_layout_image_text_positionierung'];
										$image = $content_block['nos_popup_layout_image_text_image'];
										$text = $content_block['nos_popup_layout_image_text_text'];
										$padding = $content_block['nos_popup_layout_image_text_innenabstand_textspalte'];
										switch ($colsizes) {
											case "img-1-4-text-3-4":
												$imageColClass = 'col-3';
												$textColClass = 'col-9';
												break;
											case "img-1-3-text-2-3":
												$imageColClass = 'col-4';
												$textColClass = 'col-8';
												break;
											case "img-1-2-text-1-2":
												$imageColClass = 'col-6';
												$textColClass = 'col-6';
												break;
											case "img-2-3-text-1-3":
												$imageColClass = 'col-8';
												$textColClass = 'col-4';
												break;
											case "img-3-4-text-1-4":
												$imageColClass = 'col-9';
												$textColClass = 'col-3';
												break;
											default:
												$imageColClass = 'col-6';
												$textColClass = 'col-6';
										}
										?>
										<div class="pcb-image-text grid v-center">
											<?php if ( $col_positions == 'image-right' ) { ?>
												<div class="column pcb-text-col <?php echo $textColClass . ' p-' .$padding; ?>">
													<?php if ( $text ) { ?>
														<?php echo $text; ?>
													<?php } ?>
												</div>
											<?php } ?>
											<div class="column pcb-image-col <?php echo $imageColClass; ?> cover">
												<?php if ( $image ) { ?>
													<?php echo wp_get_attachment_image($image, 'large'); ?>
												<?php } ?>
											</div>
											<?php if ( $col_positions == 'image-left' ) { ?>
												<div class="column pcb-text-col <?php echo $textColClass . ' p-' .$padding; ?>">
													<?php if ( $text ) { ?>
														<?php echo $text; ?>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									<?php } else { ?>
										<?php
										$padding = $content_block['nos_popup_layout_text_innenabstand_textspalte'];
										$text = $content_block['nos_popup_layout_text_text'];
										?>
										<?php if ( $text ) { ?>
											<div class="popup-text-wrapper p-<?php echo $padding; ?>">
												<?php echo $text; ?>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div tabindex="0" class="trap-focus"></div>
</div>