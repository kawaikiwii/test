<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverEntitiesGl extends wcmWidget {

	public function getLabel()
	{
		return 'Geographic Locations';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/entities_gl.tpl');	
	}

}