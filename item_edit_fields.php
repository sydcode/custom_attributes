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
	if ($required) {
		$class = " class='required'";
	} else {
		$class = '';
	}
	// get saved value from sesssion
	if( Session::newInstance()->_getForm($name) != '') {
		$value = Session::newInstance()->_getForm($name);
	}	
?>
			<tr class='edit_row'>
				<input type='hidden' name='fields[]' value='<?php echo $field_id; ?>' />
				<td><label class='edit_label' for='<?php echo $name ?>'><?php _e($label, PLUGIN_NAME); ?></label></td>
				<td>
<?php if ($type == 'text') { ?>
					<input id='<?php echo $name ?>'<?php echo $class ?> type='text' name='<?php echo $name ?>' value='<?php _e($value, PLUGIN_NAME); ?>' />
<?php } else if ($type == 'checkbox') {  ?>
<?php 	$checked = ($value == 'checked') ? " checked='checked'" : ''; ?>
					<label>
						<input id='<?php echo $name ?>' class='edit_checkbox' type='checkbox' name='<?php echo $name ?>' value='checked'<?php echo $checked ?> />
						<?php _e('Tick for "Yes"', PLUGIN_NAME); ?>
					</label>
<?php } else if ($type == 'select') { ?>		
					<select id='<?php echo $name ?>'<?php echo $class ?> name='<?php echo $name; ?>'>
<?php ca_select_options($field_id, $value); ?>
					</select>
<?php } else if ($type == 'radio') { ?>						
					<?php ca_radio_buttons($field_id, $name, $value, $required); ?>
<?php } ?>
				</td>
				<td>
<?php if ($required) { ?>
					<span class='required_input'><?php _e('Required', PLUGIN_NAME); ?></span>
<?php } ?>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
<?php //END