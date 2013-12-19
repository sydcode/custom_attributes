<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/**
* Database functions for Custom Attributes plugin
* 
* @package OSClass
* @subpackage Attributes
* @since 3.0
*/

class Attributes extends DAO {
	/**
	 * It references to self object: Attributes.
	 * It is used as a singleton
	 * 
	 * @access private
	 * @since 3.0
	 * @var Attributes
	 */
	private static $instance;
	private $folder;
	const DATABASE_VERSION = '3';

	/**
	 * It creates a new Attributes object class ir if it has been created
	 * before, it return the previous object
	 * 
	 * @access public
	 * @since 3.0
	 * @return Attributes
	 */
	public static function newInstance() {
		if (!self::$instance instanceof self) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Construct
	 */
	function __construct() {
		parent::__construct();
		$this->folder = basename(dirname(__FILE__));		
	}
	
	/**
	 * Update database to latest version
	 * @return boolean
	 */
	public function updateDatabase() {
		$version = osc_get_preference('database_version', CA_PLUGIN_NAME);
		if (empty($version) || $version != self::DATABASE_VERSION) {
			$table_exists = $this->tableExists_Fields();
			switch ($version) {
				case 0: 
					// New installation
					if ($table_exists) {
						$this->import( $this->folder . '/sql/update0.sql');
					} else {
						$this->import( $this->folder . '/sql/struct.sql');
					}
					break;
				case 1:
					// Update from version 1
					if ($table_exists) {
						// Check for first version of dienast's fork
						$column_exists = $this->columnExists_Fields('b_range');
						if ($column_exists) {
							$this->import( $this->folder . '/sql/update2.sql');
						} else {
							$this->import( $this->folder . '/sql/update1.sql');
						}
					} else {
						error_log('Upgrade failed! Custom Attributes table does not exist.', 0);
						return false;
					}
					break;
				case 2:
					// Update from version 2
					if ($table_exists) {
						$this->import( $this->folder . '/sql/update2.sql');
					} else {
						error_log('Upgrade failed! Custom Attributes table does not exist.', 0);
						return false;
					}
					break;
			}		
			osc_set_preference('database_version', self::DATABASE_VERSION, CA_PLUGIN_NAME, 'INTEGER');
			osc_reset_preferences();
		}
		return true;
	}
	
	/**
	 * Get name of table storing group categories
	 * @return string
	 */
	public function getTable_Categories() {
		return DB_TABLE_PREFIX . 't_item_custom_attr_categories';
	}		
	
	/**
	 * Get name of table storing item descriptions
	 * @return string
	 */
	public function getTable_Description() {
		return DB_TABLE_PREFIX . 't_item_description';
	}	
	
	/**
	 * Get name of table storing attribute fields
	 * @return string
	 */
	public function getTable_Fields() {
		return DB_TABLE_PREFIX . 't_item_custom_attr_fields';
	}	
	
	/**
	 * Get name of table storing attribute groups
	 * @return string
	 */
	public function getTable_Groups() {
		return DB_TABLE_PREFIX . 't_item_custom_attr_groups';
	}	
	
	/**
	 * Get name of table storing attribute meta
	 * @return string
	 */
	public function getTable_Meta() {
		return DB_TABLE_PREFIX . 't_item_custom_attr_meta';
	}

	/**
	 * Get name of table storing attribute values
	 * @return string
	 */
	public function getTable_Values() {
		return DB_TABLE_PREFIX . 't_item_custom_attr_values';
	}	
	
	/**
	 * Check fields table exists
	 */
	public function tableExists_Fields() {
		$sql = "SHOW TABLES LIKE '" . $this->getTable_Fields() . "'";
		$result = $this->dao->query($sql);
		$result = $result->result();
		if (empty($result)) {
			return false;
		} else {
			return true; 
		}
	}
	
	/**
	 * Check fields column exists
	 */
	public function columnExists_Fields($column) {
		$sql = "
			SELECT * FROM information_schema.COLUMNS 
			WHERE TABLE_SCHEMA = DATABASE()
			AND TABLE_NAME = '" . $this->getTable_Fields() . "'
			AND COLUMN_NAME = '" . $column . "'
		";
		$result = $this->dao->query($sql);
		$result = $result->result();
		if (empty($result)) {
			return false;
		} else {
			return true; 
		}
	}	
	
	/**
	 * Get all attribute groups
	 * @param int $category_id 
	 * @return mixed
	 */
	public function getGroups($category_id = null) {
		$this->dao->select();
		$this->dao->from($this->getTable_Groups());
		if ($category_id != null) {
			$this->dao->join($this->getTable_Categories(), 'fk_i_group_id = pk_i_id');
			$this->dao->where('fk_i_category_id', $category_id);
		}
		$results = $this->dao->get();
		if (!$results) {
			return false;
		}
		return $results->result();		
	}	
	
	/**
	 * Get name of attribute group
	 * @param int $group_id 
	 * @return string
	 */
	public function getGroupName($group_id) {
		$this->dao->select();
		$this->dao->from($this->getTable_Groups());
		$this->dao->where('pk_i_id', $group_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}
		$row = $results->row();
		$name = isset($row['s_name']) ? $row['s_name'] : '';
		return $name;	
	}	

	/**
	 * Get heading of attribute group
	 * @param int $group_id 
	 * @return string
	 */
	public function getGroupHeading($group_id) {
		$this->dao->select();
		$this->dao->from($this->getTable_Groups());
		$this->dao->where('pk_i_id', $group_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}
		$row = $results->row();
		$heading = isset($row['s_heading']) ? $row['s_heading'] : '';
		return $heading;	
	}		
	
	/**
	 * Get order type of attribute group
	 * @param int $group_id 
	 * @return string
	 */
	public function getGroupOrderType($group_id) {
		$this->dao->select();
		$this->dao->from($this->getTable_Groups());
		$this->dao->where('pk_i_id', $group_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}
		$row = $results->row();
		if (!empty($row['s_order_type']) && 'custom' == $row['s_order_type']) {
			return 'custom';
		} else {
			return 'alpha';
		} 
	}	
	
	/**
	 * Get group ID from name
	 * @param string $group_name		 
	 * @return mixed
	 */
	public function getGroupID($group_name) {
		$this->dao->select('pk_i_id');
		$this->dao->from($this->getTable_Groups());
		$this->dao->where('s_name', $group_name);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return null;
		}
		$row = $results->row();
		$id = isset($row['pk_i_id']) ? $row['pk_i_id'] : null;
		return $id;	
	}		
	
	/**
	 * Get group categories
	 * @param int $group_id
	 * @return mixed
	 */
	public function getGroupCategories($group_id) {
		$this->dao->select('fk_i_category_id');
		$this->dao->from($this->getTable_Categories());
		$this->dao->where('fk_i_group_id', $group_id);
		$results = $this->dao->get();
		if (!$results) {
			return array();
		}
		$categories = $results->result();
		$count = count($categories);
		for ($i = 0; $i < $count; $i++) {
			$categories[$i] = $categories[$i]['fk_i_category_id'];
		}
		return $categories;
	}	

	/**
	 * Get attribute field
	 * @param int $field_id
	 * @return mixed
	 */
	public function getField($field_id) {
		$this->dao->select();
		$this->dao->from($this->getTable_Fields());
		$this->dao->where('pk_i_id', $field_id);
		$results = $this->dao->get();
		if ($results) {
			$field = $results->result();
			return $field[0];
		} else {
			return array();
		}
	}
	
	/**
	 * Get all attribute fields
	 * @param integer $group_id 
	 * @param string $order_type	 
	 * @return array
	 */
	public function getFields($group_id = null, $order_type = null) {
		if (empty($order_type)) {
			$order_type = osc_get_preference('order_type', CA_PLUGIN_NAME); 
		}
		if (empty($group_id)) {
			$this->dao->select('fk_i_field_id');
			$this->dao->from($this->getTable_Meta());
			$results = $this->dao->get();
			if ($results) {
				$results = $results->result();
				foreach ($results as $result) {
					$group_fields[] = $result['fk_i_field_id'];
				}
			}
		}
		$this->dao->select();
		$this->dao->from($this->getTable_Fields());
		if (empty($group_id)) {
			if (!empty($group_fields)) {
				$this->dao->whereNotIn('pk_i_id', $group_fields);
			}		
		} else {
			$this->dao->join($this->getTable_Meta(), 'fk_i_field_id = pk_i_id');
			$this->dao->where('fk_i_group_id', $group_id);
		}
		if ($order_type == 'custom') {
			$this->dao->orderBy('i_order','ASC');
		} else {
			$this->dao->orderBy('s_label','ASC');
		}
		$results = $this->dao->get();
		if ($results) {
			return $results->result();
		} else {
			return array();
		}
	}		
	
	/**
	 * Get attribute fields for a category
	 * @param integer $cat_id 	 
	 * @param string $order_type	 
	 * @return array
	 */	
	public function getCategoryFields($cat_id, $order_type = null) {	
		$fields = $this->getFields(null, $order_type); // ungrouped fields used for all categories
		$this->dao->select('fk_i_group_id');
		$this->dao->from($this->getTable_Categories());
		$this->dao->where('fk_i_category_id', $cat_id);
		$results = $this->dao->get();
		$results = $results->result();	
		foreach ($results as $result) {
			$group_id = $result['fk_i_group_id'];
			$group_fields = $this->getFields($group_id, $order_type);
			$fields = array_merge($fields, $group_fields);
		}		
		return $fields;
	}	
	
	/**
	 * Get type of attribute field
	 * @param string $field_id	 
	 * @return string
	 */
	public function getFieldType($field_id) {
		$this->dao->select('s_type');
		$this->dao->from($this->getTable_Fields());
		$this->dao->where('pk_i_id', $field_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}		
		$row = $results->row();
		$type = isset($row['s_type']) ? $row['s_type'] : '';
		return $type;
	}		
	
	/**
	 * Get all attributes
	 * @param string $item_id	 	 
	 * @return array
	 */
	public function getValues($item_id) {
		$this->dao->select();
		$this->dao->from($this->getTable_Values());
		$this->dao->where('fk_i_item_id', $item_id);
		$results = $this->dao->get();
		if (!$results) {
			return array();
		}
		return $results->result();
	}	
	
	/**
	 * Get items with an attribute
	 * @param string $field_id
	 * @param string $value
	 * @return array
	 */
	public function getItems($field_id, $value = null, $partial_match = false) {
		$this->dao->select('fk_i_item_id, fk_i_field_id, s_value');
		$this->dao->from($this->getTable_Values());
		$this->dao->where('fk_i_field_id', $field_id);
		if ($value != null) {
			if ($partial_match) {
				$this->dao->_like('s_value', $value);
			} else {
				$this->dao->where('s_value', $value);
			}
		}
		$results = $this->dao->get();
		if (!$results) {
			return array();
		}
		return $results->result();
	}	
	
	/**
	 * Get attribute value
	 * @param string $item_id	 
	 * @param string $field_id	 
	 * @return string
	 */
	public function getValue($item_id, $field_id) {
		$this->dao->select('s_value');
		$this->dao->from($this->getTable_Values());
		$this->dao->where('fk_i_item_id', $item_id);
		$this->dao->where('fk_i_field_id', $field_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}		
		$row = $results->row();
		$value = isset($row['s_value']) ? $row['s_value'] : '';
		return $value;
	}	
	
	/**
	 * Get attribute options
	 * @param string $field_id
	 * @param string $value (selected option)
	 * @return string
	 */
	public function getOptions($field_id, $value = null) {
		$this->dao->select('s_options');
		$this->dao->from($this->getTable_Fields());
		$this->dao->where('pk_i_id', $field_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}		
		$row = $results->row();
		$options = isset($row['s_options']) ? $row['s_options'] : '';
		return $options;
	}		

	/**
	 * Get item title
	 * @param string $item_id	 
	 * @return string
	 */
	public function getItemTitle($item_id) {
		$this->dao->select('s_title');
		$this->dao->from($this->getTable_Description());
		$this->dao->where('fk_i_item_id', $item_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return '';
		}		
		$row = $results->row();
		$title = isset($row['s_title']) ? $row['s_title'] : '';
		return $title;
	}		
	
	/**
	 * Insert attribute value
	 * @param int $item_id
	 * @param int $field_id
	 * @param string $value
	 * @return mixed	 
	 */
	public function insertValue($item_id, $field_id, $value)	{
		$args = array(
			'fk_i_item_id' => $item_id, 
			'fk_i_field_id' => $field_id, 
			's_value' => $value
		);
		return $this->dao->insert($this->getTable_Values(), $args);
	}	
	
	/**
	 * Insert attribute field
	 * @param string $type
	 * @param string $label
	 * @param string $options	 
	 * @param boolean $range
	 * @param string $steps (for range)	 
	 * @param boolean $required
	 * @param boolean $search
	 * @param boolean $search_limits	 
	 * @return mixed
	 */
	public function insertField($type, $label, $options = null, $range = false, $steps = null, $required = false, $search = true, $search_limits = false) {
		$args = array( 
			's_type' => $type, 
			's_label' => $label, 
			's_options' => $options, 
			'b_range' => $range,
			's_steps' => $steps,
			'b_required' => $required, 
			'b_search' => $search,
			'b_search_limits' => $search_limits			
		);
		$result = $this->dao->insert($this->getTable_Fields(), $args);
		if ($result) {
			return $this->dao->insertedId();
		} else {
			return false;
		}
	}	
	
	/**
	 * Insert attribute group
	 * @param string $name
	 * @param string $heading
	 * @return mixed	 
	 */
	public function insertGroup($name, $heading = null) {
		$args = array('s_name' => $name, 's_heading' => $heading);
		return $this->dao->insert($this->getTable_Groups(), $args);
	}	
	
	/**
	 * Insert group category
	 * @param int $group_id
	 * @param int $category_id
	 * @return mixed	 
	 */
	public function insertGroupCategory($group_id, $category_id) {
		$args = array('fk_i_group_id' => $group_id, 'fk_i_category_id' => $category_id);
		return $this->dao->insert($this->getTable_Categories(), $args);
	}	
	
	/**
	 * Insert meta for group and field
	 * @param int $group_id
	 * @param int $field_id
	 * @return mixed	 
	 */
	public function insertMeta($group_id, $field_id)	{
		$args = array(
			'fk_i_group_id' => $group_id, 
			'fk_i_field_id' => $field_id
		);
		return $this->dao->insert($this->getTable_Meta(), $args);
	}	
	
	/**
	 * Set attribute field
	 * @param int $field_id
	 * @param string $label
	 * @param string $options
	 * @param boolean $range
	 * @param string $steps (for range)	 	 
	 * @param boolean $required
	 * @param boolean $search
	 * @param boolean $search_limits	 
	 * @return boolean	
	 */
	public function setField($field_id, $type, $label, $options = null, $range = false, $steps = null, $required = false, $search = false, $search_limits = false) {	
		$where = array('pk_i_id' => $field_id);
		$set = array(
			's_type' => $type, 
			's_label' => $label, 
			's_options' => $options, 
			'b_range' => $range,
			's_steps' => $steps,			
			'b_required' => $required, 
			'b_search' => $search,
			'b_search_limits' => $search_limits
		);
		return $this->_update($this->getTable_Fields(), $set, $where);
	}		
	
	/**
	 * Set attribute value
	 * @param int $item_id
	 * @param int $field_id
	 * @param string $value
	 * @return boolean	
	 */
	public function setValue($item_id, $field_id, $value) {	
		$exists = $this->valueExists($item_id, $field_id);
		if (!$exists) {
			return $this->insertValue($item_id, $field_id, $value);
		}
		$current = $this->getValue($item_id, $field_id);
		if ($value != $current) {
			$where = array('fk_i_item_id' => $item_id, 'fk_i_field_id' => $field_id);
			$set = array('s_value' => $value);
			return $this->_update($this->getTable_Values(), $set, $where);
		}
	}	
	
	/**
	 * Set attribute group
	 * @param int $group_id
	 * @param string $name
	 * @param string $heading
	 * @param boolean $order_type
	 * @param array $categories
	 * @return boolean	
	 */
	public function setGroup($group_id, $name, $heading, $order_type, $categories) {	
		$where = array('pk_i_id' => $group_id);
		$set = array('s_name' => $name, 's_heading' => $heading, 's_order_type' => $order_type, 'a_categories' => $categories);
		$this->_update($this->getTable_Groups(), $set, $where);
		// Update group categories
		$this->deleteGroupCategories($group_id);
		foreach ($categories as $category_id) {
			$this->insertGroupCategory($group_id, $category_id);
		}
		return true;
	}		
	
	/**
	 * Set name of attribute group
	 * @param int $group_id
	 * @param string $name
	 * @return boolean	
	 */
	public function setGroupName($group_id, $name) {	
		$where = array('pk_i_id' => $group_id);
		$set = array('s_name' => $name);
		return $this->_update($this->getTable_Groups(), $set, $where);
	}		

	/**
	 * Set heading of attribute group
	 * @param int $group_id
	 * @param string $heading
	 * @return boolean	
	 */
	public function setGroupHeading($group_id, $heading) {	
		$where = array('pk_i_id' => $group_id);
		$set = array('s_heading' => $heading);
		return $this->_update($this->getTable_Groups(), $set, $where);
	}	
	
	/**
	 * Set order type of attribute group
	 * @param int $group_id
	 * @param string $order_type
	 * @return boolean	
	 */
	public function setGroupOrderType($group_id, $order_type) {	
		$where = array('pk_i_id' => $group_id);
		$set = array('s_order_type' => $order_type);
		return $this->_update($this->getTable_Groups(), $set, $where);
	}		
	
	/**
	 * Set group categories
	 * @param int $group_id
	 * @param array $categories	 
	 * @return boolean	
	 */
	public function setGroupCategories($group_id, $categories) {	
		$this->deleteGroupCategories($group_id);
		foreach ($categories as $category_id) {
			$this->insertGroupCategory($group_id, $category_id);
		}
		return true;
	}			
	
	/**
	 * Set order of display for attribute field
	 * @param int $field_id
	 * @param int $order
	 * @return boolean	
	 */
	public function setFieldOrder($field_id, $order) {	
		$where = array('pk_i_id' => $field_id);
		$set = array('i_order' => $order);
		return $this->_update($this->getTable_Fields(), $set, $where);
	}	
	
	/**
	 * Delete attribute field
	 * @param int $field_id
	 * @return boolean	 
	 */
	public function deleteField($field_id) {
		$this->dao->delete($this->getTable_Values(), array('fk_i_field_id' => $field_id));
		$this->dao->delete($this->getTable_Meta(), array('fk_i_field_id' => $field_id));
		return $this->dao->delete($this->getTable_Fields(), array('pk_i_id' => $field_id));
	}		
	
	/**
	 * Delete attribute value
	 * @param int $item_id
	 * @param int $field_id
	 * @return boolean	 
	 */
	public function deleteValue($item_id, $field_id) {
		return $this->dao->delete($this->getTable_Values(), array('fk_i_item_id' => $item_id, 'fk_i_field_id' => $field_id));
	}	
	
	/**
	 * Delete attribute group
	 * @param int $group_id
	 * @return boolean	 
	 */
	public function deleteGroup($group_id) {
		$this->deleteGroupMeta($group_id);
		$this->deleteGroupCategories($group_id);
		return $this->dao->delete($this->getTable_Groups(), array('pk_i_id' => $group_id));
	}	

	/**
	 * Delete group categories
	 * @param int $group_id
	 * @return boolean	 
	 */
	public function deleteGroupCategories($group_id) {
		return $this->dao->delete($this->getTable_Categories(), array('fk_i_group_id' => $group_id));
	}	
	
	/**
	 * Delete group meta
	 * @param int $group_id
	 * @return boolean	 
	 */
	public function deleteGroupMeta($group_id) {
		return $this->dao->delete($this->getTable_Meta(), array('fk_i_group_id' => $group_id));
	}		
	
	/**
	 * Delete attribute values when item is deleted
	 * @param int $item_id
	 * @return boolean
	 */
	public function deleteItemValues($item_id) {
		return $this->dao->delete($this->getTable_Values(), array('fk_i_item_id' => $item_id));
	}
	
	/**
	 * Import sql file
	 * @param type $file 
	 */
	public function import($file) {
		$path = osc_plugin_resource($file) ;
		$sql = file_get_contents($path);
		if(!$this->dao->importSQL($sql)) {
			throw new Exception($this->dao->getErrorLevel().' - '.$this->dao->getErrorDesc());
		}
	}
					
	/**
	 * Remove tables
	 */
	public function uninstall()	{
		$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Categories()));
		$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Meta()));
		$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Groups()));
		$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Values()));
		$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_Fields()));
	}
	
	/**
	 * Update values
	 * @param int $table
	 * @param int $values 
	 * @param int $where
	 * @return boolean
	 */
	function _update($table, $values, $where) {
		$this->dao->from($table);
		$this->dao->set($values);
		$this->dao->where($where);
		return $this->dao->update();
	}
	
	/**
	 * Check if attribute value exists
	 * @param string $item_id	 
	 * @param string $field_id	 
	 * @return boolean
	 */
	public function valueExists($item_id, $field_id) {
		$this->dao->select('s_value');
		$this->dao->from($this->getTable_Values());
		$this->dao->where('fk_i_item_id', $item_id);
		$this->dao->where('fk_i_field_id', $field_id);
		$this->dao->limit(1);
		$results = $this->dao->get();
		if (!$results) {
			return false;
		}		
		$row = $results->row();
		if (isset($row['s_value'])) {
			return true;
		} else {
			return false;
		}
	}	
	
}

// END