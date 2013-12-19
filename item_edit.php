<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}
?>
<div id='custom_attributes'>
<?php
if(!empty($fields)) {
	include 'item_edit_fields.php';			
}
foreach ($groups as $group) {
	$group_id = $group['pk_i_id'];
	$heading = $group['s_heading'];
	$order_type = $group['s_order_type'];
	$fields = Attributes::newInstance()->getFields($group_id, $order_type);			
	if(!empty($fields)) {
		if (!empty($heading)) {
?>
	<h3 class='render-title group-heading'><?php echo $heading; ?></h3>
<?php
		}	
		include 'item_edit_fields.php';
	}
}
?>
<!-- end custom_attributes--></div>
<script type='text/javascript'>
	/* Setup jQuery Datepicker */
	jQuery('#custom_attributes .edit_date').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	/* Apply custom style to select fields */
	jQuery('#custom_attributes select').each(function() {
		selectUi($(this));
	});
</script>
<?php //END