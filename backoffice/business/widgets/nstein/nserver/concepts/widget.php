<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinNserverConcepts extends wcmWidget {

    public function getLabel()
    {
        return 'Concepts';
    }

    public function render()
    {
        return parent::render('demo/blocks/tme/concepts.tpl');  
    }

}