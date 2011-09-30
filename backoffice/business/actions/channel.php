<?php
/**
 * Project:     WCM
 * File:        channel.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the channel
 */
class channelAction extends wcmMVC_BizAction
{
    /**
     * Save last edited design zones
     */
    private function saveZones()
    {
        // Retrieve new design zones?
        $zones = getArrayParameter($_REQUEST, '_zones', null);
        $this->context->updateZones($zones);
        
        //get the widget settings
        $widgetsettings = array();
        foreach($_REQUEST as $key => $value)
        {
        	if(ereg("-settings",$key))
        	{
        		$widgetsettings[$key] = $value;
        	}
        }
        $this->context->updateSerialStorage('_widgetsettings', $widgetsettings);
        
    }

    /**
     * Save query from content tab
     */
    private function saveQuery()
    {

        //if the content tab was loaded
        if(isset($_REQUEST['newquery']) && isset($_REQUEST['orderBy']) && isset($_REQUEST['limit']))
        {
        	if($_REQUEST['id'] == 0 && $_REQUEST['newquery'] == "")
        	{
        	    $config = wcmConfig::getInstance();
        		$_REQUEST['newquery'] = $config['wcm.channel.query'];
        		$search_query = array("query" => $_REQUEST['newquery'], "orderBy" => $_REQUEST['orderBy'], "limit" => $_REQUEST['limit']);
            	$_REQUEST['request'] = serialize($search_query);
        	}
        	else
        	{
            	$search_query = array("query" => $_REQUEST['newquery'], "orderBy" => $_REQUEST['orderBy'], "limit" => $_REQUEST['limit']);
            	$_REQUEST['request'] = serialize($search_query);
        	}
        } 
        /*else
        {
        	$config = wcmConfig::getInstance();
        	$_REQUEST['request'] = serialize(array("query" => $config['wcm.channel.query']));
        	echo "here ".$_REQUEST['request'];
        }*/
    }
    
    /**
     * is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        $this->saveZones();
        $this->saveQuery();
    }
}
