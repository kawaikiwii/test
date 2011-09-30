<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverEntitiesPn extends wcmWidget {

	public function getLabel()
	{
		return 'People';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/entities_pn.tpl');	
	}

}