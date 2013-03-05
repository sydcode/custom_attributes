/* 
 * Script for "Edit Attributes" page 
 */

jQuery(document).ready(function($) {
	
	/* Show or hide fields when type is changed */
	$('.field_type').change(function() {
		switch($(this).val()) {
			case 'text':
				$(this).parent().parent().prev().hide();
				$(this).parent().parent().next().find('.field_required').show();	
				break;
			case 'select':	
				$(this).parent().parent().prev().show();
				$(this).parent().parent().next().find('.field_required').show();
				break;
			case 'radio':	
				$(this).parent().parent().prev().show();
				$(this).parent().parent().next().find('.field_required').show();
				break;					
			case 'checkbox': 
				$(this).parent().parent().prev().hide();
				$(this).parent().parent().next().find('.field_required').hide();
				break;
		}
	});
	
	/* Create sortable list of attributes that reorders when changed */		
	$('.sortable').sortable({
		stop: function (event, ui) {
			var counter = 1;
			$('.sortable input').each(function(){
				$(this).val(counter);
				counter++;
			});
		}
	});

	/* Hide attributes if alphabetical order set on page load */
	if ($('#alpha_order').is(':checked')) {
		$('.custom_order').hide();
	}
	
	/* Show or hide attributes when order is changed */
	$('#custom_order').change(function() {
		if ($(this).is(':checked')) {
			$('.custom_order').show();
		}
	});
	$('#alpha_order').change(function() {
		if ($(this).is(':checked')) {
			$('.custom_order').hide();
		}
	});
	
	/* Show or hide attribute edit panel */
	$('.attribute_label').click(function() {
		$(this).next('.attribute_panel').toggle();
		$(this).children('img').toggle();
	});
	
});