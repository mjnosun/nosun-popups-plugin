<?php
defined('ABSPATH') OR exit;
// popup number
if ( is_singular('nos_popups') ) {
	$itemCount = 1;
} else {
	$itemCount = $popupCounter;
}
// data and fields
$post_id = get_the_ID();
// $content = apply_filters('the_content', get_the_content());
$today = date('Y-m-d');
$today = date('Y-m-d', strtotime($today));
// $popup_style = get_field('nts_pop_style');
$popup_style = 'default';
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
// popup content layout repeater
$content_blocks = get_field('nos_popup_contents_pop_up');

// popup storage type
$storage = get_field('nos_popup_storage')?:'session';

// popup sizes
$popup_max_height = get_field('fieldname__design__max_height');
$popup_max_width = get_field('fieldname__design__max_width');

// popup position
$alignment = get_field('fieldname__design__alignment');
$align_items = 'center';
$justify_content = 'center';
switch ( $alignment ) {
	case 'top-left':
		$align_items = 'start';
		$justify_content = 'start';
		break;
	case 'top-center':
		$align_items = 'start';
		$justify_content = 'center';
		break;
	case 'top-right':
		$align_items = 'start';
		$justify_content = 'end';
		break;
	case 'center-left':
		$align_items = 'center';
		$justify_content = 'start';
		break;
	case 'center-center':
		$align_items = 'center';
		$justify_content = 'center';
		break;
	case 'center-right':
		$align_items = 'center';
		$justify_content = 'end';
		break;
	case 'bottom-left':
		$align_items = 'end';
		$justify_content = 'start';
		break;
	case 'bottom-center':
		$align_items = 'end';
		$justify_content = 'center';
		break;
	case 'bottom-right':
		$align_items = 'end';
		$justify_content = 'end';
		break;
	default:
		$align_items = 'center';
		$justify_content = 'center';
}

$popup_style_addition = '';
// popup background
// color
$popup_background_color = 'var(--c-bg, #ffffff)';
$popup_background_color_option = get_field('fieldname__design__background_color_options');
if ( $popup_background_color_option == 'custom' ) {
	$popup_background_color = get_field('fieldname__design__background_color');
} else {
	$popup_background_color = 'var(--c-'.$popup_background_color_option.')';
}
// image
$background_image = get_field('fieldname__design__background_image');
$background_image_alignment = get_field('fieldname__design__background_image_alignment');
$background_image_fit = get_field('fieldname__design__background_image_fit');
if ( $background_image ) {
	$background_image_alignments = explode(' ', $background_image_alignment);
	$background_image_alignment_y = $background_image_alignments[0];
	$background_image_alignment_x = $background_image_alignments[1];
	if ( $background_image_fit == 'contain' ) {
		$background_image_fit = get_field('fieldname__design__background_image_width') . '%';
		$background_image_offset_y = get_field('fieldname__design__background_image_offset_y');
		$background_image_alignment_y .= ' ' . $background_image_offset_y . 'px';
	}
	
	$popup_style_addition .= 'background-image:url('.$background_image['url'].');';
	$popup_style_addition .= 'background-position:'.$background_image_alignment_y.' '.$background_image_alignment_x.';';
	$popup_style_addition .= 'background-size:'.$background_image_fit.';background-repeat:no-repeat;';
}

?>
<div class="popup-bg popup-bg-<?php echo $itemCount . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-bg-<?php the_ID(); ?>"></div>
<div class="popup-wrapper popup-count-<?php echo $itemCount.' popup-style-'.$popup_style.'' . ( is_singular('nos_popups') ? ' active' : '' ); ?>" id="popup-<?php the_ID(); ?>" data-trigger-type="<?php echo $triggerType; ?>"<?php echo $triggerConAttr; ?> data-storage="<?= $storage ?>" style="align-items: <?php echo $align_items; ?>;justify-content: <?php echo $justify_content; ?>;">
	<div tabindex="0" class="trap-focus"></div>
	<div class="popup" role="dialog" aria-modal="true" aria-label="<?= get_the_title() ?>" style="max-width:<?php echo ( $popup_max_width ? $popup_max_width.'px' : '935px' ); ?>;background-color:<?php echo $popup_background_color; ?>;">
		<div class="popup-inner" style="max-height:<?php echo ( $popup_max_height ? $popup_max_height.'vh' : '85vh' ); ?>;">
			<button aria-label="<?php echo __('Popup schließen', 'nosun'); ?>" class="popup-close" id="popup-cl-<?php the_ID(); ?>"></button>
			<div class="popup-content-wrap" style="<?php echo $popup_style_addition; ?>">
				<div class="popup-content-inner">
					<div class="wysiwyg popup-content">
						<?php if ( $content_blocks ) { ?>
							<?php foreach ( $content_blocks as $content_block ) { ?>
								<div class="popup-content-block">
									<?php if ( $content_block['acf_fc_layout'] == 'fieldname__layout_spacer' ) { ?>
										<?php
										$spacer_options = $content_block['fieldname__layout_spacer__options'];
										if ( $spacer_options == 'custom' ) {
											$custom_spacer_height = $content_block['fieldname__layout_spacer__custom_height'];
											echo '<div class="nos-popups-spacer spacer-size-custom" style="height: '.$custom_spacer_height.'px;"></div>';
										} else {
											echo '<div class="nos-popups-spacer spacer-size-'.$spacer_options.'"></div>';
										}
										?>
									<?php } elseif ( $content_block['acf_fc_layout'] == 'nos_popup_layout_image' ) { ?>
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
										
										$text_align = ( $content_block['fieldname__popup__layout_image_text__text_align'] ? $content_block['fieldname__popup__layout_image_text__text_align'] : 'left' );
										
										$headline_color = 'var(--c-headlines, #000000)';
										$headline_color_option = $content_block['fieldname__popup__layout_image_text__headline_color_options'];
										if ( $headline_color_option == 'custom' ) {
											$headline_color = $content_block['fieldname__popup__layout_image_text__custom_headline_color'];
										} else {
											$headline_color = 'var(--c-'.$headline_color_option.')';
										}
										
										$text_color = 'var(--c-text, #000000)';
										$text_color_option = $content_block['fieldname__popup__layout_image_text__text_color_options'];
										if ( $text_color_option == 'custom' ) {
											$text_color = $content_block['fieldname__popup__layout_image_text__custom_text_color'];
										} else {
											$text_color = 'var(--c-'.$text_color_option.')';
										}
										
										$background_color = 'var(--c-bg, transparent)';
										$background_color_option = $content_block['fieldname__popup__layout_image_text__background_color_options'];
										if ( $background_color_option == 'custom' ) {
											$background_color = $content_block['fieldname__popup__layout_image_text__custom_background_color'];
										} else {
											$background_color = 'var(--c-'.$background_color_option.')';
										}
										
										switch ( $headline_tag ) {
											case 'h1': $headlineHTMLTag = 'h1';break;
											case 'h2': $headlineHTMLTag = 'h2';break;
											case 'h3': $headlineHTMLTag = 'h3';break;
											case 'h4': $headlineHTMLTag = 'h4';break;
											case 'h5': $headlineHTMLTag = 'h5';break;
											case 'h6': $headlineHTMLTag = 'h6';break;
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
										<div class="pcb-image-text grid v-center" style="background-color: <?php echo $background_color; ?>; grid-gap:0;">
											<?php if ( $col_positions == 'image-left' ) { ?>
												<div class="column pcb-image-col <?php echo $imageColClass; ?> cover">
													<?php if ( $image ) { ?>
														<?php echo wp_get_attachment_image($image, 'large'); ?>
													<?php } ?>
												</div>
											<?php } ?>
											<div class="column pcb-text-col <?php echo $textColClass . ' p-' .$padding; ?>" style="text-align:<?php echo $text_align; ?>;">
												<?php if ( $headline ) { ?>
													<div class="popup-headline-container">
														<span class="popup-headline <?php echo $headlineHTMLTag;?>" style="color: <?php echo $headline_color; ?>"><?php echo nl2br($headline); ?></span>
													</div>
												<?php } ?>
												<?php if ( $text ) { ?>
													<div class="wysiwyg" style="color: <?php echo $text_color; ?>"><?php echo $text; ?></div>
												<?php } ?>
												<?php if ( $add_button ) { ?>
													<a href="<?php echo $button_url; ?>" class="button <?php echo $content_block['fieldname__popup__layout_image_text_button_style']; ?>"<?php echo $button_target; ?> rel="<?php echo $button_rel; ?>"><?php echo $button_text; ?></a>
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
								<!-- Layout Text -->
									<?php } else { ?>
										<?php
										$padding = $content_block['nos_popup_layout_text_innenabstand_textspalte'];
										$headline = $content_block['nos_popup_layout_text_headline'];
										$headline_tag = $content_block['nos_popup_layout_text_headline_tag'];
										
										$text_align = ( $content_block['fieldname__popup__layout_text__text_align'] ? $content_block['fieldname__popup__layout_text__text_align'] : 'left' );
										
										$headline_color = 'var(--c-headlines, #000000)';
										$headline_color_option = $content_block['fieldname__popup__layout_text__headline_color_options'];
										if ( $headline_color_option == 'custom' ) {
											$headline_color = $content_block['fieldname__popup__layout_text__custom_headline_color'];
										} else {
											$headline_color = 'var(--c-'.$headline_color_option.')';
										}
										
										$text_color = 'var(--c-text, #000000)';
										$text_color_option = $content_block['fieldname__popup__layout_text__text_color_options'];
										if ( $text_color_option == 'custom' ) {
											$text_color = $content_block['fieldname__popup__layout_text__custom_text_color'];
										} else {
											$text_color = 'var(--c-'.$text_color_option.')';
										}
										
										$background_color = 'var(--c-bg, transparent)';
										$background_color_option = $content_block['fieldname__popup__layout_text__background_color_options'];
										if ( $background_color_option == 'custom' ) {
											$background_color = $content_block['fieldname__popup__layout_text__custom_background_color'];
										} else {
											$background_color = 'var(--c-'.$background_color_option.')';
										}
										
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
										<div class="popup-text-wrapper p-<?php echo $padding; ?>" style="background-color: <?php echo $background_color; ?>;text-align: <?php echo $text_align; ?>;">
											<?php if ( $headline ) { ?>
												<div class="popup-headline-container">
													<span class="popup-headline <?php echo $headlineHTMLTag;?>" style="color: <?php echo $headline_color; ?>"><?php echo nl2br($headline); ?></span>
												</div>
											<?php } ?>
											<?php if ( $text ) { ?>
												<div class="wysiwyg" style="color: <?php echo $text_color; ?>"><?php echo $text; ?></div>
											<?php } ?>
											<?php if ( $add_button ) { ?>
												<a href="<?php echo $button_url; ?>" class="button <?php echo $content_block['fieldname__popup__layout_text_button_style']; ?>"<?php echo $button_target; ?> rel="<?php echo $button_rel; ?>"><?php echo $button_text; ?></a>
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