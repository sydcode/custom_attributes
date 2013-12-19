<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

// Process actions
$action = Params::getParam('plugin_action');
$group_id = Params::getParam('group_id');
$groups = Attributes::newInstance()->getGroups();
$fields = Attributes::newInstance()->getFields($group_id);
if (!empty($action)) {
	$field_id = Params::getParam('field_id');
	if ($action == 'edit_value') {
		$item_id = Params::getParam('item_id');
		$value = Params::getParam('item_value');
		Attributes::newInstance()->setValue($item_id, $field_id, $value);
		osc_add_flash_ok_message( __('Value saved', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
	}
	$values = Attributes::newInstance()->getItems($field_id);
}
?>
<script type='text/javascript' src='<?php echo osc_plugin_url(__FILE__); ?>js/values.js'></script>
<?php osc_show_flash_message(CA_PLUGIN_NAME); ?>
<div id='custom_attributes'>
	<h2 class='render-title heading'><?php _e('Custom Attributes', CA_PLUGIN_NAME); ?></h2>
	<div class='config_column'>
<?php	if (count($groups) > 0) { ?>
		<div class='select_group'>
			<h2 class='render-title sub_heading'><?php _e('Select Group', CA_PLUGIN_NAME); ?></h2>
			<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_values.php' />
				<input type='hidden' name='plugin_action' value='select_group' />	
				<select name='group_id'>
					<option class='select_option' value=''><?php _e('Select a group', CA_PLUGIN_NAME); ?></option>
<?php
$selected_id = Params::getParam('group_id');
foreach ($groups as $group) {
	$id = $group['pk_i_id'];
	$name = trim($group['s_name']);
	if ($id == $selected_id) {
		$selected = " selected='selected'";
	} else {
		$selected = '';
	}
?>
					<option value='<?php echo $id; ?>'<?php echo $selected; ?>><?php echo $name; ?></option>
<?php	}	?>
				</select>
				<p><button class='btn btn-mini' type='submit'><?php _e('Edit', CA_PLUGIN_NAME); ?></button></p>
			</form>
		</div>	
<?php } ?>		
		<div>
			<h2 class='render-title sub_heading'><?php _e('Select Attribute', CA_PLUGIN_NAME); ?></h2>
<?php	if (count($fields) == 0) { ?>
			<p><?php _e('There are no attributes.', CA_PLUGIN_NAME); ?></p>
<?php } else { ?>
			<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_values.php' />
				<input type='hidden' name='plugin_action' value='edit_attribute' />	
				<input type='hidden' name='group_id' value='<?php echo $group_id; ?>' />
				<select name='field_id'>
					<option class='select_option' value=''><?php _e('Select an attribute', CA_PLUGIN_NAME); ?></option>
<?php
$selected_id = Params::getParam('field_id');
foreach ($fields as $field) {
	$field_id = $field['pk_i_id'];
	$label = trim($field['s_label']);
	if ($field_id == $selected_id) {
		$selected = " selected='selected'";
	} else {
		$selected = '';
	}
?>
					<option value='<?php echo $field_id; ?>'<?php echo $selected; ?>><?php echo $label; ?></option>
<?php	}	?>
				</select>
				<p><button class='btn btn-mini' type='submit'><?php _e('Edit', CA_PLUGIN_NAME); ?></button></p>
			</form>
		</div>
<?php } ?>			
	</div>
<?php if (!empty($values)) { ?>
	<div class='config_column'>
		<h2 class='render-title sub_heading'><?php _e('Edit Values', CA_PLUGIN_NAME); ?></h2>
		<div class='list-categories'>
			<ul class='value_list'>
<?php
$field_id = Params::getParam('field_id');
$field_type = Attributes::newInstance()->getFieldType($field_id);
foreach ($values as $value) {
	$item_id = $value['fk_i_item_id'];
	$value = trim($value['s_value']);
	$title = Attributes::newInstance()->getItemTitle($item_id);
?>
				<li class='category_div'>
					<div class='item_title attribute_label'>
						<?php echo $title; ?>
						<img class='arrow_open' src='<?php echo osc_plugin_url(__FILE__); ?>css/arrow_open.png' alt='' />
						<img class='arrow_close' src='<?php echo osc_plugin_url(__FILE__); ?>css/arrow_close.png' alt='' />
					</div>
					<ul class='value_panel'>
						<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
							<input type='hidden' name='page' value='plugins' />
							<input type='hidden' name='action' value='renderplugin' />
							<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_values.php' />
							<input type='hidden' name='plugin_action' value='edit_value' />		
							<input type='hidden' name='item_id' value='<?php echo $item_id; ?>' />	
							<input type='hidden' name='field_id' value='<?php echo $field_id; ?>' />
							<input type='hidden' name='group_id' value='<?php echo $group_id; ?>' />								
							<li>
<?php if ($field_type == 'checkbox') {  ?>
<?php 	$checked = ($value == 'checked') ? " checked='checked'" : ''; ?>
								<label>
								<input class='edit_checkbox' type='checkbox' name='item_value' value='checked'<?php echo $checked; ?> />
								<?php _e('Tick for "Yes"', CA_PLUGIN_NAME); ?></label>
<?php } elseif ($field_type == 'date') { ?>	
								<input id='<?php echo $name; ?>' class='edit_date' type='text' name='item_value' value='<?php echo $value; ?>' />
<?php } elseif ($field_type == 'radio') { ?>						
								<?php CustomAttributes::newInstance()->radio_buttons($field_id, 'item_value', $value); ?>								
<?php } elseif ($field_type == 'select') { ?>		
								<select name='item_value'>
									<?php CustomAttributes::newInstance()->select_options($field_id, $value); ?>
								</select>
<?php } elseif ($field_type == 'text') { ?>
								<input id='<?php echo $name; ?>' type='text' name='item_value' value='<?php echo $value; ?>' />
<?php } elseif ($field_type == 'textarea') {  ?>
								<textarea id='<?php echo $name; ?>' name='item_value'><?php echo $value; ?></textarea>											
<?php } ?>							
							</li>
							<li><button class='btn btn-mini' type='submit'><?php _e('Save', CA_PLUGIN_NAME); ?></button></li>
						</form>							
					</ul>		
				</li>
<?php } ?>					
			</ul>
		</div>
	</div>
<?php } ?>
<!-- end custom_attributes --></div>
<?php //END