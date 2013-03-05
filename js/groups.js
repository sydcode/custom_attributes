/* 
 * Script for "Edit Groups" page 
 */

jQuery(document).ready(function($) {

	// Show or hide group edit panel
	$('.group_title').click(function() {
		$(this).next('.group_panel').toggle();
		$(this).children('img').toggle();
	});

	// Setup category tree
	$('#category_tree').treeview({
		animated: "fast",
		collapsed: true
	});
	
	// Show category tree
	$('#category_tree').show();
	
});

// check all categories
function checkAll(id, check) {
	aa = jQuery('#' + id + ' input[type=checkbox]').each(function() {
		jQuery(this).attr('checked', check);
	});
}

// check category
function checkCat(id, check) {
	aa = jQuery('#cat' + id + ' input[type=checkbox]').each(function() {
		jQuery(this).attr('checked', check);
	});
}