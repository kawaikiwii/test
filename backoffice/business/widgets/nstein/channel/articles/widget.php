<?php

/**
 *  Widget
 *
 */

class wcmWidgetNsteinChannelArticles extends wcmWidget {

    public function getLabel()
    {
        return 'Last articles';
    }

    public function displaySettings()
    {
        return false;
        $list = range(0,10);
        $html = 'Item Count : <select name="nb">'.renderHtmlOptions($list, (int)getArrayParameter($this->settings, 'nb', 5), false).'</select>';
        return $html;
    }
    
    public function savePhoto($argIdentifiers, $argValue)
    {
        $relation = new wcmBizrelation();
        $relation->beginEnum('sourceId='.$argIdentifiers[0].' AND sourceClass="article" AND destinationId='.$argIdentifiers[1].' AND destinationClass="photo"');
        $relation->nextEnum();
        $relation->destinationId = $argValue;
        $relation->save();
    }
}

?>
