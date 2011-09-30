<?php
/**
 * Project:     WCM
 * File:        publicationWorkflow.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
require_once(dirname(__FILE__) . '/base/wcmBaseWorkflowScript.php');

/**
 * This class is a basic publication workflow script that
 * works in conjunction with the standard 'publicationWorkflow'
 * provided in WCM 4 demo web site.
 */
class publicationWorkflow extends wcmBaseWorkflowScript
{
    /**
     * Mine object before creation
     *
     * @return bool FALSE to cancel creation
     */
    public function beforeCreate()
    {
    	$config = wcmConfig::getInstance();
       	
    	if (!empty($config['tme.enabled']))
        	$tmeData = wcmSemanticServer::getInstance()->mineObject($this->wcmObject);
        
        if(isset($tmeData) && !empty($tmeData))
        	$this->wcmObject->semanticData->merge($tmeData);

        wcmTrace('WF: SDT: ' . print_r($this->wcmObject->semanticData, true));

        return true;
    }

    /**
     * This callback is invoked AFTER the object has been created
     */
    public function onCreate()
    {
        $config = wcmConfig::getInstance();
       	if (!empty($config['tme.enabled']))
        	wcmSemanticServer::getInstance()->indexObject($this->wcmObject);
        else 
        	return true ;
    }
    
    /**
     * This callback is invoked AFTER the object has been updated
     */
    public function onUpdate()
    {
        $config = wcmConfig::getInstance();
        $tme = wcmSemanticServer::getInstance();
	$methods = array();
       	//if (!empty($config['tme.enabled'])){
		//on initialise les méthodes de scroll de text
		//NFinder pour Organizations,People,Places
		//NconceptExtractor pour concept		
		$methods[0] = "NFinder";
		$methods[1] = "NConceptExtractor";
		if(isset($_SESSION['wcm']['footprint']['context'])){
			$context = $_SESSION['wcm']['footprint']['context'];
			$tmeData = $tme->mineObject($context, $methods);
			//Si TME retourne un résultat, on le merge à l'existant
			if(isset($tmeData) && !empty($tmeData))
				$this->wcmObject->semanticData->merge($tmeData);
		}
        	wcmSemanticServer::getInstance()->indexObject($this->wcmObject);
        /*}else{ 
        	return true ;
	}*/
    }

    /**
     * This callback is invoked AFTER the object has been deleted
     */
    public function onDelete()
    {
        $config = wcmConfig::getInstance();
        
       	if (!empty($config['tme.enabled']))
        	wcmSemanticServer::getInstance()->deindexObject($this->wcmObject);
        else 
        	return true ;
    }

    /**
     * This callback is invoked BEFORE the object is published
     *
     * In this example, we automatically create a version before publishing
     * so the user will be able to 'rollback' to this state.
     *
     * @return bool FALSE to cancel transition
     */
    public function beforePublish()
    {
        return $this->wcmObject->archive(_BIZ_VERSION_CREATED_BEFORE_PUBLICATION);
    }
    
    /**
     * This callback is invoked AFTER the object has been published
     */
    public function onPublish()
    {
        wcmTrace('WF: ' . $this->wcmObject->getClass() . ' #' . $this->wcmObject->id . ' published');
    }
    
    /**
     * These callback is invoked AFTER the object has been "instant published"
     */
    public function ondraft_to_published()
    {
        $this->wcmObject->save();
    }
    
	public function onunapproved_to_published()
    {
        $this->wcmObject->save();
    }
    
	public function onsubmitted_to_published()
    {
        $this->wcmObject->save();
    }
    
	public function onrejected_to_published()
    {
        $this->wcmObject->save();
    }
    	
    
}
