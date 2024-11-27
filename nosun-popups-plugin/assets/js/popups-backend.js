jQuery(document).ready(function($){
	
	// set end date to +10 years if empty
	// BUGGY and not required anymore
	
	// var d = new Date(Date.now());
	// var day = d.getDate();
	// var month = d.getMonth();
	// var month2Digit = ("0" + (d.getMonth() + 1)).slice(-2);
	// const months = ["Jänner","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
	// let monthName = months[d.getMonth()];
	// var year = d.getFullYear();
	// var yearPlusTen = d.getFullYear() + 10;
	// var startDateInput = $('.acf-field.acf-field-date-picker[data-name="nts_pop_start_date"] input[type="text"]');
	// var startDateInputHidden = $('.acf-field.acf-field-date-picker[data-name="nts_pop_start_date"] input[type="hidden"]');
	// var endDateInput = $('.acf-field.acf-field-date-picker[data-name="nts_pop_end_date"] input[type="text"]');
	// var endDateInputHidden = $('.acf-field.acf-field-date-picker[data-name="nts_pop_end_date"] input[type="hidden"]');
	// if ( !startDateInput.val() ) {
	// 	var dateOutput = day + '. ' + monthName + ' ' + year;
	// 	var dateOutputHidden = year+''+month2Digit+''+day;
	// 	startDateInput.val(dateOutput);
	// 	startDateInputHidden.val(dateOutputHidden);
	// }
	// if ( !endDateInput.val() ) {
	// 	var dateOutput = day + '. ' + monthName + ' ' + yearPlusTen;
	// 	var dateOutputHidden = yearPlusTen+''+month2Digit+''+day;
	// 	endDateInput.val(dateOutput);
	// 	endDateInputHidden.val(dateOutputHidden);
	// }
});