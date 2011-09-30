<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverSimilar extends wcmWidget {

	public function getLabel()
	{
		return 'Similar Items';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/similar.tpl');	
	}

}