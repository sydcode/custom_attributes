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
	<h3 class='render-title group-heading'><?php _e($heading, PLUGIN_NAME); ?></h3>
<?php
		}	
		include 'item_edit_fields.php';
	}
}
?>
<!-- end custom_attributes--></div>
<script type='text/javascript'>
	/* Apply custom style to select fields */
	jQuery('#custom_attributes select').each(function() {
		selectUi($(this));
	});
</script>
<?php //END