<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}
?>
<div id='custom_attributes'>
<?php 
if(!empty($fields)) {
	include 'item_detail_fields.php';			
}
foreach ($groups as $group) {
	$group_id = $group['pk_i_id'];
	$heading = $group['s_heading'];
	$order_type = $group['s_order_type'];
	$fields = Attributes::newInstance()->getFields($group_id, $order_type);			
	if(!empty($fields)) {
		if (!empty($heading)) {
?>
	<h3 class='heading'><?php echo $heading; ?></h3>
<?php
		}	
		include 'item_detail_fields.php';
	}
}
?>
</div>
<?php //END