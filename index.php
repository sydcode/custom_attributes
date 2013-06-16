<?php
/*
Plugin Name: Custom Attributes
Plugin URI: http://www.osclass.org/
Description: Create custom attributes for your listings. Version Beta by <a href="http://forums.osclass.org/index.php?action=profile;u=12290" title="View sydcode's profile">sydcode</a>.<br/>Thanks to <a href="http://forums.osclass.org/index.php?action=profile;u=11728" title="View sharkey's profile">sharkey</a> for sponsoring this plugin. Also thanks to <a href="http://forums.osclass.org/index.php?action=profile;u=1728" title="View Jay's profile">Jay</a> and <a href="http://forums.osclass.org/index.php?action=profile;u=16575" title="View dienast's profile">dienast</a> for their improvements.
Version: Beta
Author: sydcode
Author URI: http://forums.osclass.org/index.php?action=profile;u=12290
Short Name: custom_attributes
Plugin update URI: custom_attributes

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('CA_PLUGIN_NAME', 'custom_attributes');

// Load database class and check for updates
require 'attributes.php';
Attributes::newInstance()->updateDatabase();

/**
 * Add table to database for storing attributes
 */
function ca_call_after_install() {
	Attributes::newInstance()->updateDatabase();
}

/**
 * Show standard configuraton panel
 */
function ca_admin_configuration() {
	osc_plugin_configure_view(osc_plugin_path(__FILE__));
}

/**
 * Remove attributes table from database
 */
function ca_call_after_uninstall() {
	Attributes::newInstance()->uninstall();
	osc_delete_preference('order_type', CA_PLUGIN_NAME);
	osc_delete_preference('heading', CA_PLUGIN_NAME);
	osc_delete_preference('database_version', CA_PLUGIN_NAME);
	osc_reset_preferences();
}

/**
 * Show attributes for new listing 
 * @param int $cat_id  
 */
function ca_form($cat_id = null) {
	if ($cat_id != null) {
		if(osc_is_this_category(CA_PLUGIN_NAME, $cat_id)) {
			$groups = Attributes::newInstance()->getGroups($cat_id);
			$order_type = osc_get_preference('order_type');
			$fields = Attributes::newInstance()->getFields(null, $order_type);				
			if(!empty($groups) || !empty($fields)) {
				require 'item_edit.php';
			}				
		}
	}
}

/**
 * Process attributes for new listing 
 * @param array $item 
 */
function ca_form_post($item = null) {
	if (!empty($item)) {
		$item_id = $item['fk_i_item_id'];
		$cat_id = $item['fk_i_category_id'];
		if ($item_id != null && $cat_id != null) {
			if (osc_is_this_category(CA_PLUGIN_NAME, $cat_id)) {
				$fields = Attributes::newInstance()->getCategoryFields($cat_id);
				foreach ($fields as $field) {
					$value = Params::getParam('field_' . $field['pk_i_id']);
					Attributes::newInstance()->insertValue($item_id, $field['pk_i_id'], $value);
				}
			}
		}
	}
}

/** 
 * Show attributes in search form
 * @param int $cat_id 
 */
function ca_search_form($cat_id = null) {
	if($cat_id != null) {
		foreach($cat_id as $id) {
			if(osc_is_this_category(CA_PLUGIN_NAME, $id)) {
				$groups = Attributes::newInstance()->getGroups($id);
				$order_type = osc_get_preference('order_type');
				$fields = Attributes::newInstance()->getFields(null, $order_type);
				if(!empty($groups) || !empty($fields)) {
					include 'search_form.php';
				}
				break;
			}
		}
	}
}

/** 
 * Add search conditions
 * @param array $params 
 */
function ca_search_conditions($params = null) {
	if (is_null($params)) return;
	if (!empty($params['sPattern'])) {
		$pattern = trim($params['sPattern']);
		Search::newInstance()->addConditions("s_title LIKE '%". $pattern . "%'");	
	}
	$values_table = Attributes::newInstance()->getTable_Values();
	foreach($params as $key => $value) {
		$has_min = strpos($key, 'min_');
		$has_max = strpos($key, 'max_');
		if ($has_min === 0) {
			$field_id = str_replace('min_field_', '', $key);
		} elseif ($has_max === 0) {
			$field_id = str_replace('max_field_', '', $key);
		} else {
			$field_id = str_replace('field_', '', $key);
		}
		$field_type = Attributes::newInstance()->getFieldType($field_id);
		if ($field_id == $key || empty($value)) continue;
		$subquery = "SELECT fk_i_item_id FROM " . $values_table . " WHERE fk_i_field_id = " . $field_id;
		if ($has_min === 0 && $field_type == 'text') {
			$subquery .= " AND CAST(s_value AS DECIMAL) >= " . $value;
		} elseif ($has_max === 0 && $field_type == 'text') {
			$subquery .= " AND CAST(s_value AS DECIMAL) <= " . $value;
		} else {
			if ($field_type == 'text' || $field_type == 'textarea') {
				$subquery .= " AND s_value LIKE '%" . $value . "%'";
			} else {
				$subquery .= " AND s_value = '" . $value . "'";
			}
		}
		Search::newInstance()->addConditions("pk_i_id IN (" . $subquery. ")");
	}	
}

/**
 * Show attributes for listing
 */
function ca_item_detail() {
	$cat_id = osc_item_category_id();
	if (osc_is_this_category(CA_PLUGIN_NAME, $cat_id)) {
		$item_id = osc_item_id();
	  $groups = Attributes::newInstance()->getGroups($cat_id);
		$order_type = osc_get_preference('order_type');
		$fields = Attributes::newInstance()->getFields(null, $order_type);				
		if(!empty($groups) || !empty($fields)) {
			require 'item_detail.php';
		}
	}
}

/**
 * Show attributes on edit page
 * @param int $cat_id 
 * @param int $item_id  
 */
function ca_item_edit($cat_id = null, $item_id = null) {
	if ($cat_id != null && $item_id != null) {
		if (osc_is_this_category(CA_PLUGIN_NAME, $cat_id)) {
			$groups = Attributes::newInstance()->getGroups($cat_id);
			$order_type = osc_get_preference('order_type');
			$fields = Attributes::newInstance()->getFields(null, $order_type);				
			if(!empty($groups) || !empty($fields)) {
				require 'item_edit.php';
			}		
		}
	}
}

/**
 * Process attributes from edit page
 * @param array $item
 */
function ca_item_edit_post($item = null) {
	if (!empty($item)) {
		$item_id = $item['fk_i_item_id'];
		$cat_id = $item['fk_i_category_id'];
		if ($item_id != null && $cat_id != null) {
			if( osc_is_this_category(CA_PLUGIN_NAME, $cat_id)) {	
				$fields = Attributes::newInstance()->getCategoryFields($cat_id);
				foreach ($fields as $field) {
					$value = Params::getParam('field_' . $field['pk_i_id']);
					Attributes::newInstance()->setValue($item_id, $field['pk_i_id'], $value);
				}
			}
		}
	}
}

/**
 * Create select options for an attribute
 * @param int $field_id
 * @param int $value
 * @return text
 */
function ca_select_options($field_id, $value = null) {
	$options = Attributes::newInstance()->getOptions($field_id, $value);
	if (empty($options)) return;
	$options = explode(',', $options);
	$output = "<option value=''>" . __('Select a value', CA_PLUGIN_NAME) . "</option>" . PHP_EOL;
	foreach ($options as $option) {
		$option = trim($option);
		if ($value != null && $option == $value) {
			$selected = " selected='selected'";
		} else {
			$selected = '';
		}
		$output .= "<option value='" . $option . "'" . $selected . ">" . $option . "</option>" . PHP_EOL;
	}
	echo $output;
}

/**
 * Create radio buttons for an attribute
 * @param int $field_id
 * @param int $value
 * @return text
 */
function ca_radio_buttons($field_id, $name, $value = null, $required = null, $search = false) {
	$options = Attributes::newInstance()->getOptions($field_id, $value);
	if (empty($options)) return;
	if (empty($required)) {
		$class = '';
	} else {
		$class = ' class="required"';
	}	
	$output = '';
	if ($search) {
		if ($value == null) {
			$checked = " checked='checked'";
		}	else {
			$checked = '';
		}
		$output .= "<div><label class='radio_button_label'>";
		$output .= "<input class='radio_button' type='radio' name='" . $name . "'" . $class . " value=''" . $checked . " />";
		$output .= __('Unknown', CA_PLUGIN_NAME) . "</label></div>" . PHP_EOL;	
	}
	$options = explode(',', $options);
	foreach ($options as $option) {
		$option = trim($option);
		if ($value != null && $option == $value) {
			$checked = " checked='checked'";
		} else {
			$checked = '';
		}
		$output .= "<div><label class='radio_button_label'>";
		$output .= "<input class='radio_button' type='radio' name='" . $name . "'" . $class . " value='" . $option . "'" . $checked . " />";
		$output .= $option . "</label></div>" . PHP_EOL;
	}
	echo $output;
}

/**
 * Add links to admin menu	
 * @return text
 */
function ca_admin_menu() {
	echo '<h3><a href="#">' . __('Custom Attributes', CA_PLUGIN_NAME) . '</a></h3> 
	<ul>
		<li><a href="' . osc_admin_configure_plugin_url("custom_attributes/index.php") . '">&raquo; ' . __('Configure Plugin', CA_PLUGIN_NAME) . '</a></li>
		<li><a href="' . osc_admin_render_plugin_url("custom_attributes/conf_groups.php") . '">&raquo; ' . __('Edit Groups', CA_PLUGIN_NAME) . '</a></li>
		<li><a href="' . osc_admin_render_plugin_url("custom_attributes/conf.php") . '">&raquo; ' . __('Edit Attributes', CA_PLUGIN_NAME) . '</a></li>
		<li><a href="' . osc_admin_render_plugin_url("custom_attributes/conf_values.php") . '">&raquo; ' . __('Edit Values', CA_PLUGIN_NAME) . '</a></li>
	</ul>';
}

/**
 * Add stylesheets to front header		
 * @return text 
 */
function ca_front_header() {
	echo '<link href="' . osc_plugin_url(__FILE__) . 'css/front_styles.css" rel="stylesheet" type="text/css">' . PHP_EOL;
	$theme = osc_theme();
	if ($theme == 'bender') {
		echo '<link href="' . osc_plugin_url(__FILE__) . 'css/bender.css" rel="stylesheet" type="text/css">' . PHP_EOL;
		osc_enqueue_script('jquery-ui');
	} else {
		echo '<link href="' . osc_base_url(false) . 'oc-includes/osclass/gui/css/jquery-ui/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">' . PHP_EOL;
	}
}

/**
 * Add stylesheets to admin header		
 * @return text 
 */
function ca_admin_header() {
  echo '<link href="' . osc_plugin_url(__FILE__) . 'css/admin_styles.css" rel="stylesheet" type="text/css">' . PHP_EOL;
	echo '<link href="' . osc_base_url(false) . 'oc-includes/osclass/gui/css/jquery-ui/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css">' . PHP_EOL;	
}

/**
 * Delete attribute values when item is deleted
 */
function ca_delete_item($item_id) {       
	Attributes::newInstance()->deleteItemValues($item_id);
}

/**
 * Save attribute values into session before inserting item
 */
function ca_pre_item_post() {
	$fields = Params::getParam('fields');
	if (!empty($fields) && is_array($fields)) {
		foreach ($fields as $id) {
			$field = Attributes::newInstance()->getField($id);
			$type = $field['s_type'];
			$name = 'field_' . $id;
			$value = Params::getParam($name);
			if ($type == 'checkbox' && empty($value)) {
				$value = 'unchecked';
			} 
			Session::newInstance()->_setForm($name, $value);
			Session::newInstance()->_keepForm($name);
		}
	}
}

/**
 * Save attribute values into session
 */
function ca_save_inputs_session() {
	$fields = Params::getParam('fields');
	if (!empty($fields) && is_array($fields)) {
		foreach ($fields as $id) {
			$name = 'field_' . $id;
			Session::newInstance()->_keepForm($name);
		}
	}
}

/**
 * Function Hooks
 */
 
// Hook for registering plugin 
osc_register_plugin(osc_plugin_path(__FILE__), 'ca_call_after_install');		

// Hook for showing standard configuraton panel
osc_add_hook(osc_plugin_path(__FILE__) . "_configure", 'ca_admin_configuration');

// Hook for uninstall link
osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'ca_call_after_uninstall');

// Hook for new item 
osc_add_hook('item_form', 'ca_form');

// Hook for processing new item
osc_add_hook('posted_item', 'ca_form_post');

// Hook for showing extra fields in search form
osc_add_hook('search_form', 'ca_search_form');

// Hook for adding new search conditions
osc_add_hook('search_conditions', 'ca_search_conditions');

// Hook for showing attributes for item
osc_add_hook('item_detail', 'ca_item_detail');

// Hook for showing attributes form on admin
osc_add_hook('item_edit', 'ca_item_edit');

// Hook for deleting attribute values when item is deleted
osc_add_hook('delete_item', 'ca_delete_item');

// Hook for processing attributes from admin form
osc_add_hook('edited_item', 'ca_item_edit_post');

// Hook for adding links to admin menu
osc_add_hook('admin_menu', 'ca_admin_menu');

// Hook for adding content to front header
osc_add_hook('header', 'ca_front_header');

// Hook for adding content to admin header
osc_add_hook('admin_header', 'ca_admin_header');

// Hook for saving attribute values into session before inserting item
osc_add_hook('pre_item_post', 'ca_pre_item_post') ;

// Hook for saving attrbiute values into session
osc_add_hook('save_input_session', 'ca_save_inputs_session' );

// END