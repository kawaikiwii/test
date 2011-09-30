<?php

// Initialize system
require_once dirname(__FILE__).'/../initWebApp.php';

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header("Content-Type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
echo '<ajax-response>' . PHP_EOL;


// Action a effectuer
$action = getArrayParameter($_REQUEST, 'action', '');

// Les données du context
$bizClass = getArrayParameter($_REQUEST, 'bizClass', '');
$bizId = getArrayParameter($_REQUEST, 'bizId', '');
$bizobject = new $bizClass(wcmProject::getInstance(), $bizId);

// les données du Widget
$widgetClass = getArrayParameter($_REQUEST, 'widgetClass', false);
$guid = getArrayParameter($_REQUEST, 'guid', false);


switch($action) {

        case 'updateWidget':
                $divId = getArrayParameter($_REQUEST, 'divid', '');
                if($widgetClass) {
                    $widget = new $widgetClass(wcmWidget::VIEW_SETTINGS, array(), $bizobject, $guid);
                    $html = $widget->display();
                }
                echo '<response type="item" id="'.$divId.'"><![CDATA['.$html.'<script type="text/javascript">portal.update();</script>]]></response>'."\n";
                break;

        case 'applySettings':
        case 'saveSettings':
                
                parse_str($_REQUEST['settings'], $settings);
                if($action=='saveSettings')
                {
                    $designZone = new wcmDesignZone($bizClass, $bizId, $_REQUEST['zoneName']);
                    $designZone->refresh();
                    $designZone->setWidget($widgetClass, $guid, $settings);
                    $designZone->save();
					echo '<response type="javascript">debug.show("The settings of your box are now Save !");</response>';
                }

                $widget = new $widgetClass(0, $settings, $bizobject, $guid);
                echo '<response type="item" id="'.$widgetClass.'-'.$guid.'-context"><![CDATA['.$widget->edit().'<script type="text/javascript">portal.getWidget("'.$widgetClass.'-'.$guid.'").initCtl();</script>]]></response>';
                break;

        case 'cancelSettings':
                $designZone = new wcmDesignZone($bizClass, $bizId, $_REQUEST['zoneName']);
                $designZone->refresh();
                $content = $designZone->getZoneContent();

                if(isset($content[$guid]))
                        $settings = $content[$guid]['settings'];
                else    
                        $settings = array();

                $widget = new $widgetClass(wcmWidget::VIEW_SETTINGS, $settings, $bizobject, $guid);
//              echo '<response type="item" id="'.$widgetClass.'-'.$guid.'-settings">toto</reponse>';
                echo '<response type="item" id="'.$widgetClass.'-'.$guid.'-settings">'.$widget->displaySettings().'</response>';
                echo '<response type="item" id="'.$widgetClass.'-'.$guid.'-context">'.$widget->edit().'</response>';
                break;

		case 'refreshData':
        case 'saveData':
                // On recup la design Zone pour les settings
                $designZone = new wcmDesignZone($bizClass, $bizId, $_REQUEST['zoneName']);
                $content = $designZone->refresh();

                // On recup les settings de l'object
                if(isset($content[$guid]) && isset($content[$guid]['settings'])) 
                        $settings = $content[$guid]['settings'];
                else 
                        $settings = array();

                $widget = new $widgetClass(wcmWidget::VIEW_CONTENT, $settings, $bizobject, $guid);

				if($action == 'saveData')
				{
					parse_str($_REQUEST['context'], $context);
	                $widget->saveData($context);
					echo '<response type="javascript">debug.show("The content of your box is now Save !");</response>';
				}
				else
						echo '<response type="item" id="'.$widgetClass.'-'.$guid.'-context"><![CDATA['.$widget->edit().'<script type="text/javascript">portal.getWidget("'.$widgetClass.'-'.$guid.'").initCtl();</script>]]></response>';

                break;

}               
echo '</ajax-response>'. PHP_EOL;

