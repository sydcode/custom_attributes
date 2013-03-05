/* 
 * Script for "Edit Values" page
 */

jQuery(document).ready(function($) {

	/* Show or hide value edit panel */
	$('.item_title').click(function() {
		$(this).next('.value_panel').toggle();
		$(this).children('img').toggle();
	});
	
});