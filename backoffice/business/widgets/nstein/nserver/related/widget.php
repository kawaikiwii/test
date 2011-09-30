<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverRelated extends wcmWidget {

	public function getLabel()
	{
		return 'Related Content';
	}

	public function render()
	{
		return parent::render('demo/blocks/tme/related.tpl');	
	}

}