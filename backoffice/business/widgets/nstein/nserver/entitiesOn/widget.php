<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverEntitiesOn extends wcmWidget {

	public function getLabel()
	{
		return 'Organizations';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/entities_on.tpl');	
	}

}