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
$storage = get_field('nos_popup_storage')?:'session';

?>
<div class="popup-bg popup-bg-<?php echo $itemCount . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-bg-<?php the_ID(); ?>"></div>
<div class="popup-wrapper popup-count-<?php echo $itemCount.' popup-style-'.$popup_style.'' . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-<?php the_ID(); ?>" data-trigger-type="<?php echo $triggerType; ?>"<?php echo $triggerConAttr; ?> data-storage="<?= $storage ?>">
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
										$headline = $content_block['nos_popup_layout_text_headline'];
										$headline_tag = $content_block['nos_popup_layout_text_headline_tag'];
										switch ( $headline_tag ) {
											case 'h1': $headlineHTMLTag = 'h1';break;
											case 'h2': $headlineHTMLTag = 'h2';break;
											case 'h3': $headlineHTMLTag = 'h3';break;
											case 'h4': $headlineHTMLTag = 'h4';break;
											case 'h5': $headlineHTMLTag = 'h5';break;
											case 'h6': $headlineHTMLTag = 'h6';break;
											// case 'p': $headlineHTMLTag = 'p';break;
											// case 'span': $headlineHTMLTag = 'span';break;
											// case 'div': $headlineHTMLTag = 'div';break;
											default: $headlineHTMLTag = 'h2';
										}
										$text = $content_block['nos_popup_layout_image_text_text'];
										$padding = $content_block['nos_popup_layout_image_text_innenabstand_textspalte'];
										$add_button = $content_block['nos_popup_layout_text_add_button'];
										if ( $add_button ) {
											$button_text = $content_block['nos_popup_layout_text_button_text'];
											$button_url = $content_block['nos_popup_layout_text_button_url'];
											$button_attributes = $content_block['nos_popup_layout_text_button_attributes'];
											$button_target = '';
											$button_rel = '';
											$rel_counter = 0;
											foreach ( $button_attributes as $button_attr ) {
												if ( $button_attr == 'blank' ) {
													$button_target = ' target="_blank"';
												} else {
													$rel_counter++;
													if ( $rel_counter == 1 ) {
														$button_rel .= $button_attr;
													} else {	
														$button_rel .= ' ' . $button_attr;
													}
												}
											}
										}
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
											<?php if ( $col_positions == 'image-left' ) { ?>
												<div class="column pcb-image-col <?php echo $imageColClass; ?> cover">
													<?php if ( $image ) { ?>
														<?php echo wp_get_attachment_image($image, 'large'); ?>
													<?php } ?>
												</div>
											<?php } ?>
											<div class="column pcb-text-col <?php echo $textColClass . ' p-' .$padding; ?>">
												<?php if ( $headline ) { ?>
													<div class="popup-headline-container">
														<span class="popup-headline <?php echo $headlineHTMLTag;?>"><?php echo nl2br($headline); ?></span>
													</div>
												<?php } ?>
												<?php if ( $text ) { ?>
													<div class="wysiwyg"><?php echo $text; ?></div>
												<?php } ?>
												<?php if ( $add_button ) { ?>
													<a href="<?php echo $button_url; ?>" class="button primary filled"<?php echo $button_target; ?> rel="<?php echo $button_rel; ?>"><?php echo $button_text; ?></a>
												<?php } ?>
											</div>
											<?php if ( $col_positions == 'image-right' ) { ?>
												<div class="column pcb-image-col <?php echo $imageColClass; ?> cover">
													<?php if ( $image ) { ?>
														<?php echo wp_get_attachment_image($image, 'large'); ?>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									<?php } else { ?>
										<?php
										$padding = $content_block['nos_popup_layout_text_innenabstand_textspalte'];
										$headline = $content_block['nos_popup_layout_text_headline'];
										$headline_tag = $content_block['nos_popup_layout_text_headline_tag'];
										switch ( $headline_tag ) {
											case 'h1': $headlineHTMLTag = 'h1';break;
											case 'h2': $headlineHTMLTag = 'h2';break;
											case 'h3': $headlineHTMLTag = 'h3';break;
											case 'h4': $headlineHTMLTag = 'h4';break;
											case 'h5': $headlineHTMLTag = 'h5';break;
											case 'h6': $headlineHTMLTag = 'h6';break;
											// case 'p': $headlineHTMLTag = 'p';break;
											// case 'span': $headlineHTMLTag = 'span';break;
											// case 'div': $headlineHTMLTag = 'div';break;
											default: $headlineHTMLTag = 'h2';
										}
										$text = $content_block['nos_popup_layout_text_text'];
										$add_button = $content_block['nos_popup_layout_text_add_button'];
										if ( $add_button ) {
											$button_text = $content_block['nos_popup_layout_text_button_text'];
											$button_url = $content_block['nos_popup_layout_text_button_url'];
											$button_attributes = $content_block['nos_popup_layout_text_button_attributes'];
											$button_target = '';
											$button_rel = '';
											$rel_counter = 0;
											foreach ( $button_attributes as $button_attr ) {
												if ( $button_attr == 'blank' ) {
													$button_target = ' target="_blank"';
												} else {
													$rel_counter++;
													if ( $rel_counter == 1 ) {
														$button_rel .= $button_attr;
													} else {	
														$button_rel .= ' ' . $button_attr;
													}
												}
											}
										}
										?>
										<div class="popup-text-wrapper p-<?php echo $padding; ?>">
											<?php if ( $headline ) { ?>
												<div class="popup-headline-container">
													<span class="popup-headline <?php echo $headlineHTMLTag;?>"><?php echo nl2br($headline); ?></span>
												</div>
											<?php } ?>
											<?php if ( $text ) { ?>
												<div class="wysiwyg"><?php echo $text; ?></div>
											<?php } ?>
											<?php if ( $add_button ) { ?>
												<a href="<?php echo $button_url; ?>" class="button primary filled"<?php echo $button_target; ?> rel="<?php echo $button_rel; ?>"><?php echo $button_text; ?></a>
											<?php } ?>
										</div>
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