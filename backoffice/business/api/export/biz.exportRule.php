<?php 
/**
 * Project:     WCM
 * File:        biz.exportRule.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

class exportRule extends bizobject {
    public $title;
    public $code; // Unique
    public $name; // Usually, the client's name
    public $unitTemplate;
    public $globalTemplate;
    public $zip;
    public $copyIllustrations;
    public $copyVideos;
    public $formats;
    public $videoFormats;
    public $confirmationFile;
    
    private $distributionChannels;
    private $workingPath;
    private $templatesPath;
    private $scriptFilename;
    private $parameters;
    private $fatalError = null;
    private $logger;
    private $logFile;
    
    private $session;
    private $config;
    private $error = false;
    private $zipArchive;
    
    private $report = array('verbose'=>0, 'message'=>0, 'warning'=>0, 'error'=>0);

    function getZipType() {
        return array("none"=>"None", "unit"=>"Unit", "global"=>"Global");
    }

    public function refreshByCode($name, $code) {
        $sql = 'SELECT id FROM '.$this->tableName.' WHERE name=? AND code=?';
        $id = $this->database->executeScalar($sql, array($name, $code));
        return $this->refresh($id);
    }

    public function setFatalError($errorMessage) {
        $this->fatalError = $errorMessage;
    }

    public function getFatalError() {
        return $this->fatalError;
    }

    public function setWorkingPath($workingPath) {
        $this->workingPath = $workingPath;
    }

    public function getWorkingPath() {
        return $this->workingPath;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function logMsg($msg, $type = 'message') {
    
        $method = 'log'.ucFirst($type);
        if (method_exists($this->logger, $method))
            $this->logger->$method($msg);
        else
            $this->logger->logWarning("Unknown message type : '$type' :: $msg");
            
        $this->report[$type]++;
        
        if ($type == "error") {
            $this->error = true;
        }
    }

    private function setLogFile() {
        if (class_exists($this->code, false) && method_exists($this->code, 'setLogFile')) {
            $this->logFile = call_user_func(array($this->code, 'setLogFile'), &$this);
        } else {
            $taskName = "t";
            if (isset($this->parameters["taskName"])) {
                $taskName = $this->parameters["taskName"];
            }


            $this->logFile = $this->config['wcm.webSite.repository']."logs/exports/".strtolower($this->name)."/".strtolower($this->code)."/".date("Y-m-d")."-".strtolower($taskName).'.log';
            
        }
    }

    public function getError() {
        return $this->error;
    }

    public function getLogfile() {
        return $this->logFile;
    }

    public function setLogger($logger) {
            $this->logger = $logger; 
    }

    public function getConfig() {
        return $this->config;
    }

    public function getSession() {
        return $this->session;
    }


    static function getExportRules($where = null) {
        $returnedArray = array();
        $currentExportRule = new exportRule();
        $currentExportRule->beginEnum($where);
        
        while ($currentExportRule->nextEnum()) {
            $returnedArray[] = clone ($currentExportRule);
        }
        
        $currentExportRule->endEnum();
        return $returnedArray;
    }

    static function getExportRulesByPermission($userId) {
        $returnedArray = array();
        $currentExportRule = new exportRule();
        
        $database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('news')->getConnector()->getBusinessDatabase();
        
        $exportRuleWithPermissions = array();
        $sql = "SELECT DISTINCT(exportRuleId) FROM biz_exportRulePermission";
        $rs = $database->executeQuery($sql);
        while ($rs->next()) {
            $exportRuleWithPermissions[] = $rs->get('exportRuleId');
        }
        
        $exportRulePermitted = array();
        $sql = "SELECT DISTINCT(exportRuleId) FROM biz_exportRulePermission WHERE userId=$userId AND enabled=1";
        $rs = $database->executeQuery($sql);
        while ($rs->next()) {
            $exportRulePermitted[] = $rs->get('exportRuleId');
        }
        
        if (count($exportRuleWithPermissions) > 0) {
            $sql = "id NOT IN (".implode(",", $exportRuleWithPermissions).") AND workflowstate='published'";
            $currentExportRule->beginEnum($sql);
            while ($currentExportRule->nextEnum()) {
                $returnedArray[] = clone ($currentExportRule);
            }
            $currentExportRule->endEnum();
        } else {
            $currentExportRule->beginEnum("workflowstate='published'");
            while ($currentExportRule->nextEnum()) {
                $returnedArray[] = clone ($currentExportRule);
            }
            $currentExportRule->endEnum();
        }
        
        if (count($exportRulePermitted) > 0) {
            $sql = "id IN (".implode(",", $exportRulePermitted).") AND workflowstate='published'";
            $currentExportRule->beginEnum($sql);
            while ($currentExportRule->nextEnum()) {
                $returnedArray[] = clone ($currentExportRule);
            }
            $currentExportRule->endEnum();
        }
        
        return $returnedArray;
    }


    protected function setDefaultValues() {
        parent::setDefaultValues();
    }

    public function refresh($id = null) {
        parent::refresh($id);
        
        if ($id) {
        
            $this->config = wcmConfig::getInstance();
            $this->session = wcmSession::getInstance();
            
            $this->distributionChannels = $this->getDistributionChannels();
            
            $this->templatesPath = $this->config['wcm.exports.path']."$this->name/$this->code/templates/";
            $this->scriptFilename = $this->config['wcm.exports.path']."$this->name/$this->code/script.php";
            
            if (file_exists($this->scriptFilename)) {
                require_once ($this->scriptFilename);
            } else {
                // $this->logMsg('Script file not found', 'warning');
            }
        }
        
    }

    public function getDistributionChannels($all = false) {
        $where = ($all) ? 'exportRuleId='.$this->id : 'exportRuleId='.$this->id.' AND active=1';
        $returnValue = array();
        if ($this->id) {
            $currentDistributionChannel = new distributionChannel();
            $currentDistributionChannel->beginEnum($where);
            while ($currentDistributionChannel->nextEnum()) {
                $returnValue[] = clone ($currentDistributionChannel);
            }
            $currentDistributionChannel->endEnum();
        }
        return $returnValue;
        
    }

    public function execute($documents, $parameters = null) {
        $this->parameters = $parameters;
        
        if (!$this->logger) {
            $this->setLogFile();
            $this->logger = new wcmLogger(true, true, $this->logFile, false);
        }
        
        $this->workingPath = $this->config['wcm.exports.tmpPath'].$this->name;
        
        if (!is_dir($this->workingPath))
            mkdir($this->workingPath, 0777, true);
            
	


        $this->workingPath .= "/".$this->code;
        if (!is_dir($this->workingPath))
            mkdir($this->workingPath, 0777, true);
            
        $this->workingPath .= '/'.date('Ymd_His')."_".uniqid();
        $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::execute [Creating working directory '.$this->workingPath.']');
        if (!mkdir($this->workingPath, 0777, true)) {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::execute [Creating working directory '.$this->workingPath.']', 'error');
        }
        
        $this->executeMethod('begin');
        
		if ($this->copyIllustrations) {
            foreach ($documents as $bizobject) {
                $this->executeMethod('getIllustrations', array('bizobject'=>$bizobject, 'className'=>$bizobject->getClass(), 'exportRule'=>$this));
            }
        }
	
    	if ($this->copyVideos) {
            foreach ($documents as $bizobject) {
                $this->executeMethod('getVideos', array('bizobject'=>$bizobject, 'className'=>$bizobject->getClass(), 'exportRule'=>$this));
            }
        }

        if ($this->unitTemplate) {
            foreach ($documents as $bizobject) {
                $this->executeMethod('unitTransform', array('document'=>$bizobject, 'bizobject'=>$bizobject->getAssocArray(false), 'className'=>$bizobject->getClass(), 'exportRule'=>$this));
            }
        } else {
            $this->logMsg('No unitTransform');
        }
        				
        $this->executeMethod('globalTransform', array('documents'=>$documents, 'exportRule'=>$this, 'parameters'=>$this->parameters));
        
        if ($this->zip == "global") {
            $this->executeMethod('createZip', array('exportRule'=>$this));
        }
        
        $this->executeMethod('push', $this->parameters);
        
        $this->executeMethod('clear');
        
        $this->executeMethod('end');
        
        return $this->error;
    }

    private function executeMethod($method, $arrayParameters = array()) {
        $returnedValue = null;
        if (class_exists($this->code, false)) {
            $preProcessValidation = true;
            
            $preProcess = 'before_'.$method;
            if (method_exists($this->code, $preProcess) && (!$this->fatalError)) {
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::executeMethod [Execute preProcess method '.$this->code.'::'.$preProcess.'()]');
                $preProcessValidation = call_user_func(array($this->code, $preProcess), &$arrayParameters, &$this, &$returnedValue);
            }
            if ($preProcessValidation) {
                if (method_exists('exportRule', $method) && (!$this->fatalError)) {
                    $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::executeMethod [Execute method exportRule->'.$method.'()]');
                    $returnedValue = $this->$method($arrayParameters);
                }
                
                $postProcess = 'after_'.$method;
                if (method_exists($this->code, $postProcess) && (!$this->fatalError)) {
                    $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::executeMethod [Execute postProcess method '.$this->code.'::'.$postProcess.'()]');
                    call_user_func(array($this->code, $postProcess), $arrayParameters, &$this, &$returnedValue);
                }
            } else {
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::executeMethod [PreProcess method '.$this->code.'::'.$preProcess.'() skip the method execution]', 'warning');
            }
        } else {
            if (method_exists('exportRule', $method) && (!$this->fatalError)) {
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::executeMethod [Execute method exportRule->'.$method.'()]');
                $returnedValue = $this->$method($arrayParameters);
            }
        }
        return $returnedValue;
    }

    public function createZip($arrayParameters) {
        if ($this->zip == "global") {
        
            $archiveName = $this->setZipFileName($arrayParameters);
            $file = "$archiveName.zip";
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::createZip [Creating global archive : '.$file.']');
            
            $this->zipArchive = new ZipArchive();
            if ($this->zipArchive->open($file, ZIPARCHIVE::CREATE) !== TRUE) {
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::createZip [Cannot open <'.$file.'>]', 'error');
                return false;
            }
            
            $filesArchived = $this->addFileToZip($this->workingPath);
            
            $this->zipArchive->close();
            
            foreach ($filesArchived as $filesArchive) {
                unlink($filesArchive);
            }
        }
    }

    public function addFileToZip($folder) {
        $dir = new DirectoryIterator($folder);
        $filesArchived = array();
        foreach ($dir as $file) {
            if (!$file->isDot()) {
                $filename = $file->getFilename();
                
                if ($file->isDir()) {
                    $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::addFileToZip [Creating directory in archive : '.$filename.']');
                    $this->zipArchive->addEmptyDir($filename);
                    $filesArchived[] = $this->addFileToZip($folder."/".$filename);
                    rmdir_r($filename);
                } else {
                    $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::addFileToZip [Adding file to archive : '.$filename.']');
                    if (!$this->zipArchive->addFile($folder."/".$filename, $filename)) {
                        $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::addFileToZip [Fail to add file to archive : '.$filename.']', 'error');
                    }
                    $filesArchived[] = $folder."/".$filename;
                }
            }
        }
        return $filesArchived;
    }

    public function getIllustrations($arrayParameters) {
        if ($this->formats) {
            $relateds = $arrayParameters['bizobject']->getRelateds();
            $formats = unserialize($this->formats);
            
            foreach ($relateds as $related) {
                if ($related["relation"]["destinationClass"] == "photo") {
                    foreach ($formats as $format) {
                        $filename = $related["object"]->getPhotoRelativePathByFormat($format);
                        if (file_exists($this->config['wcm.webSite.repository'].$filename)) {
                            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getIllustrations [Copying : '.$filename.']');
                            $remoteFilename = array_pop(explode('/', $filename));
                            
                            // gestion d'un repertoire distant                           
                            if (isset($this->parameters['distantDir']) && !empty($this->parameters['distantDir']))
                            {
                            	$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getIllustrations [distantDir : '.$this->parameters['distantDir'].']');
                            
                            	$remoteFilename = $this->parameters['distantDir']."/".$remoteFilename;	
                            	// si le rep n'existe pas on le créé
						    	if (!is_dir($this->workingPath."/".$this->parameters['distantDir']))
						    		mkdir($this->workingPath."/".$this->parameters['distantDir'], 0777, true);
                            }
                            	
                            if (!copy($this->config['wcm.webSite.repository'].$filename, $this->workingPath."/".$remoteFilename)) {
                                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getIllustrations [Fail to copy : '.$filename.']', 'error');
                            }
                        } else {
                            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getIllustrations [File not found : '.$filename.']', 'warning');
                            
                        }
                    }
                }
            }
            
        } else {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getIllustrations [No retrieving Illustrations]');
        }
        
    }

 	public function getVideos($arrayParameters) 
 	{
 		$object = $arrayParameters['bizobject'];
 		
 		// vérifie s'il s'agit bien d'un objet vidéo
 		if ($object->getClass() == "video" && !empty($this->videoFormats))
 		{
 			$formats = unserialize($object->formats);
 			foreach ($formats as $format) 
 			{			
	 			$exportFormats = unserialize($this->videoFormats);			
	 			// conversion du tableau de format d'export souhaité en valeurs mime pour comparaison
	 			$tabFormats = array();
	 			foreach ($exportFormats as $expf) 
 				{
 					// on supprime les parenthèse pour ne garder que la forme mime qui servira de comparaison
 					$val = strstr($expf, "(");
 					// debut ajout pour version php 5.2 ! -- si 5.3 strstr($expf, "(", true) suffit)
 					if (!empty($val)) 
 						$result = str_replace($val, "", $expf);
 					else
 						$result = $expf;
 					// fin ajout
					$tabFormats[] = $result; // = $val si php 5.3
				}
	 			 			
	 			// si le format de la vidéo est souhaité au niveau de l'export
	 			if (in_array($format["mime"], $tabFormats))
	 			{
		 			// récupère le nom du fichier distant
		 			$remoteFilename = array_pop(explode('/', $format["url"]));
		 			// gestion d'un repertoire distant                           
		            if (isset($this->parameters['distantDir']) && !empty($this->parameters['distantDir']))
		            {
		            	$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getVideos [distantDir : '.$this->parameters['distantDir'].']');
		                            
		                $remoteFilename = $this->parameters['distantDir']."/".$remoteFilename;	
		                // si le rep n'existe pas on le créé
						if (!is_dir($this->workingPath."/".$this->parameters['distantDir']))
							mkdir($this->workingPath."/".$this->parameters['distantDir'], 0777, true);
		            }
		                            	
		            // copie le fichier distant le rep workingPath
		 			if (!copy($format["url"], $this->workingPath."/".$remoteFilename)) 
		                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getVideos [Fail to copy : '.$format["url"].']', 'error');
		            else 
		                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::getVideos [File copy OK : '.$format["url"].']');	                            		
	 			}
 			}
 		}
 		   
    } 
   
    public function unitTransform($arrayParameters) {
        if ($this->unitTemplate) {
            if (file_exists($this->templatesPath.$this->unitTemplate)) {
                $gen = new wcmTemplateGenerator(null, false);
                
                $buffer = $gen->executeTemplate($this->templatesPath.$this->unitTemplate, $arrayParameters);
                $filename = $this->setUnitFileName($arrayParameters);
                $handle = fopen($filename, "a");
                fwrite($handle, $buffer);
                fclose($handle);
            } else {
            	$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::unitTransform [Unit template '.$this->templatesPath.$this->unitTemplate.' not found]','error');
                $this->error = true;
            }
        } else {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::unitTransform [No unitTransform]');
        }
    }

    public function setUnitFileName($arrayParameters) {
        if (class_exists($this->code, false) && method_exists($this->code, 'setUnitFileName'))
            return call_user_func(array($this->code, 'setUnitFileName'), &$arrayParameters, &$this);
        else
            return $this->workingPath.'/'.$arrayParameters['className'].'-'.$arrayParameters['bizobject']['id'].'.xml';
    }

    public function globalTransform($arrayParameters) {
		$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::globalTransform [globalTransform]');	
        $news = array();
        if ($this->globalTemplate) {
	    
            if (file_exists($this->templatesPath.$this->globalTemplate)) {
		
                $gen = new wcmTemplateGenerator(null, false);
				$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::globalTransform [Template '.$this->templatesPath.'/'.$this->globalTemplate.']');
                $buffer = $gen->executeTemplate($this->templatesPath.$this->globalTemplate, $arrayParameters);
		
                $filename = $this->setGlobalFileName($arrayParameters);
                         
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::globalTransform [GlolbalTransform::'.$filename.']');
                $handle = fopen($filename, "a");
		fwrite($handle, $buffer);
                fclose($handle);
		
            } else {
                $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::globalTransform [No File generated on globalTransform : template not found]', 'error');
                $this->error = true;
            }
        } else {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::globalTransform [No globalTransform]');
        }
    }

    public function setGlobalFileName($arrayParameters) {
	$this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::setGlobalFileName [setGlobalFileName]');
        if (class_exists($this->code, false) && method_exists($this->code, 'setGlobalFileName')){	        
	    return call_user_func(array($this->code, 'setGlobalFileName'), &$arrayParameters, &$this);
        }else{
            return $this->workingPath.'/index.xml';
	}
    }
    
    /*
     * Permet de nommer le Zip différemment du nom du fichier xml
     * Créée le 17/08/2010
     */
	 public function setZipFileName($arrayParameters) {
        if (class_exists($this->code, false) && method_exists($this->code, 'setZipFileName'))
            return call_user_func(array($this->code, 'setZipFileName'), &$arrayParameters, &$this);
        elseif (class_exists($this->code, false) && method_exists($this->code, 'setGlobalFileName'))
            return str_replace(".xml","",call_user_func(array($this->code, 'setGlobalFileName'), &$arrayParameters, &$this));
        else
            return $this->workingPath.'/index';
    }

    public function push() {
        foreach ($this->distributionChannels as $channel) {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'dc:'.$channel->id.'] bizExportRule::push [Push Distribution channel : '.$channel->code.'('.$channel->type.')]');
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'dc:'.$channel->id.'] bizExportRule::push [Push Distribution channel : '.$channel->id.']');
            $channel->push($this->workingPath, &$this);
            if (!$this->error && $channel->getError()) {
                $this->error = true;
            }
        }
    }

    public function clear() {
        $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::clear [Clear Working Path : '.$this->workingPath.']');
		rmdir_r($this->workingPath);
    }

    public function end() {
        if (!$this->fatalError) {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::end [Export successfully completed]');
        } else {
            $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::end [Export Aborted :: Fatal Error :: '.$this->fatalError.']', 'error');
            $this->error = true;
        }
    }

    public function clearDistributionChannels() {
        $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::clearDistributionChannels [Clear Distribution Channels]');
        $this->distributionChannels = NULL;
    }

    public function addDistributionChannel($distributionChannel) {
        $this->logMsg('[t:'.$this->parameters['taskId'].'er:'.$this->id.'] bizExportRule::addDistributionChannel [Distribution Channel : '.$distributionChannel->code.'('.$distributionChannel->type.')]');
        $this->distributionChannels[] = $distributionChannel;
    }

    public function add_FTP_DistributionChannel($host, $user, $pass, $remotePath_ftp) {
        $myDistributionChannel = new distributionChannel();
        $myDistributionChannel->connexionString = serialize(array('host'=>$host, 'user'=>$user, 'pass'=>$pass, 'remotePath_ftp'=>$remotePath_ftp));
        $myDistributionChannel->type = 'ftp';
        $myDistributionChannel->active = 1;
        $this->addDistributionChannel($myDistributionChannel);
    }

    public function add_FS_DistributionChannel($remotePath_fs) {
        $myDistributionChannel = new distributionChannel();
        $myDistributionChannel->connexionString = serialize(array('remotePath_fs'=>$remotePath_fs));
        $myDistributionChannel->type = 'fs';
        $myDistributionChannel->active = 1;
        $this->addDistributionChannel($myDistributionChannel);
    }

    public function add_EMAIL_DistributionChannel($fromName, $fromMail, $to, $title) {
    	$this->logMsg('add_EMAIL_DistributionChannel - fromName:'.$fromName.' / fromMail:'.$fromMail.' / to:'.$to.' / title:'.$title );
        $myDistributionChannel = new distributionChannel();
        $myDistributionChannel->connexionString = serialize(array('fromName'=>$fromName, 'fromMail'=>$fromMail, 'to'=>$to, 'title'=>$title));
        $myDistributionChannel->type = 'email';
        $myDistributionChannel->active = 1;
        $this->addDistributionChannel($myDistributionChannel);
    }

    static function getUsers($name) {
        $result = array();
        if ($name) {
            $where = "name LIKE '%".$name."%'";
        }
        $wcmuser = new wcmUser();
        $wcmuser->beginEnum($where, "name");
        $i = 0;
        while ($wcmuser->nextEnum()) {
            $result[$i]['id'] = $wcmuser->id;
            $result[$i]['name'] = $wcmuser->name;
            $i++;
        }
        $wcmuser->endEnum();
        return $result;
    }

    public function cleanPermissions() {
        $sql = 'DELETE FROM #__exportRulePermission WHERE exportRuleId=?';
        $params = array($this->id);
        $this->database->executeQuery($sql, $params);
    }

    public function setPermissions($permission = array()) {
        $permid = array();
        $perm = explode("|", $permission);
        if (is_array($perm) && sizeof($perm) > 1) {
            foreach ($perm as $value)
                $permid[] = $value;
        } else
            $permid[] = $perm[0];
            
        if (is_array($permid)) {
            $this->cleanPermissions();
            
            foreach ($permid as $user) {
                $sql = 'INSERT INTO #__exportRulePermission (exportRuleId,userId,enabled) VALUES (?,?,?)';
                $params = array($this->id, $user, 1);
                $this->database->executeQuery($sql, $params);
            }
        }
    }

    public function getPermissions() {
        $sql = 'SELECT * FROM #__exportRulePermission WHERE exportRuleId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsArray = array();
        $i = 0;
        
        if ($rs) {
            while ($rs->next()) {
                $permissionsArray[$i]['id'] = $rs->get('id');
                $permissionsArray[$i]['exportRuleId'] = $rs->get('exportRuleId');
                $permissionsArray[$i]['userId'] = $rs->get('userId');
                $permissionsArray[$i]['enabled'] = $rs->get('enabled');
                $i++;
            }
        }
        return $permissionsArray;
    }

    public function getPermissionsForGui() {
        $tab = array();
        $permissions = $this->getPermissions();
        if (sizeof($permissions) > 0) {
            for ($i = 0; $i < sizeof($permissions); $i++) {
                $user = new wcmUser();
                $user->refresh($permissions[$i]['userId']);
                
                if (isset($user->id))
                    $tab[] = $user->id;
            }
        }
        return $tab;
    }

    public function save($source = null) {
        $config = wcmConfig::getInstance();
        
        if (!parent::save($source))
            return false;
            
        if (isset($source['permissions']))
            $this->setPermissions($source['permissions']);
            
        @chmod($config['wcm.exports.path'], 0777);
	@mkdir($config['wcm.exports.path']."$this->name", true);
        @chmod($config['wcm.exports.path']."$this->name", 0775);
        @mkdir($config['wcm.exports.path']."$this->name/$this->code", true);
        @chmod($config['wcm.exports.path']."$this->name/$this->code/", 0775);
        @mkdir($config['wcm.exports.path']."$this->name/$this->code/templates/", true);
        @chmod($config['wcm.exports.path']."$this->name/$this->code/templates/", 0775);
        
        exec("cd ".$this->config['wcm.exports.path'].strtolower($this->name)."/".strtolower($this->code)."/; ln -s ".$this->config['wcm.webSite.repository']."logs/exports/$this->name/$this->code/ logs");
        
        return true;
    }

    public function getTemplatesPath() {
        return $this->templatesPath;
    }
    
}
