<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}
?>
	<table>
		<tbody>
<?php
foreach ($fields as $field) {
	$field_id = $field['pk_i_id'];
	$label = $field['s_label'];
	$type = $field['s_type'];
	$name = 'field_' . $field['pk_i_id'];
	$value = Attributes::newInstance()->getValue($item_id, $field_id); 
	$required = $field['b_required'];
	// Build classes
	if ($required) {
		$class = 'required';
	} else {
		$class = '';
	}
	if ($type == 'date') {
		$class .= ' edit_date';
	}
	if (!empty($class)) {
		$class = " class='" . trim($class) . "'";
	}
	// Get saved value from sesssion
	if( Session::newInstance()->_getForm($name) != '') {
		$value = Session::newInstance()->_getForm($name);
	}	
?>
			<tr class='edit_row'>
				<input type='hidden' name='fields[]' value='<?php echo $field_id; ?>' />
				<td><label class='edit_label' for='<?php echo $name; ?>'><?php echo $label; ?></label></td>
				<td>
<?php if ($type == 'checkbox') {  ?>
<?php 	$checked = ($value == 'checked') ? " checked='checked'" : ''; ?>
					<label>
						<input id='<?php echo $name; ?>' class='edit_checkbox' type='checkbox' name='<?php echo $name; ?>' value='checked'<?php echo $checked; ?> />
						<?php _e('Tick for "Yes"', CA_PLUGIN_NAME); ?>
					</label>
<?php } elseif ($type == 'date') { ?>	
					<input id='<?php echo $name; ?>'<?php echo $class; ?> type='text' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />
<?php } elseif ($type == 'radio') { ?>						
					<?php $this->radio_buttons($field_id, $name, $value, $required); ?>					
<?php } elseif ($type == 'select') { ?>		
					<select id='<?php echo $name; ?>'<?php echo $class; ?> name='<?php echo $name; ?>'>
						<?php $this->select_options($field_id, $value); ?>
					</select>
<?php } elseif ($type == 'text') { ?>
					<input id='<?php echo $name; ?>'<?php echo $class; ?> type='text' name='<?php echo $name; ?>' value='<?php echo $value; ?>' />
<?php } elseif ($type == 'textarea') {  ?>
					<textarea id='<?php echo $name; ?>'<?php echo $class; ?> name='<?php echo $name; ?>'><?php echo $value; ?></textarea>							
<?php } ?>
				</td>
				<td>
<?php if ($required) { ?>
					<span class='required_input'><?php _e('Required', CA_PLUGIN_NAME); ?></span>
<?php } ?>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
<?php //END