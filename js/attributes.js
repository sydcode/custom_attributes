/* 
 * Script for "Edit Attributes" page 
 */

jQuery(document).ready(function($) {
	
	/* Inital setup */
	$('.field_type').each(function() {
		updateFieldType($(this));
	});
	
	/* Show or hide options when type is changed */
	$('.field_type').change(function() {
		updateFieldType($(this));
	});
	
	function updateFieldType($element) {
		var $parent = $element.parent().parent();
		switch($element.val()) {
			case 'checkbox': 
				$parent.prev().hide();
				$parent.next().children('.required, .range').hide();
				break;		
			case 'date': 
				$parent.prev().hide();
				$parent.next().children('.required').show();
				$parent.next().children('.range').hide();
				break;					
			case 'radio':	
				$parent.prev().show();
				$parent.next().children('.required').show();
				$parent.next().children('.range').hide();
				break;					
			case 'select':	
				$parent.prev().show();
				$parent.next().children('.required').show();
				$parent.next().children('.range').hide();
				break;
			case 'text':
				$parent.prev().hide();
				$parent.next().children('.required, .range').show();	
				break;	
			case 'textarea':
				$parent.prev().hide();
				$parent.next().children('.required').show();	
				$parent.next().children('.range').hide();
				break;				
		}
	}
	
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