<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

// Process actions
$action = Params::getParam('plugin_action');
$group_id = Params::getParam('group_id');
if (!empty($action)) {
	switch($action) {
		case('create_group'): 
			$name = Params::getParam('group_name');
			if (!empty($name)) {
				$group_id = Attributes::newInstance()->getGroupID($name);
				if (empty($group_id)) {
					Attributes::newInstance()->insertGroup($name);
					$group_id = Attributes::newInstance()->getGroupID($name);
					Params::setParam('group_id', $group_id);
					osc_add_flash_ok_message( __('Group created.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
				} else {
					osc_add_flash_warning_message( __('That group already exists, please choose another name.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
				}
			}
			break;
		case('delete_group'): 
			Attributes::newInstance()->deleteGroup($group_id);
			unset($group_id);
			osc_add_flash_ok_message( __('Group deleted.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
			break;
		case('update_name'): 
			$name = Params::getParam('group_name');
			Attributes::newInstance()->setGroupName($group_id, $name);
			osc_add_flash_ok_message( __('Name updated.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
			break;		
		case('update_heading'): 
			$heading = Params::getParam('group_heading');
			Attributes::newInstance()->setGroupHeading($group_id, $heading);
			osc_add_flash_ok_message( __('Heading updated.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
			break;
		case('update_categories'): 
			$categories = Params::getParam('categories');
			Attributes::newInstance()->setGroupCategories($group_id, $categories);
			osc_add_flash_ok_message( __('Categories updated.', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
			break;			
	}
}

// Build group options for dropdowns
$groups = Attributes::newInstance()->getGroups();
$group_options = '';
foreach ($groups as $group) {
	$id = $group['pk_i_id'];
	$name = trim($group['s_name']);
	$group_options .= "<option value='" . $id . "'>" . $name . "</option>";
}
?>
<script type='text/javascript' src='<?php echo osc_assets_url('js/jquery.treeview.js'); ?>'></script>
<script type='text/javascript' src='<?php echo osc_plugin_url(__FILE__); ?>js/groups.js'></script>
<?php osc_show_flash_message(CA_PLUGIN_NAME);  ?>
<div id='custom_attributes'>
	<h2 class='render-title heading'><?php _e('Custom Attributes', CA_PLUGIN_NAME); ?></h2>
	<div class='config_column'>
		<div class='create_group'>
			<h2 class='render-title sub_heading'><?php _e('Create Group', CA_PLUGIN_NAME); ?></h2>
			<form method='post' action='<?php echo osc_admin_base_url(true);?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='create_group' />		
				<input class='group_input' type='text' name='group_name' value='' />
				<p><button class='btn btn-mini' type='submit'><?php _e('Create', CA_PLUGIN_NAME); ?></button></p>
			</form>
		</div>
<?php	if (!empty($group_options)) { ?>
		<div class='edit_group'>
			<h2 class='render-title sub_heading'><?php _e('Edit Group', CA_PLUGIN_NAME); ?></h2>
			<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='edit_group' />
				<select name='group_id'>
					<option class='select_option' value=''><?php _e('Select a group', CA_PLUGIN_NAME); ?></option>
					<?php echo $group_options; ?>
				</select>
				<p><button class='btn btn-mini' type='submit'><?php _e('Edit', CA_PLUGIN_NAME); ?></button></p>
			</form>
		</div>	
		<div class='delete_group'>
			<h2 class='render-title sub_heading'><?php _e('Delete Group', CA_PLUGIN_NAME); ?></h2>
			<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='delete_group' />	
				<select name='group_id'>
					<option class='select_option' value=''><?php _e('Select a group', CA_PLUGIN_NAME); ?></option>
					<?php echo $group_options; ?>
				</select>
				<p><button class='btn btn-mini' type='submit'><?php _e('Delete', CA_PLUGIN_NAME); ?></button></p>
			</form>
		</div>
<?php } ?>		
	</div>
<?php 
if (!empty($group_id)) { 
	$group_name = Attributes::newInstance()->getGroupName($group_id);
	$group_heading = Attributes::newInstance()->getGroupHeading($group_id);
	$categories = Category::newInstance()->toTreeAll();
	$selected = Attributes::newInstance()->getGroupCategories($group_id);		
?>
	<div class='config_column'>
		<div class='group_name'>
			<h2 class='render-title sub_heading'><?php _e('Name', CA_PLUGIN_NAME); ?></h2>
			<form method='post' method='post' method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='update_name' />	
				<input type='hidden' name='group_id' value='<?php echo $group_id; ?>' />	
				<input class='group_input' type='text' name='group_name' value='<?php echo $group_name; ?>' />
				<p><button class='btn btn-mini' type='submit'><?php _e('Update', CA_PLUGIN_NAME); ?></button></p>
			</form>		
		</div>
		<div class='group_heading'>
			<h2 class='render-title sub_heading'><?php _e('Heading', CA_PLUGIN_NAME); ?></h2>
			<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='update_heading' />	
				<input type='hidden' name='group_id' value='<?php echo $group_id; ?>' />	
				<input class='group_input' type='text' name='group_heading' value='<?php echo $group_heading; ?>' />
				<p><button class='btn btn-mini' type='submit'><?php _e('Update', CA_PLUGIN_NAME); ?></button></p>
			</form>		
		</div>
		<div class='group_categories'>
			<h2 class='render-title sub_heading'><?php _e('Categories', CA_PLUGIN_NAME); ?></h2>
			<form method='post' method='post' action='<?php echo osc_admin_base_url(true); ?>'>
				<input type='hidden' name='page' value='plugins' />
				<input type='hidden' name='action' value='renderplugin' />
				<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_groups.php' />
				<input type='hidden' name='plugin_action' value='update_categories' />	
				<input type='hidden' name='group_id' value='<?php echo $group_id; ?>' />	
				<p>
					<div class='form-label'>
						<a href='javascript:void(0);' onclick="checkAll('category_tree', true); return false;"><?php _e('Check all'); ?></a> &middot;
						<a href='javascript:void(0);' onclick="checkAll('category_tree', false); return false;"><?php _e('Uncheck all'); ?></a>
					</div>
					<ul id="category_tree">
						<?php CategoryForm::categories_tree($categories, $selected); ?>
					</ul>
				</p>
				<p><button class='btn btn-mini' type='submit'><?php _e('Update', CA_PLUGIN_NAME); ?></button></p>
			</form>		
		</div>
	</div>
<?php } ?>
<!-- end custom_attributes --></div>
<?php //END