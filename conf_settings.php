<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

// Process actions
$action = Params::getParam('plugin_action');
if (!empty($action)) {
	if ('save_settings' == $action) {
		$hide_empty = Params::getParam('hide_empty');
		$uninstall_data = Params::getParam('uninstall_data');
		osc_set_preference('hide_empty', $hide_empty, CA_PLUGIN_NAME, 'STRING');
		osc_set_preference('uninstall_data', $uninstall_data, CA_PLUGIN_NAME, 'STRING');
		osc_reset_preferences();
		osc_add_flash_ok_message( __('Settings saved', CA_PLUGIN_NAME), CA_PLUGIN_NAME);
	}
}
?>
<?php osc_show_flash_message(CA_PLUGIN_NAME); ?>
<div id='custom_attributes'>
	<h2 class='render-title heading'><?php _e('Custom Attributes', CA_PLUGIN_NAME); ?></h2>
	<div class='settings'>
		<h2 class='render-title sub_heading'><?php _e('Settings', CA_PLUGIN_NAME); ?></h2>
		<form method='post' action='<?php echo osc_admin_base_url(true); ?>'>
			<input type='hidden' name='page' value='plugins' />
			<input type='hidden' name='action' value='renderplugin' />
			<input type='hidden' name='file' value='<?php echo osc_plugin_folder(__FILE__); ?>conf_settings.php' />
			<input type='hidden' name='plugin_action' value='save_settings' />	
			<div class='form-horizontal'>
				<div class='form-row'>
					<div class='form-label'><?php _e('Attributes', CA_PLUGIN_NAME); ?></div>		
					<div class='form-controls'>					
						<div class='select-box undefined'>
							<?php 
								$hide_empty = osc_get_preference('hide_empty', CA_PLUGIN_NAME); 
								$checked = (!empty($hide_empty) && 'hide' == $hide_empty) ? 'checked="checked"' : '';
							?>
							<div class='form-label-checkbox'>
							<label>
								<input type='checkbox' value='hide' name='hide_empty' <?php echo $checked; ?> />
								<?php _e('Hide empty attributes', CA_PLUGIN_NAME); ?>
              </label>
							</div>							
						</div>
						<div class='help-box'>
							<?php _e('Only show attributes that have a value for the listing', CA_PLUGIN_NAME); ?>
						</div>
					</div>
				</div>					
				<div class='form-row'>
					<div class='form-label'><?php _e('Uninstall', CA_PLUGIN_NAME); ?></div>		
					<div class='form-controls'>					
						<div class='select-box undefined'>
							<?php 
								$uninstall_data = osc_get_preference('uninstall_data', CA_PLUGIN_NAME); 
								$checked = (!empty($uninstall_data) && 'uninstall' == $uninstall_data) ? 'checked="checked"' : '';
							?>
							<div class='form-label-checkbox'>
							<label>
								<input type='checkbox' value='uninstall' name='uninstall_data' <?php echo $checked; ?> />
								<?php _e('Delete data', CA_PLUGIN_NAME); ?>
              </label>
							</div>							
						</div>
						<div class='help-box'>
							<?php _e('Delete groups, attributes and values when the plugin is uninstalled', CA_PLUGIN_NAME); ?>
						</div>
					</div>
				</div>				
			</div>
			<p><input type='submit' class='btn btn-submit' value='Save changes' id='save_changes' /></p>
		</form>		
	</div>
	<div class='help'>
		<h2 class='render-title sub_heading'><?php _e('Help', CA_PLUGIN_NAME); ?></h2>
		<p><?php _e('A PHP template function is provided for showing custom attributes in another location.', CA_PLUGIN_NAME); ?><br />
		<?php _e('This function shows attributes for the current item and category.', CA_PLUGIN_NAME); ?></p>
		<p class='code'>if (function_exists('custom_attributes'))<br />custom_attributes();</p>
		<p><?php _e('Add arguments if you want to show attributes for another item and category.', CA_PLUGIN_NAME); ?></p>
		<p class='code'>if (function_exists('custom_attributes'))<br />custom_attributes(itemID, categoryID);</p>
	</div>	
<!-- end custom_attributes --></div>
<?php //END