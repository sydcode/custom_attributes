	<table>
		<tbody>
<?php 
foreach ($fields as $field) {
	$type = $field['s_type'];
	$label = $field['s_label'];
	$value = Attributes::newInstance()->getValue($item_id, $field['pk_i_id']);
	if ($type == 'checkbox') {
		if ($value == 'checked') $value = 'Yes';
		else $value = 'No';
	}
?>
			<tr>
				<td class='detail_label'><?php _e($label, PLUGIN_NAME); ?></td>
				<td class='detail_label'><?php _e($value, PLUGIN_NAME); ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
<?php //END