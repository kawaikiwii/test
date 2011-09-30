<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverTags extends wcmWidget {

	public function getLabel()
	{
		return 'Tags';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/tags.tpl');	
	}

}