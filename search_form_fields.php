<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}
?>
<fieldset class='box show_only'>
<?php 
foreach ($fields as $field) { 
	if (empty($field['b_search'])) {
		continue;
	}
	$id = $field['pk_i_id'];
	$label = $field['s_label'];
	$type = $field['s_type'];
	$search_limits = $field['b_search_limits'];
	if ($search_limits) {
		$name_min = 'min_field_' . $id;
		$name_max = 'max_field_' . $id;
		$class = 'row two_input';
		$value_min = Params::getParam($name_min);
		$value_max = Params::getParam($name_max);
	} else {
		$name = 'field_' . $id;
		$class = 'row one_input';
		$value = Params::getParam($name);
	}
?>
	<div class='<?php echo $class; ?>'>
		<h6><?php echo $label; ?></h6>
<?php if ($type == 'checkbox') { ?>
<?php   $checked = ($value == 'checked') ? " checked='checked'" : ''; ?>
				<input class='search_checkbox' type='checkbox' id='<?php echo $name; ?>' name='<?php echo $name; ?>' value='checked'<?php echo $checked; ?> />
				<label class='search_label' for='<?php echo $name; ?>'><?php _e('Check to show listings', CA_PLUGIN_NAME); ?></label>
<?php } elseif ($type == 'date') { ?>	
				<input class='search_date' type='text' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />	
<?php } elseif ($type == 'radio') { ?>	
				<?php $this->radio_buttons($id, $name, $value, null, true); ?>
<?php } elseif ($type == 'select') { ?>		
				<select class='search_select' name='<?php echo $name; ?>'>
					<?php $this->select_options($id, $value); ?>
				</select>
<?php } elseif ($type == 'text') { ?>
			<?php if ($search_limits) { ?>
			<div class='search_limits'>
				<label>Min:</label><input class='search_smalltext' type='text' name='<?php echo $name_min; ?>' value='<?php echo $value_min; ?>' />	
				<label class='search_maximum'>Max:</label><input class='search_smalltext' type='text' name='<?php echo $name_max; ?>' value='<?php echo $value_max; ?>' />	
			</div>
			<?php } else { ?>
			<input type='text' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />	
			<?php } ?>
<?php } elseif ($type == 'textarea') {  ?>
			<textarea class='search_textarea' name='<?php echo $name; ?>'><?php echo $value; ?></textarea>					
<?php } ?>
	</div>
<?php } ?>
</fieldset>
<?php //END