<?php
class wcmWidgetNsteinArticleWidget extends wcmWidget 
{
    public function getLabel()
    {
        return $this->context['title'];
    }

    public function render()
    {
        return parent::render('widget/articleWidget.tpl');    
    }

}
?>