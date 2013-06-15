ALTER TABLE /*TABLE_PREFIX*/t_item_custom_attr_fields ADD COLUMN s_steps INT(10) UNSIGNED NULL AFTER s_options;
ALTER TABLE /*TABLE_PREFIX*/t_item_custom_attr_fields ADD COLUMN b_range BOOLEAN NULL AFTER s_options;
ALTER TABLE /*TABLE_PREFIX*/t_item_custom_attr_fields ADD COLUMN b_search_limits BOOLEAN NULL AFTER b_search;