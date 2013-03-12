<div id='custom_attributes'>
<?php 
if(!empty($fields)) {
	include 'search_form_fields.php';			
}
foreach ($groups as $group) {
	$group_id = $group['pk_i_id'];
	$heading = $group['s_heading'];
	$order_type = $group['s_order_type'];
	$fields = Attributes::newInstance()->getFields($group_id, $order_type);			
	if(!empty($fields)) {
		if (!empty($heading)) {
?>
	<h4 class='search_heading'><?php echo $heading; ?></h4>
<?php
		}	
		include 'search_form_fields.php';
	}
}
?>
</div>
<?php //END