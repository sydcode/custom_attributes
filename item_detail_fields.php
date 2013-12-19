<?php
if ( !defined('ABS_PATH') ) { 
	exit('ABS_PATH is not loaded. Direct access is not allowed.');
}
?>
	<table>
		<tbody>
<?php 
$hide_empty = osc_get_preference('hide_empty', CA_PLUGIN_NAME);
foreach ($fields as $field) {
	$type = $field['s_type'];
	$label = $field['s_label'];
	$value = Attributes::newInstance()->getValue($item_id, $field['pk_i_id']);
	if (!empty($hide_empty) && 'hide' == $hide_empty) {
		if ('checkbox' != $type && '' == trim($value)) {
			continue;
		}
	}	
	if ($type == 'checkbox') {
		if ($value == 'checked') $value = 'Yes';
		else $value = 'No';
	}
?>
			<tr>
				<td class='detail_label'><?php echo $label; ?></td>
				<td class='detail_label'><?php echo $value; ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
<?php //END