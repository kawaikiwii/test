<?php

function smarty_function_edit($params, &$smarty)
{
    if (empty($params['widget'])) {
        $smarty->trigger_error("editable: missing 'widget' parameter");
        return;
    } else {
        $widget = $params['widget'];
    }

	if (empty($params['widget'])) {
		$smarty->trigger_error("editable: missing 'name' parameter");
		return;
	} else {
		$name = $params['name'];
	}

	$value = getArrayParameter($params, 'value', null);
	$type = getArrayParameter($params, 'type', 'text');
	
	if($widget['mode'] & wcmWidget::VIEW_SETTINGS) {
		switch($type) {
			case 'text':
			default:
				return '<input type="text" name="'.$name.'" value="'.$value.'" />';
		}
	} else {
		return $value;
	}
}
