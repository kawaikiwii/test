<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverSubjectMap extends wcmWidget {

	public function getLabel()
	{
		return 'Subject Map';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/subject_map.tpl');	
	}

}