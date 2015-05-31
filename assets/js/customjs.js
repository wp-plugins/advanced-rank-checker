function select_list() {
	var country = jQuery(".country option:selected").val();
	
	if(country == 8888) {
		jQuery(".add_country").removeClass("hide");
	}
}

jQuery(document).ready(function() {
    jQuery("body").tooltip({ selector: '[data-toggle=tooltip]' });
    jQuery(".add_country").insertAfter(jQuery(".country"));
});