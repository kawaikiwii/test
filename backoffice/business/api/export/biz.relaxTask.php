<?php 
/**
 * Project:     WCM
 * File:        biz.relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

class relaxTask extends bizobject {
    public $name;
    public $type;
    public $query;
    public $planning;
    public $nextExecutionDate;
    public $lastExecutionDate;
    public $loginAs;
    public $exportRulesIds;
    public $processId;
    public $enable;
    public $companyName;
    public $serializedForm = array();
    public $perimeter;
    
    public $sort;
    public $parameters;
    public $limit;
    
    public $sendReport;
    public $sendReportTo;
    
    private $_exportRules = null;
    private $_logger;
    private $_logFile;
    private $error = false;

    public function refresh($id) {
        parent::refresh($id);
        $this->loadExportRules();
    }


    public function loadExportRules() {
        $this->_exportRules = array();
        $exportRulesIds_array = explode('|', $this->exportRulesIds);
        if ((is_array($exportRulesIds_array)) && (count($exportRulesIds_array) > 0)) {
            foreach ($exportRulesIds_array as $erId) {
                $exportRule = new exportRule();
                $exportRule->refresh($erId);
                if ($exportRule->id) {
                    $this->_exportRules[] = clone ($exportRule);
                    
                }
            }
        }
        
    }
    
    /*    public function unloadExportRules()
     {
     $exportRulesIds_array = array();
     if (count($this->_exportRules)>0)
     {
     foreach ($this->_exportRules as $er)
     {
     $exportRulesIds_array[] = $er->id;
     }
     }
     $this->exportRulesIds = serialize($exportRulesIds_array);
     }
     */

    static function updateRelaxTask($id, $datas) {
        if ((count($datas) > 0)) {
            $sql = 'UPDATE #__relaxTask SET '.implode('=?,', array_keys($datas)).'=? WHERE id=?';
            $params = array_values($datas);
            $params[] = $id;
            
            $project = wcmProject::getInstance();
            $connector = $project->datalayer->getConnectorByReference("biz");
            $businessDB = $connector->getBusinessDatabase();
            $businessDB->executeQuery($sql, $params);
        }
    }

    static function getTypes() {
        return array('standard'=>'Standard', 'alerte'=>'Alerte');
    }

    static function getDefinedPlannings() {
    	return array('*#*#*#*#*'=>_PM_REAL_TIME, '0#8#*#*#*'=>_PM_ONCE_DAY, '0#8,20#*#*#*'=>_PM_TWICE_DAILY);
    }

    public function makeQueryFromSerializedForm() {
        //$query = 'publicationDate:[__LAST__ TO __NEXT__]';
        $query = 'publicationDate:[__LAST_PUSH__ TO __NEXT__]';
        // get perimeter infos
        $tabPerimeter = array();
        if (!is_array($this->perimeter))
        	$tabPerimeter = unserialize($this->perimeter);
        else
        	$tabPerimeter = $this->perimeter;	
        
        if (!empty($tabPerimeter))
        {
        	$channel = "channelId";
        	if (isset($this->serializedForm['pm_cat2']) && $this->serializedForm['pm_cat2'] == "P+S")
        		$channel = "channelIds";
        	$j=0;
        	foreach ($tabPerimeter as $univers=>$services)
        	{
        		// if severall universes separate them in the query
        		if ($j==0)
        			$query .= ' AND ';
        		/*else 
        			$query .= ') OR (';*/
        		
        		// add universe
        		$query .= 'siteId:'.$univers;
        		
        		foreach ($services as $service=>$channels)
        		{ 
        			// add service
        			$query .= ' AND classname:'.$service;
        			if (!empty($channels)) $query .= ' AND '.$channel.':(';
        			$k=0;
        			// add channels
        			foreach ($channels as $channel)
        			{
        				if ($k > 0)
        					$query .= ', ';
        				$query .= $channel;
        				$k++;
        				$cChannel = channel::getArrayChannelChilds($channel);
        				if (!empty($cChannel)) {
        					foreach ($cChannel as $key=>$subChannel) {
        						if ($subChannel["workflowState"] == "published") {
        							$query .= ', '.$subChannel["id"];
        							$k++;
        						}
        					}
        				}
        			}
        			if (!empty($channels)) $query .= ')';
        		}
        	$j++;	
        	}
        }
        
        // proceed with content inclusion (separate by ",") and update query
        if (isset($this->serializedForm['content']) && ($this->serializedForm['content'] != ''))
        {
        	$this->serializedForm['content'] = trim($this->serializedForm['content']);
        	$this->serializedForm['content'] = str_replace('"',"",$this->serializedForm['content']);
        	$this->serializedForm['content'] = preg_replace("#,+#",",",$this->serializedForm['content']);
        	$this->serializedForm['content'] = str_replace(",",", ",$this->serializedForm['content']);
        	$this->serializedForm['content'] = preg_replace("# +#"," ",$this->serializedForm['content']);
        	$this->serializedForm['content'] = str_replace(" ,",",",$this->serializedForm['content']);
        	$this->serializedForm['content'] = preg_replace("#,+#",",",$this->serializedForm['content']);
        	$tabContent = explode(",", $this->serializedForm['content']);
        	if (sizeof($tabContent) > 1 )
        	{
        		$i=0;
        		$query .= ' AND fulltext:(';
        		foreach($tabContent as $val)
        		{
        			if ($i > 0)
        				$query .= ' OR ';
        			$val = trim($val);
        			$val = str_replace(' ','" AND "',$val);
        			$query .= '"'.$val.'"';
        			$i++;
        		}
        		$query .= ')';
        	}
        	else
            	$query .= ' AND fulltext:("'.str_replace(' ','" AND "',$this->serializedForm['content']).'")';
        }

        // proceed with content exclusion (separate by ",") and update query
        if (isset($this->serializedForm['exclude']) && ($this->serializedForm['exclude'] != ''))
        {
        	$this->serializedForm['exclude'] = trim($this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = str_replace('"',"",$this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = preg_replace("#,+#",",",$this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = str_replace(",",", ",$this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = preg_replace("# +#"," ",$this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = str_replace(" ,",",",$this->serializedForm['exclude']);
        	$this->serializedForm['exclude'] = preg_replace("#,+#",",",$this->serializedForm['exclude']);
        	$tabExclude = explode(",", $this->serializedForm['exclude']);
        	if (sizeof($tabExclude) > 1 )
        	{
        		$i=0;
        		$query .= ' AND NOT fulltext:(';
        		foreach($tabExclude as $val)
        		{
        			if ($i > 0)
        				$query .= ' OR ';
        			$val = trim($val);
        			$val = str_replace(' ','" AND "',$val);
        			$query .= '"'.$val.'"';
        			$i++;
        		}
        		$query .= ')';
        	}
        	else
            	$query .= ' AND NOT fulltext:("'.str_replace(' ','" AND "',$this->serializedForm['exclude']).'")';
        }
            
        $query = $query.' AND workflowState:published AND listids:NOT(245,248,252)';
        
        $this->query = $query;
    }

    public function logMsg($msg, $type = 'message') {
        $method = 'log'.ucFirst($type);
        if (method_exists($this->_logger, $method))
            $this->_logger->$method($msg);
        else
            $this->_logger->logWarning("Unknown message type : '$type' :: $msg");
    }

    private function setLogFile() {
        $config = wcmConfig::getInstance();
        $this->_logFile = $config['wcm.logging.path'].'/tasks/'.strtolower(safeFileName($this->companyName)).'/'.safeFileName($this->id).'-'.strtolower(safeFileName($this->name)).'/'.date("Y-m-d").'.log';
    }

    public function getLogFile() {
        return $this->_logFile;
    }

    public function getlogPath() {
        $config = wcmConfig::getInstance();
        return $config['wcm.logging.path'].'/tasks/'.strtolower(safeFileName($this->companyName)).'/'.safeFileName($this->id).'-'.strtolower(safeFileName($this->name)).'/';
    }

    public function launch() {

		date_default_timezone_set('Europe/Paris');
		
    	if ($this->loginAs) {
            $account = new account();
            $account->refresh($this->loginAs);
            if ($account->expirationDate && $account->expirationDate < date('Y-m-d')) {
            	$this->enable = 0;
            	$this->save();
            	return false;
            }
        }
    
        $this->arrayParameters["taskName"] = safeFileName($this->name);
        $this->arrayParameters["taskId"] = safeFileName($this->id);
        $this->arrayParameters["companyName"] = safeFileName($this->companyName);
        
        if ($this->parameters) {
            $parameters = explode(",", $this->parameters);
            foreach ($parameters as $parameter) {
                $param = explode("=", $parameter);
                $this->arrayParameters[$param[0]] = $param[1];
            }
        }
        
        /* PRE-PROCESS  */
        if (!$this->id) {
            $this->setErrorMsg('Lancement impossible, objet relaxTask non instancié');
            return false;
        }
        $this->setLogFile();
        $this->_logger = new wcmLogger(true, true, $this->_logFile, false);
        
        //$this->logMsg(" ");
        $this->logMsg("#########################################################################");
        
        self::updateRelaxTask($this->id, array('processId'=>getmypid()));
        /* PROCESS */
        $this->process();
        
        /* POST-PROCESS */
        $datas = array('processId'=>0, 'nextExecutionDate'=>$this->nextIterationDate(), 'lastExecutionDate'=>$this->nextExecutionDate);
        self::updateRelaxTask($this->id, $datas);
        /* **/
        
        if ($this->error && $this->sendReport) {
        
            $this->sendReport();
            
        }
        
        return true;
    }

    private function sendReport() {
        require_once (WCM_DIR."/includes/mail/mail.php");
        
        if ($this->sendReportTo) {
            $fromName = "TASK REPORT";
            $fromMail = "task.report@afprelaxnews.com";
            $title = "TASK ERROR : $this->companyName - $this->name";
            $content = "";
            
            $reportMail = new htmlMimeMail();
            $reportMail->setHeader('X-Mailer', 'HTML Mime mail class');
            $reportMail->setHeader('Date', date('D, d M y H:i:s O'));
            $reportMail->setFrom('"'.$fromName.'" <'.$fromMail.'>');
            $reportMail->setSubject($title);
            $reportMail->setHtmlCharset("UTF-8");
            //Condition si erreur au niveau de la task
            if (count($this->_exportRules)== 1 || count($this->_exportRules)== 0){
                //if ($this->error) {

                    $logFile = $this->getLogfile();

                    $handle = fopen($logFile, "rb");

                    $content .= "Log : ".$logFile."<br/><br/>";

                    $logContent = fread($handle, filesize($logFile));

                    $content .= str_replace("\n", "<br/>", $logContent);

                    fclose($handle);
                //}
               
            }else{
                foreach ($this->_exportRules as $exportRule) {
                    if ($exportRule->getError()) {
                        //$logFile = $exportRule->getLogfile();
                        $logFile = $this->getLogfile();

                        $handle = fopen($logFile, "rb");

                        $content .= "Export : ".$exportRule->title."<br/>";
                        $content .= "Log : ".$logFile."<br/><br/>";

                        $logContent = fread($handle, filesize($logFile));

                        $content .= str_replace("\n", "<br/>", $logContent);
                        fclose($handle);
                        $content .= "<br/>---------------------------------------------------------------<br/>";
                    }

                }
                
                
            }
            
            $reportMail->setHtml($content);
            
            $reportMail->setSMTPParams(SMTPServer, '25', ServerName, SMTPAuth, SMTPUser, SMTPPassword);
            $reportMail->buildMessage();
            
            $sendReportTo = explode(",", $this->sendReportTo);
            $reportMail->send($sendReportTo, "smtp");
            unset($reportMail);
        }
        
    }

    private function process() {
        // mise à jour paramètres temporels contextuels de la requete
        $date = date('Y-m-d H:i:s');
        $LAST_7_DAYS = strtotime($date.' - 7 days');
        $LAST_MONTH = strtotime($date.' - 1 month');
        $LAST_YEAR = strtotime($date.' - 1 year');
        $NEXT_YEAR = strtotime($date.' + 1 year');
        $NEXT_3_MONTH = strtotime($date.' + 3 month');
        
        $search = array('__LAST_7_DAYS__', '__LAST_MONTH__', '__NOW__', '__NOW_UTC__', '__CURDATE__', '__LAST__', '__LAST_UTC__', '__NEXT__', '__LAST_PUSH__', '__LAST_YEAR__', '__NEXT_YEAR__', '__LAST_MONTH_SIMPLE__', '__NEXT_3_MONTH__');
        $replace = array(date('Y-m-d\TH:i:00', $LAST_7_DAYS), date('Y-m-d\TH:i:00', $LAST_MONTH), date("Y-m-d\TH:i:59",strtotime("now -1 minute")), gmdate("Y-m-d\TH:i:59",strtotime("now -1 minute")), date("Y-m-d"), str_replace(' ', 'T', date('Y-m-d\TH:i:00', strtotime($this->lastExecutionDate))), str_replace(' ', 'T', gmdate('Y-m-d\TH:i:00', strtotime($this->lastExecutionDate))), str_replace(' ', 'T', $this->nextExecutionDate), str_replace(' ', 'T', date('Y-m-d\TH:i:01', strtotime($this->lastExecutionDate))), date('Y-m-d', $LAST_YEAR), date('Y-m-d', $NEXT_YEAR), date('Y-m-d', $LAST_MONTH), date('Y-m-d', $NEXT_3_MONTH));
        
        $query = str_replace($search, $replace, $this->query);
        
        //$this->logMsg("start process task ".$this->name." (".$this->id.") :: ".date("Y-m-d\TH:i:s"));
        $this->logMsg("[t:".$this->id."] bizRelaxTask::process [start]");
        
        $this->arrayParameters['taskId'] = $this->id;
                
        // Complétion avec les droits du Account
        if ($this->loginAs) {
            $account = new account();
            $account->refresh($this->loginAs);
            //$this->logMsg("account Id :".$account->id);
        
            /* $permissions = $account->getLucenePermissions();
             $query = '('.$query.') AND ('.$permissions.')';*/
            
        }
        
        // Construction du resultSet
        $config = wcmConfig::getInstance();
        $search = wcmBizsearch::getInstance($config['wcm.search.engine']);
        $uid = 'relaxTask_'.$this->processId.'_'.uniqid();
        $this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [query:'.$query.']');
        
        $total = $search->initSearch($uid, $query, $this->sort);
        
        $this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [total (before limit) : '.$total.']');
        
        if ($total > 0) {
            if ($this->limit != "" && $total > $this->limit) {
                $total = $this->limit - 1;
            }
            $this->logMsg("[t:".$this->id."] bizRelaxTask::process [nb de résultats:".$total."]");
            
            $resultSet = $search->getDocumentRange(0, $total, $uid, false);
            $this->logMsg("[t:".$this->id."] bizRelaxTask::process [uid:".$uid."]");
            $dispResultSet = var_export($resultSet, true);
			$objectId = "";
            foreach($resultSet as $bizobject){
                $objectId .= $bizobject->id.', ';
            }
            $objectId = substr($objectId, 0, -2);
            
            $this->logMsg("[t:".$this->id."] bizRelaxTask::process [".$objectId."]");


            if (!is_array($this->_exportRules))
                $this->loadExportRules();
	    
	
                
            // Appel de chaque ExportRule avec le resultset
            if (count($this->_exportRules > 0)) {
                if ($this->type == 'alerte') {
                	$this->logMsg("--Process with Export alert--");
                	foreach ($this->_exportRules as $exportRule) 
                    {
                    	$this->logMsg("ExportRule id:".$exportRule->id);
                    	// MIS EN COMMENTAIRE POUR TERMINER L'EXECUTION DU SCRIPT !!!!
                        //$exportRule->clearDistributionChannels();
                        $this->logMsg("ExportRule : clearDistributionChannels");
                        $this->arrayParameters["accountId"] = $account->id;
                        
                        $this->logMsg("Sending documents");
                        
                        //manage email parameters
                        $dataForm = array();
                        
                        if (!empty($this->serializedForm))
                        {
                        	if (!is_array($this->serializedForm)) $dataForm = unserialize($this->serializedForm);
                        	else $dataForm = unserialize($this->serializedForm);                    	
                        }
                        
                        // total info
	                    if (isset($total))
							$this->arrayParameters['total'] = $total;	

						// add keywords selected	
						if (!empty($this->serializedForm))
						{
							$serializedData = unserialize($this->serializedForm);
							if (isset($serializedData['content']) && !empty($serializedData['content']))
								$this->arrayParameters['keywords']	= $serializedData['content'];
						}
							
						// add site Id - must be unique - get the last one by default
                    	if (!empty($this->perimeter))
						{
							$currentSite = "";
							$perimeterData = unserialize($this->perimeter);
							if (!empty($perimeterData))
							{
								foreach ($perimeterData as $key=>$val)
									$currentSite = $key;	
							}
							$this->arrayParameters['univers'] = $currentSite;
							wcmTrace("##PUSHMAIL### univers :".$this->arrayParameters['univers']);
						}
						else
							wcmTrace("##PUSHMAIL### missing universe");	
							
                        // init name sender if defined
	                    if (isset($dataForm['pushName']))
							$this->arrayParameters['pushName'] = $dataForm['pushName'];	
						// init email sender if defined
	                    if (isset($dataForm['pushEmail']))
							$this->arrayParameters['pushEmail'] = $dataForm['pushEmail'];	
						// init email subject if defined
	                    if (isset($dataForm['pushSubject']))
							$this->arrayParameters['pushSubject'] = $dataForm['pushSubject'];
						elseif($total == 1)
							$this->arrayParameters['pushSubject'] = utf8_decode($resultSet[0]->title);
						elseif($total > 1)
						{
							$this->arrayParameters['pushSubject'] = "Relax Selection : ";
							for($i=0;$i<3;$i++)
							{
								if(isset($resultSet[$i]))
								{
									if($i > 0)
										$this->arrayParameters['pushSubject'] .= " // ";
									$this->arrayParameters['pushSubject'] .= utf8_decode($resultSet[$i]->title);
								}
							}
						}
		
                        $error = $exportRule->execute($resultSet, $this->arrayParameters);
                        if ($error && !$this->error) {
                            $this->error = true;
                            $this->logMsg("Export rule Error !","error");
                        }
                    }
                } else {
                    foreach ($this->_exportRules as $exportRule) {
                        $this->logMsg('[t:'.$this->id.'er:'.$exportRule->id.'] bizRelaxTask::process [Exporting via : '.$exportRule->title.' ('.$exportRule->name.'/'.$exportRule->code.')]');
                        $this->logMsg('[t:'.$this->id.'er:'.$exportRule->id.'] bizRelaxTask::process [ExportRule id : '.$exportRule->id.']');
                        //Surcharge du fichier de log d'export vers le fichier de logs de tasks
                        $exportRule->setLogger($this->_logger);
                        $error = $exportRule->execute($resultSet, $this->arrayParameters);
                        if ($error && !$this->error) {
                            $this->error = true;
                            $this->logMsg('[t:'.$this->id.'er:'.$exportRule->id.'] bizRelaxTask::process [Export rule KO]','error');
                        }
                    }
                }
                
                if($this->error)                    
                    $this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [task KO]','error');
                else
                    $this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [task OK]');

            }
        } else {
            $this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [No query results]');
                        
            if($this->error)                    
            	$this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [task KO]','error');
            else
            	$this->logMsg('[t:'.$this->id.'] bizRelaxTask::process [task OK]');
        }
    }
    
    /**************************************************************************************************/

    static function expressLaunch($id) {

		    date_default_timezone_set('Europe/Paris');
    
        self::updateRelaxTask($id, array('nextExecutionDate'=>date("Y-m-d H:i:s")));
    }

    static function enable($id) {

		    date_default_timezone_set('Europe/Paris');
    
        self::updateRelaxTask($id, array('enable'=>1));
    }

    static function disable($id) {

		    date_default_timezone_set('Europe/Paris');
    
        self::updateRelaxTask($id, array('enable'=>0));
    }

    static function stop($id) {

		    date_default_timezone_set('Europe/Paris');
    
        $relaxTask = new relaxTask();
        $relaxTask->refresh($id);
        
        exec('ps '.$relaxTask->processId, $processState);
        if (count($processState) >= 2)
            exec('kill -9 '.$relaxTask->processId, $result);
            
        self::updateRelaxTask($id, array('processId'=>0));
    }

    static function initialize($id) {

		    date_default_timezone_set('Europe/Paris');
    
        $relaxTask = new relaxTask();
        $relaxTask->refresh($id);
        $nextExecutionDate = $relaxTask->nextIterationDate();
        $datas = array('lastExecutionDate'=>date("Y-m-d H:i:s"), 'nextExecutionDate'=>$nextExecutionDate);
        self::updateRelaxTask($id, $datas);
    }

    static function getLaunchingTasks() {

		    date_default_timezone_set('Europe/Paris');

        $project = wcmProject::getInstance();
        $connector = $project->datalayer->getConnectorByReference("biz");
        $db = $connector->getBusinessDatabase();
        $query = 'SELECT id FROM #__relaxTask WHERE enable=? AND nextExecutionDate <= ? AND (processId=? OR processId IS NULL)';
        $params = array('1', date("Y-m-d H:i:s"), 0);
        $results = $db->executeQuery($query, $params);
        $ids = array();
        while ($results->next()) {
            $row = $results->getRow();
            $ids[] = $row['id'];
        }
        return $ids;
    }

    static function getCrashingTasks() {
		
		require_once (WCM_DIR."/includes/mail/mail.php");
		date_default_timezone_set('Europe/Paris');
		
		$fromName = "TASK REPORT";
        $fromMail = "task.report@afprelaxnews.com";
        $title = "Task error : Task(s) running for more than 1 hour";		
		
		
		$date = date('Y-m-d H:i:s');       
		$hour = strtotime($date.' - 1 hour');
		
		
		
        $project = wcmProject::getInstance();
        $connector = $project->datalayer->getConnectorByReference("biz");
        $db = $connector->getBusinessDatabase();
        $query = 'SELECT id, name FROM #__relaxTask WHERE enable=? AND nextExecutionDate <= ? AND (processId<>0 AND processId IS NOT NULL)';
        $params = array('1', date("Y-m-d H:i:s",$hour));
        $results = $db->executeQuery($query, $params);
        $ids = array();
        while ($results->next()) {
        	$row = $results->getRow();
        	
        	
        	
        	$reportMail = new htmlMimeMail();
        	$reportMail->setHeader('X-Mailer', 'HTML Mime mail class');
        	$reportMail->setHeader('Date', date('D, d M y H:i:s O'));
	        $reportMail->setFrom('"'.$fromName.'" <'.$fromMail.'>');
		//on définit le titre du mail
	        $title = "TASK ERROR : ".$row['id']."-".$row['name'];
	        $reportMail->setSubject($title);
	        $reportMail->setHtmlCharset("UTF-8");
        	$reportMail->setHtml("La tâche ".$row['id'].":".$row['name']." tourne depuis plus d'une heure !");
            
            $reportMail->setSMTPParams(SMTPServer, '25', ServerName, SMTPAuth, SMTPUser, SMTPPassword);
            $reportMail->buildMessage();
            
            $sendReportTo[0] = "devTeam@relaxnews.com";
            $reportMail->send($sendReportTo, "smtp");
            unset($reportMail);
        	$ids[] = $row['id'];           
        }        
        
        return $ids;
        
    }

    /**************************************************************************************************/


    public function nextIterationDate() {
        global $a;
        
        $aNow = date("Y");
        $mNow = date("m");
        $jNow = date("d");
        $hNow = date("H");
        $minNow = date("i") + 1;
        
        $a = $aNow;
        $m = $mNow - 1;
        
        $planningArray = array_combine(array('minute', 'hour', 'day', 'month', 'dayOfWeek'), explode('#', $this->planning));
        
        foreach ($planningArray as $key=>$value) {
            //    $planningArray[$key] = (int) $value;
            $planningArray[$key] = $value;
        }
        while (($m = $this->nextMonth($m, $planningArray)) != - 1) /* on parcourt tous les mois de l'interval demandé */ { /* jusqu'à trouver une réponse convenable */
            if ($m != $mNow || $a != $aNow) /*si ce n'est pas ce mois ci */ {
                if (($j = $this->nextDay($a, $m, 0, $planningArray)) == -1) /* le premier jour trouvé sera le bon. */ { /*  -1 si l'intersection entre jour de semaine */
                    continue; /* et jour du mois est nulle */
                } /* ...auquel cas on passe au mois suivant */ else { /* s'il y a un jour */
                    $h = $this->nextHour(-1, $planningArray); /* la première heure et la première minute conviendront*/
                    $min = $this->nextMinute(-1, $planningArray);
                    return date("Y-m-d H:i:s", mktime($h, $min, 0, $m, $j, $a));
                }
            } else { /* c'est ce mois ci */
                $j = $jNow - 1;
                while (($j = $this->nextDay($a, $m, $j, $planningArray)) != - 1) /* on cherche un jour à partir d'aujourd'hui compris */ {
                    if ($j > $jNow) /* si ce n'est pas aujourd'hui */ { /* on prend les premiers résultats */
                        $h = $this->nextHour(-1, $planningArray);
                        $min = $this->nextMinute(-1, $planningArray);
                        return date("Y-m-d H:i:s", mktime($h, $min, 0, $m, $j, $a));
                    }
                    if ($j == $jNow) /* même algo pour les heures et les minutes */ {
                        $h = $hNow - 1;
                        while (($h = $this->nextHour($h, $planningArray)) != - 1) {
                            if ($h > $hNow) {
                                $min = $this->nextMinute(-1, $planningArray);
                                return date("Y-m-d H:i:s", mktime($h, $min, 0, $m, $j, $a));
                            }
                            if ($h == $hNow) {
                                $min = $minNow - 1;
                                while (($min = $this->nextMinute($min, $planningArray)) != - 1) {
                                    if ($min >= $minNow) {
                                        return date("Y-m-d H:i:s", mktime($h, $min, 0, $m, $j, $a));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    private function parseFormat($min, $max, $interval) {
        $retour = Array();
        
        if ($interval == '*') {
            for ($i = $min; $i <= $max; $i++)
                $retour[$i] = TRUE;
            return $retour;
        } else {
            for ($i = $min; $i <= $max; $i++)
                $retour[$i] = FALSE;
        }
        
        $interval = explode(',', $interval);
        foreach ($interval as $val) {
            $val = explode('-', $val);
            if (isset($val[0]) && isset($val[1])) {
                if ($val[0] <= $val[1]) {
                    for ($i = $val[0]; $i <= $val[1]; $i++)
                        $retour[$i] = TRUE; /* ex : 9-12 = 9, 10, 11, 12 */
                } else {
                    for ($i = $val[0]; $i <= $max; $i++)
                        $retour[$i] = TRUE; /* ex : 10-4 = 10, 11, 12... */
                    for ($i = $min; $i <= $val[1]; $i++)
                        $retour[$i] = TRUE; /* ...et 1, 2, 3, 4 */
                }
            } else {
                $retour[$val[0]] = TRUE;
            }
        }
        
        return $retour;
    }

    private function nextMonth($m, $planning) {
        global $a;
        $valeurs = $this->parseFormat(1, 12, $planning['month']);
        do {
            $m++;
            if ($m == 13) {
                $m = 1;
                $a++; /*si on a fait le tour, on rÈessaye l'annÈe suivante */
            }
        } while ($valeurs[$m] != TRUE);
        
        return $m;
    }

    private function nextDay($a, $m, $j, $planning) {
        $valeurs = $this->parseFormat(1, 31, $planning['day']);
        $valeurSemaine = $this->parseFormat(0, 6, $planning['dayOfWeek']);
        do {
            $j++;
            /* si $j est Ègal au nombre de jours du mois + 1 */
            if ($j == date('t', mktime(0, 0, 0, $m, 1, $a)) + 1)
                return - 1;
            $js = date('w', mktime(0, 0, 0, $m, $j, $a));
        } while ($valeurs[$j] != TRUE || $valeurSemaine[$js] != TRUE);
        
        return $j;
    }

    private function nextHour($h, $planning) {
        $valeurs = $this->parseFormat(0, 23, $planning['hour']);
        do {
            $h++;
            if ($h == 24)
                return - 1;
        } while ($valeurs[$h] != TRUE);
        
        return $h;
    }

    private function nextMinute($min, $planning) {
        $valeurs = $this->parseFormat(0, 59, $planning['minute']);
        do {
            $min++;
            if ($min == 60)
                return - 1;
        } while ($valeurs[$min] != TRUE);
        
        return $min;
    }

}
