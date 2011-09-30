<?php
// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

$action = getArrayParameter($_REQUEST, 'action', '');

switch($action)
{
    case 'renderObject':
        $relatedClass = getArrayParameter($_REQUEST, 'relatedClass');
        $relatedId = getArrayParameter($_REQUEST, 'relatedId', 0);
        if($relatedClass)
        {
            if (is_numeric($relatedId))
            {
                $bizobject = new $relatedClass;
                $bizobject->refresh($relatedId);
            }
            else
            {
                $bizobject = new $relatedClass;
                $bizobject->initFromXML($_SESSION['wcm']['tmpObjects'][$relatedId]['obj']);
            }

            $params = array(
                'bizobject' => $bizobject->getAssocArray(false),
                'pk' => getArrayParameter($_REQUEST, 'pk', '_br_2')
            );

            $tg = new wcmTemplateGenerator();
            echo $tg->executeTemplate('relations/related.tpl', $params);

            //echo wcmXML::processXSLT($bizobject->toXML(), WCM_DIR . '/xsl/list/renderObject.xsl', $params);
        }
        break;
}