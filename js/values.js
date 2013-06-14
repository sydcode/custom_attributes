/* 
 * Script for "Edit Values" page
 */

jQuery(document).ready(function($) {

	/* Setup jQuery Datepicker */
	$('#custom_attributes .edit_date').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	/* Show or hide value edit panel */
	$('.item_title').click(function() {
		$(this).next('.value_panel').toggle();
		$(this).children('img').toggle();
	});
	
});