jQuery(document).ready(function($){
	$.ajax({
		url: nosPopupAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'nos_load_popup'
		},
		success: function (response) {
			$('body').prepend(response);
		}
	});
});