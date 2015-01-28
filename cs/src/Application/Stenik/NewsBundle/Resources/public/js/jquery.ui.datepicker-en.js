$(function(){
	$.datepicker.regional['en'] = {
		dayNamesMin: ['Sun', 'Mon', 'Teu', 'Wed', 'Thu', 'Fri', 'Sat'],
		monthNames: ["January","February","March","April","May","June",
			"July","August","September","October","November","December"], // Names of months for drop-down and formatting
		monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
	}
	$.datepicker.setDefaults($.datepicker.regional['en']);
});