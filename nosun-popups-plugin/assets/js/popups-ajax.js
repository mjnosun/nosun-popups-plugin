jQuery(document).ready(function($){
	$.ajax({
		url: nosPopupAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'nos_load_popup',
			post_id: nosPopupAjax.post_id
		},
		success: function (response) {
			$('body').prepend(response);
		}
	});
});