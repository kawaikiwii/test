<?php 
/**
 * Project:     WCM
 * File:        biz.account.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

class account extends wcmObject {
    public $isManager;
    public $isChiefManager;
    //    public $isDemoAccount;
    public $managerId;
    public $wcmUserId;
    public $managerPathId;
    //    public $startingDate;
    public $expirationDate;
    public $companyName;
    public $fullText;
    public $profile;
    
    private $wcmUser = null;
    private $_permissions = null;
    private $_hasPermissions = null;

    protected function getDatabase() {
        if (!$this->database) {
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('news')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__account';
        }
    }

    public function refresh($id) {
        parent::refresh($id);
        //if ($this->id) $this->refreshWcmUser();
        $this->wcmUser = null;
        $this->_permissions = null;
        $this->_hasPermissions = null;
        return $this;
    }

    public function refreshWcmUser() {
        if ($this->wcmUserId) {
            $this->wcmUser = new wcmUser();
            $this->wcmUser->refresh($this->wcmUserId);
        }
    }


    public function refreshByUserName($userName) {
        $wcmUser = new wcmUser();
        $wcmUserId = $wcmUser->database->executeScalar("SELECT id FROM $wcmUser->tableName WHERE name=?", array($userName));
        if ($wcmUserId)
            return $this->refreshByWcmUser($wcmUserId);
        else
            return false;
    }

    public function refreshByWcmUser($userId) {
        $accountId = $this->database->executeScalar("SELECT id FROM $this->tableName WHERE wcmUserId=?", array($userId));
        if ($accountId)
            return $this->refresh($accountId);
        else
            return false;
    }

    static function getAccountsIds($wcmUserId, $type = 'childs') {
        $childsIds = array();
        $currentAccount = new account();
        $where = ($type == 'all') ? '' : 'managerId='.$wcmUserId;
        $currentAccount->beginEnum($where);
        while ($currentAccount->nextEnum()) {
            $currentAccount->refreshWcmUser();
            $childsIds[] = $currentAccount->id;
            if ($type == 'family')
                $childsIds = array_merge($childsIds, self::getAccountsIds($currentAccount->wcmUserId, $type));
        }
        $currentAccount->endEnum();
        return $childsIds;
    }

    static function getAccounts($wcmUserId, $type = 'childs', $fullText = '', $orderBy = '', $hideInactive = null) {
        if (!isset($hideInactive))
            $hideInactive = false;
            
        $childs = array();
        
        //manage search for family
        if ($type == 'all') 
        	$where = "";
        else if ($type == 'family' && !empty($fullText))
        {
        	// root managerId (1) special case
        	if ($wcmUserId == "1") 
        		$where = "(managerId=".$wcmUserId." OR managerPathId LIKE '".$wcmUserId."-%')";
        	else
        		$where = "(managerId=".$wcmUserId." OR managerPathId LIKE '%-".$wcmUserId."-%')";
        }
        else 
        	$where = "managerId=".$wcmUserId;    
        	  
        //$where = ($type == 'all') ? '' : 'managerId='.$wcmUserId;
        
        if ($fullText) {
            if ($where)
                $where .= " AND ";
            $where .= "MATCH (`fullText`) AGAINST ('".str_replace("'", "''", $fullText)."*' IN BOOLEAN MODE)";
        }
        
        if ($hideInactive == true)
            $where .= " AND ((expirationDate >= CURDATE()) OR (expirationDate  IS NULL))";

        wcmTrace("###".$type."###".$where);
        
        $currentAccount = new account();
        $currentAccount->beginEnum($where, $orderBy);
        while ($currentAccount->nextEnum()) 
        {
            $currentAccount->refreshWcmUser();
            $childs[] = clone ($currentAccount);
            // add test to fulltext for search family case
            if ($type == 'family' && empty($fullText))
            {
                $childs = array_merge($childs, self::getAccounts($currentAccount->wcmUserId, $type, $fullText, $orderBy, $hideInactive));
            }
        }
        
        $currentAccount->endEnum();
        return $childs;
    }

    public function nbChilds($type = 'childs') {
        return count(self::getAccountsIds($this->wcmUserId, $type));
    }

    static function transferManager($wcmUserId, $managerIdFrom, $managerIdTo) 
    {
        if ($managerIdTo != "" && $managerIdTo != "0" && $managerIdTo != 0) 
        {
            $currentAccount = new account();
            if ($wcmUserId != "" && $wcmUserId != "0" && $wcmUserId != 0) 
            {
                $currentAccount->refreshByWcmUser($wcmUserId);
                $currentAccount->managerId = $managerIdTo;
                $currentAccount->save();
                //$currentAccount->save(array('managerId'=>$managerIdTo));
            } 
            else if ($managerIdFrom != "" && $managerIdFrom != "0" && $managerIdFrom != 0) 
            {
                $where = 'managerId = '.$managerIdFrom;
                $currentAccount->beginEnum($where);
                while ($currentAccount->nextEnum()) 
                {          	
                    $currentAccount->managerId = $managerIdTo;
                	//$currentAccount->save(array('managerId'=>$managerIdTo));
                    $currentAccount->save();
                }
                $currentAccount->endEnum();
            }
        }
    }

    public function getSubscriptions($subscribedClass = NULL) {
        $where = "sysUserId='".$this->wcmUserId."'";
        if ($subscribedClass) {
            $where .= " AND subscribedClass='".$subscribedClass."'";
        }
        return bizobject::getBizobjects("subscription", $where);
    }

    public function getManagerName() {
        $manager = new wcmUser();
        if ($manager->refresh($this->managerId))
            return getConst($manager->name);
        else
            return '';
    }

    public function getManagerAccount() {
        $manager = new account();
        if ($manager->refreshByWcmUser($this->managerId))
            return $manager;
        else
            return false;
    }

    public function isChiefManager() {
        return ($this->profile == '_ACCOUNT_PROFILE_SUPERVISOR');
    }

    public function isManager() {
        return ($this->profile == '_ACCOUNT_PROFILE_MANAGER');
    }

    public function isDemoAccount() {
        return ($this->profile == '_ACCOUNT_PROFILE_DEMO');
    }

    public function __get($name) {
        switch ($name) {
            case "wcmUser_name":
                if ($this->wcmUser)
                    return getConst($this->wcmUser->name);
                elseif ($this->wcmUserId) {
                    $this->refreshWcmUser();
                    if ($this->wcmUser->name)
                        return getConst($this->wcmUser->name);
                }
                break;
            case "email":
                if ($this->wcmUser)
                    return getConst($this->wcmUser->email);
                elseif ($this->wcmUserId) {
                    $this->refreshWcmUser();
                    if ($this->wcmUser->email)
                        return $this->wcmUser->email;
                }
                break;
            case "manager_name":
                return $this->getManagerName();
                break;
            case "wcmUser_login":
                if ($this->wcmUser)
                    return getConst($this->wcmUser->login);
                elseif ($this->wcmUserId) {
                    $this->refreshWcmUser();
                    if ($this->wcmUser->login)
                        return getConst($this->wcmUser->login);
                }
                break;
            case "permissions":
                if (!is_array($this->_permissions))
                    $this->getPermissions();
                return $this->_permissions;
                break;
            case "hasPermissions":
                if ($this->_hasPermissions === null)
                    $this->countPermissions();
                return $this->_hasPermissions;
                break;
        }
        return null;
    }

    public function save($source = null) 
    {
    	if (!$this->wcmUser) {
            $this->wcmUser = new wcmUser();
            $this->wcmUser->refresh($this->wcmUserId);
        }
        $this->fullText = $this->companyName.' '.$this->wcmUser->name.' '.$this->wcmUser->login.' '.$this->wcmUser->email;
        
        $this->managerPathId = self::getAccountAncestors();
        
        if (!parent::save($source))
            return false;
        $this->setPermissions();     
        
        return true;
    }
	/*
	public function saveAll($where = '', $orderBy = 'id', $source = null) {
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        $i = 0;
        while ($enum->nextEnum()) {
            echo $className." : ".$enum->id."\n";
            if ($enum->save($source))
                $i++;
            else
                echo $className." : ".$enum->id." - error !\n";
        }
        
        echo $className." : ".$i." saved\n";
        
        $enum->endEnum();
        sleep(1);
    }
    */
    public function delete() {
        $this->cleanPermissions();
        if (!$this->wcmUser) {
            $this->wcmUser = new wcmUser();
            $this->wcmUser->refresh($this->wcmUserId);
        }
        $this->wcmUser->delete();
        return parent::delete();
    }

    /**OLD VERSION***/
    public function obsolete_getPermissions() {
        $sql = 'SELECT * FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsArray = array();
        while ($rs->next()) {
            $univers = $rs->get('univers');
            if (!isset($permissionsArray[$univers]))
                $permissionsArray[$univers] = array();
            $service = $rs->get('service');
            if (!isset($permissionsArray[$univers][$service]))
                $permissionsArray[$univers][$service] = array();
            $rubrique = $rs->get('rubrique');
            if (!in_array($rubrique, $permissionsArray[$univers][$service]))
                $permissionsArray[$univers][$service][] = $rubrique;
        }
        
        $this->_permissions = $permissionsArray;
		/* ***** */
        return ($permissionsArray);      
    }
    
    public function getNbRubriquesByPilier($pilier) {
    	$sql = "SELECT count(id) as nbRubriques FROM #__channel WHERE parentId=".$pilier." AND workflowState='published'";
    	$params = array($this->id);
    	$rs = $this->database->executeQuery($sql, $params);
    	if ($rs->next())
    		return $rs->get('nbRubriques');
    	else
    		return 0;
    }

    public function getPermissions($children = "") {
        $sql = 'SELECT * FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsArray = array();
        while ($rs->next()) {
            $univers = $rs->get('univers');
            if (!isset($permissionsArray[$univers]))
                $permissionsArray[$univers] = array();
            $service = $rs->get('service');
            if (!isset($permissionsArray[$univers][$service]))
                $permissionsArray[$univers][$service] = array();
            $rubrique = $rs->get('rubrique');
            if (!in_array($rubrique, $permissionsArray[$univers][$service])) 
                $permissionsArray[$univers][$service][] = $rubrique;
        }

        $insere_notice = false; //pour les éphémérides
        $notice = array();
        $sql_notice = 'SELECT * FROM #__channel WHERE parentId=195';
        $rs_notice = $this->database->executeQuery($sql_notice, $params);
        while ($rs_notice->next()) {
        	$rubrique_notice = $rs_notice->get('id');
        	$notice[] = $rubrique_notice;
        }
        if($children != "") {
            foreach($permissionsArray as $univers => $univ) {
                foreach($univ as $services => $service) {
                    foreach($service as $rubrique) {
                        if(is_numeric($rubrique)) {
                            $sql2 = 'SELECT * FROM #__channel WHERE parentId='.$rubrique;
                            $rs2 = $this->database->executeQuery($sql2, $params);
                            while ($rs2->next()) {
                                $rubrique2 = $rs2->get('id');
                                if (!in_array($rubrique2, $permissionsArray[$univers][$services]))
                                    $permissionsArray[$univers][$services][] = $rubrique2;
                            }
                            if(!$insere_notice && $univers == 6 && $services == "news" && in_array($rubrique,$notice)) {
                            	$insere_notice = true;
                            	$permissionsArray[6]["notice"][] = 195;
                            }
                        }
                    }
                }
            }
        }
        
        $this->_permissions = $permissionsArray;
		/* ***** */
        return ($permissionsArray);
    }
    
    /*
     * Créée pour les univers transverses type BIPH (Olivier)
     */
    public function getPermissionsUniverse() {
    	$sql = 'SELECT DISTINCT univers FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsUniverseArray = array();
        while ($rs->next()) {
            $univers = $rs->get('univers');
            if (!in_array($univers,$permissionsUniverseArray))
                $permissionsUniverseArray[] = $univers;
        }
        
        return ($permissionsUniverseArray);
    }
    
    public function getArrayPermissions() {
        $sql = 'SELECT * FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsArray = array();
        while ($rs->next()) {
            $univers = $rs->get('univers');
            if (!isset($permissionsArray[$univers]))
                $permissionsArray[$univers] = array();
            $service = $rs->get('service');
            if (!isset($permissionsArray[$univers][$service]))
                $permissionsArray[$univers][$service] = array();
            $rubrique = $rs->get('rubrique');
            if (!in_array($rubrique, $permissionsArray[$univers][$service]))
                $permissionsArray[$univers][$service][] = $rubrique;
        }
        
        return $permissionsArray;
    }
    
 	public function getSimpleIdArrayPermissions()
 	{
        $sql = 'SELECT * FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        $rs = $this->database->executeQuery($sql, $params);
        
        $permissionsArray = array();
        while ($rs->next()) 
        	$permissionsArray[$rs->get('id')] = $rs->get('univers').",".$rs->get('service').",".$rs->get('rubrique');
        
        return $permissionsArray;
    }
    
    /**************************************************************************************************************/

    public function getLucenePermissions_Service($univers) {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        $queryArray = array();
        
        foreach ($this->_permissions[$univers] as $service=>$rubriques) {
            if ($service == '*') {
                $manager = $this->getManagerAccount();
                if ($manager)
					/* ***** */
                    return ""; //$manager->getLucenePermissions_Service($univers);
                else
                    return 'className:*';
            } else {
                $rubrique_query = $this->getLucenePermissions_Rubrique($univers, $service);
                if ($rubrique_query)
                    $rubrique_query = ' AND ('.$rubrique_query.')';
                $queryArray[] = 'className:'.$service.$rubrique_query;
            }
        }
        if (!$queryArray)
            return 'className:*';
        else
            return '('.implode(') OR (', $queryArray).')';
    }

    public function getLucenePermissions_Rubrique($univers, $service) {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        $queryArray = array();
        
        foreach ($this->_permissions[$univers][$service] as $rubrique) {
            if ($rubrique == '*') {
                $manager = $this->getManagerAccount();
                if ($manager)
                    return $manager->getLucenePermissions_Rubrique($univers, $service);
                else
                    return 'channelids:*';
            } else {
                $queryArray[] = 'channelids:'.$rubrique;
            }
        }
        if (!$queryArray)
            return 'channelids:*';
        else
            return '('.implode(') OR (', $queryArray).')';
    }

    public function getLucenePermissions() {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        $queryArray = array();
        foreach ($this->_permissions as $univers=>$services) {
        
            $service_query = $this->getLucenePermissions_Service($univers);
            if ($service_query)
                $service_query = ' AND ('.$service_query.')';
            $queryArray[] = 'siteId:'.$univers.$service_query;
            
        }
        if (!$queryArray)
            return 'siteId:-1';
        else
            return '('.implode(') OR (', $queryArray).')';
    }
    
    /************ Pour l'affichage de ce dont l'utilisateur a droit ************/
	public function getSearchLucenePermissions_Service($univers,$siteId) {
        if (!is_array($this->_permissions))
            $this->getPermissions("children");
        $queryArray = array();
        
        foreach ($this->_permissions[$univers] as $service=>$rubriques) {
            if ($service == '*') {
                $manager = $this->getManagerAccount();
                if ($manager)
					/* ***** */
                    return ""; //$manager->getLucenePermissions_Service($univers);
                else
                    return 'className:*';
            } else {
                $rubrique_query = $this->getSearchLucenePermissions_Rubrique($univers, $service,$siteId);
                if ($rubrique_query)
                    $rubrique_query = ' AND ('.$rubrique_query.')';
                $queryArray[] = 'className:'.$service.$rubrique_query;
            }
        }
        if (!$queryArray)
            return 'className:*';
        else
            return '('.implode(') OR (', $queryArray).')';
    }
    
	public function getSearchLucenePermissions_Rubrique($univers, $service,$siteId) {
        if (!is_array($this->_permissions))
            $this->getPermissions("children");
        $queryArray = array();
        
        $toutes_rubriques = true;
        $sql = "SELECT * FROM #__channel WHERE parentId IS NULL AND workflowState='published' AND siteId=?";
    	$params = array($siteId);
    	$rs = $this->database->executeQuery($sql, $params);
    	while ($rs->next()) {
    		if(!in_array($rs->get('id'),$this->_permissions[$univers][$service]))
    			$toutes_rubriques = false;
    	}
        
    	if(!$toutes_rubriques) {
	        foreach ($this->_permissions[$univers][$service] as $rubrique) {
	            if ($rubrique == '*') {
	                $manager = $this->getManagerAccount();
	                if ($manager)
	                    return $manager->getSearchLucenePermissions_Rubrique($univers, $service);
	                else
	                    return 'channelid:*';
	            } else {
	                $queryArray[] = $rubrique;
	            }
	        }
	        if (!$queryArray)
	            return 'channelid:*';
	        else
	            return 'channelid:('.implode(',', $queryArray).')';
    	}
    	else
    		return;
    }
    
	public function getSearchLucenePermissions($siteId) {
		if (!is_array($this->_permissions))
            $this->getPermissions("children");
		$queryArray = array();
		foreach ($this->_permissions as $univers=>$services) {
			if($univers == $siteId) {
				$service_query = $this->getSearchLucenePermissions_Service($univers,$siteId);
	            $queryArray[] = $service_query;
	            break;
			}
		}
        if (!$queryArray)
            return 'siteId:-1';
        else
        	return implode(') OR (', $queryArray);
    }
    /**************************************************************************************************************/

    public function countPermissions() {
        $sql = 'SELECT COUNT(*) FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        
        $this->_hasPermissions = $this->database->executeScalar($sql, $params);
    }

    public function initPermissions() {
        $this->_permissions = array();
        $this->_hasPermissions = 0;
    }

    public function cleanPermissions() {
        $sql = 'DELETE FROM #__accountPermission WHERE accountId=?';
        $params = array($this->id);
        $this->database->executeQuery($sql, $params);
    }
    
    public function addPermission($univers, $service, $rubrique) 
    {
        if (!isset($this->_permissions[$univers]))
            $this->_permissions[$univers] = array();
        if (!$service)
            $service = '*';
        if (!isset($this->_permissions[$univers][$service]))
            $this->_permissions[$univers][$service] = array();
        if (!$rubrique)
            $rubrique = '*';
        if (!in_array($rubrique, $this->_permissions[$univers][$service]))
            $this->_permissions[$univers][$service][] = $rubrique;
        $this->_hasPermissions++;       
    }

    public function setPermissions($perm = null) 
    {
    	if (!empty($this->_permissions) || (isset($perm) && empty($perm)))		
    		$this->cleanPermissions();
    	
        if (is_array($this->_permissions)) {
            foreach ($this->_permissions as $univers=>$services) {
                foreach ($services as $service=>$rubriques) {
                    foreach ($rubriques as $rubrique) {
                        $sql = 'INSERT INTO #__accountPermission (accountId,univers,service,rubrique) VALUES (?,?,?,?)';
                        $params = array($this->id, $univers, $service, $rubrique);
                        $this->database->executeQuery($sql, $params);
                    }
                }
            }
        }
    	
    }

	public function setPermissionsFromArray($permissionArray) 
    {
    	if (!empty($this->_permissions))		
    		$this->cleanPermissions();
    	
        if (is_array($permissionArray)) {
            foreach ($permissionArray as $univers=>$services) {
            	$sitePartnerFeed = new site();
    			$sitePartnerFeed->refresh($univers);
    			if(!$sitePartnerFeed->partnerFeeds) {
	                foreach ($services as $service=>$rubriques) {
	                    foreach ($rubriques as $rubrique) {
	                        $sql = 'INSERT INTO #__accountPermission (accountId,univers,service,rubrique) VALUES (?,?,?,?)';
	                        $params = array($this->id, $univers, $service, $rubrique);
	                        $this->database->executeQuery($sql, $params);
	                    }
	                }
    			}
                unset($sitePartnerFeed);
            }
        }   	
    }
    
    public function getUnivers() {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        return array_keys($this->_permissions);
    }

    public function getServices($univers) {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        if (!array_key_exists($univers, $this->_permissions))
            return array();
            
        if (array_key_exists('*', $this->_permissions[$univers])) 
        {
        	// IMPORTANT : le code original ne fonctionne pas si managerId != 1 la variable $userID n'existe pas
        	// cela nécessite des tests plus approfondis  	
            //if ($this->managerId == 1) {
                $universObj = new site();
                $universObj->refresh($univers);
                return explode('|', $universObj->services);
            /*} else {
                $tempAccount = new account();
                $tempAccount->refreshByWcmUser($userId);
                return $tempAccount->getServices($univers);
            }*/
        }
        
        return array_keys($this->_permissions[$univers]);
    }

    public function getRubriques($univers, $service) {
        if (!is_array($this->_permissions))
            $this->getPermissions();
        if (!array_key_exists($univers, $this->_permissions))
            return array();
        if (!array_key_exists($service, $this->_permissions[$univers]))
            return array();
		/* ***** */
        return array_values($this->_permissions[$univers][$service]);
    }

    public function isMyManagerAllowed($univers, $service = null, $rubrique = null) {
        // IMPORTANT : me code original ne fonctionne pas si managerId != 1 la variable $userID n'existe pas
        // cela nécessite des tests plus approfondis
    	//if ($this->managerId == 1)
            return true;
        /*$tempAccount = new account();
        $tempAccount->refreshByWcmUser($userId);
        return $tempAccount->isAllowedTo($univers, $service, $rubrique);*/
    }

    public function isAllowedTo($univers, $service = null, $rubrique = null) {
        if (!is_array($this->_permissions))
            $this->getPermissions();
            
        if (!array_key_exists($univers, $this->_permissions))
            return false;
            
        if ($service === null)
            return true;
            
        if (!array_key_exists($service, $this->_permissions[$univers])) {
            if (array_key_exists('*', $this->_permissions[$univers]))
                return $this->isMyManagerAllowed($univers, $service, $rubrique);
            return false;
        }
        
        if ($rubrique === null)
            return true;
            
        if (in_array('*', $this->_permissions[$univers][$service]))
            return $this->isMyManagerAllowed($univers, $service, $rubrique);
            
        foreach ($this->_permissions[$univers][$service] as $p_rubrique) {
            $channelsArray = explode('-', $p_rubrique);
            if (in_array($rubrique, $channelsArray))
                return true;
        }
        
        return false;
    }

    static function getChannelsCheckBoxSelection($siteId = null, $service = null, $permissions = null, $checkCheckboxes = null) {
        $perm = array();
        if (isset($permissions[$siteId]) && isset($permissions[$siteId][$service]))
            $perm = $permissions[$siteId][$service];
            
        $channels = wcmCache::fetch('ArraySiteChannels-'.$siteId);
        if ( empty($channels) || !is_array($channels)) {
            wcmCache::store('ArraySiteChannels-'.$siteId, channel::getChannelHierarchyConstBySite($siteId), 3600);
            $channels = wcmCache::fetch('ArraySiteChannels-'.$siteId);
        }
        
        $rootChannels = array(0=>NULL);
        
        $html = '<table class="channels_choice" cellpadding="1" cellspacing="1"><tr>';
        
        foreach ($channels as $id=>$path) 
        {
            $parts = explode(' :: ', $path);
            $deep = sizeof($parts);
            
            $decalage = sizeof($parts) - 1;
            
            $rank_letter = strtolower(chr(64 + min(5, count($parts))));
            $rank_letter_prev = $rank_letter;
            $currentLabelConst = array_pop($parts);
            
            // on n'affiche que les 2 premiers niveaux
            if ($rank_letter == 'a' || $rank_letter =='b')
            {
            
	            if ($rank_letter == 'a')
	                $html .= '</td><td class="channel_choice_block">';
	            else 
	                $html .= '<span class="channel_choice_item level_c" style="margin:1px; padding:1px;">';
	                
	            // on récupère les rubriques parentes et on prépare le javascript pour cocher les checbox correspondantes
	            $cChannel = channel::getArrayChannelChilds($id);
	            
	            $js = "";
	            $js2 = "";
	            
	            if (! empty($cChannel)) {
	                foreach ($cChannel as $key=>$channel) {
	                    if ($channel["workflowState"] == "published") {
	                        // on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
	                        $letter = ord($rank_letter) + $channel["depth"];
	                        $charLetter = chr($letter);
	                        $elementId = $key."_".$charLetter;
	                        if ($charLetter == 'a' || $charLetter == 'b')
	                        {
	                        	$js .= "document.getElementById('".$elementId."').checked=1;";
	                        	$js .= "document.getElementById('".$elementId."').disabled=true;";
	                        	$js2 .= "document.getElementById('".$elementId."').disabled=false;";
	                        }
	                        if (isset($channel["childs"])) {
	                            foreach ($channel["childs"] as $key2=>$channel2) {
	                                if ($channel2["workflowState"] == "published") {
	                                    // on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
	                                    $letter = ord($rank_letter) + $channel2["depth"];
	                                    $charLetter = chr($letter);
	                                    $elementId = $key2."_".$charLetter;
	                                    if ($charLetter == 'a' || $charLetter == 'b')
	                        			{
	                                    	$js .= "document.getElementById('".$elementId."').checked=1;";
	                                		$js .= "document.getElementById('".$elementId."').disabled=true;"; 
	                        				$js2 .= "document.getElementById('".$elementId."').disabled=false;";
	                        			}                     
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	            
	            // use for checkboxes control before submitting
	            if (isset($checkCheckboxes) && !empty($checkCheckboxes))
	            	$js .= "document.getElementById('".$checkCheckboxes."').value=1;";
	            	
	            $html .= '<input style="margin-left:'.$decalage.'0px;" type="checkbox"';
	            $html .= ' name="channelIds[]" id="'.$id.'_'.$rank_letter.'" value="'.$id.'"';
	            $html .= ' onClick="if(this.checked) {'.$js.'} else {'.$js2.'}"';
	            if (in_array($id, $perm))
	            {
	            	// on teste si la catégorie parente est cochée et si c'est le cas on grise la case fille
	            	$channelP = new channel();
	            	$channelP->refresh($id);
	            	$channelPid = $channelP->parentId;
	            	if (!empty($channelPid) && in_array($channelPid, $perm))
	                	$html .= ' checked disabled=\'true\'';
	                else
	            		$html .= ' checked ';
	            }
	            $html .= ' rel="none" />';
	            
	            if ($rank_letter != 'a')
	                $html .= '<span id="0'.$id.'_'.$rank_letter.'" >'.getConst($currentLabelConst)."</span></span>";
	            else
	                $html .= '<span id="0'.$id.'_'.$rank_letter.'" ><b>'.getConst($currentLabelConst)."</b></span></span>";
	    	}
        }
        
        $html .= '</tr></table>';
        return $html;
    }

    static function checkAllChannels($siteId = null, $service = null, $userPerm = null) 
    {
        $html = '';
        $channels = wcmCache::fetch('ArraySiteChannels-'.$siteId);
        if ( empty($channels) || !is_array($channels)) 
        {
            wcmCache::store('ArraySiteChannels-'.$siteId, channel::getChannelHierarchyConstBySite($siteId), 3600);
            $channels = wcmCache::fetch('ArraySiteChannels-'.$siteId);
        }
        
        $rootChannels = array(0=>NULL);
        
        $js = "";
        $js2 = "";
        $js3 = "";
        $js4 = "";
        foreach ($channels as $id=>$path) 
        {
            $parts = explode(' :: ', $path);
            $decalage = sizeof($parts) - 1;
            
            $rank_letter = strtolower(chr(64 + min(5, count($parts))));
            $rank_letter_prev = $rank_letter;
            $currentLabelConst = array_pop($parts);
            
            $letter = ord($rank_letter);
            $charLetter = chr($letter);
            $elementId = $id."_".$charLetter;
            if ($charLetter == 'a' || $charLetter == 'b')
	        {
	            $js .= "document.getElementById('".$elementId."').checked=1;";
	            $js2 .= "document.getElementById('".$elementId."').checked=0;";
	            $js3 .= "document.getElementById('".$elementId."').disabled=true;document.getElementById('".$elementId."').checked=1;";
	            $js4 .= "document.getElementById('".$elementId."').disabled=false;";
	        }
            
            // on récupère les rubriques parentes et on prépare le javascript pour cocher les checbox correspondantes
            $cChannel = channel::getArrayChannelChilds($id);
            
            if (! empty($cChannel)) 
            {
                foreach ($cChannel as $key=>$channel) 
                {
                    if ($channel["workflowState"] == "published") 
                    {
                        // on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
                        $letter = ord($rank_letter) + $channel["depth"];
                        $charLetter = chr($letter);
                        $elementId = $key."_".$charLetter;
                        if ($charLetter == 'a' || $charLetter == 'b')
	        			{				        				
		        			$js .= "document.getElementById('".$elementId."').checked=1;";
	                        $js2 .= "document.getElementById('".$elementId."').checked=0;";
	                        $js3 .= "document.getElementById('".$elementId."').disabled=true;document.getElementById('".$elementId."').checked=1;";
	            			$js4 .= "document.getElementById('".$elementId."').disabled=false;";
	        			}
            			if (isset($channel["childs"])) {
                            foreach ($channel["childs"] as $key2=>$channel2) 
                            {
                                if ($channel2["workflowState"] == "published") 
                                {
                                    // on détermine la nouvelle valeur de la rank_letter suivant la profondeur des parent channels
                                    $letter = ord($rank_letter) + $channel2["depth"];
                                    $charLetter = chr($letter);
                                    $elementId = $key2."_".$charLetter;
                                    if ($charLetter == 'a' || $charLetter == 'b')
	        						{
                                    	$js .= "document.getElementById('".$elementId."').checked=1;";
                                    	$js2 .= "document.getElementById('".$elementId."').checked=0;";
                                		$js3 .= "document.getElementById('".$elementId."').disabled=true;document.getElementById('".$elementId."').checked=1;";
            							$js4 .= "document.getElementById('".$elementId."').disabled=false;";
	        						}
            					}
                            }
                        }
                    }
                }
            }
        }
        
        $html .= '<br /><br /><span ><input type="checkbox" id="allChannelIds" name="allChannelIds" value="*" onClick="if(this.checked) {'.$js3.';document.getElementById(\'list\').style.display=\'none\';document.getElementById(\'checkuncheck\').style.display=\'none\';} else { '.$js4.';;document.getElementById(\'checkuncheck\').style.display=\'inline\';};" />';
		$html .= '<span>'._BIZ_ALL_CHANNELS.'</span></span>&nbsp;&nbsp;<br /><br />';
        //$html .= '<img src=\'/img/icons/full_star.gif\' border=\'0\' align=\'ABSMIDDLE\'><a href="#" onclick="'.$js3.'" style=\'font-family:Arial, Helvetica, sans-serif;font-size:10px;\'/>'._BIZ_CHECKALL.'</a>&nbsp;';
        $html .= '<div id="checkuncheck" style="visibility=inline"><img src=\'/img/icons/full_star.gif\' border=\'0\' align=\'ABSMIDDLE\'><a href="#" onclick="'.$js.'" style=\'font-family:Arial, Helvetica, sans-serif;font-size:10px;\'/>'._BIZ_CHECKALL.'</a>&nbsp;';
        $html .= '<img src=\'/img/icons/empty_star.gif\' border=\'0\' align=\'ABSMIDDLE\'><a href="#" onclick="'.$js2.'"style=\'font-family:Arial, Helvetica, sans-serif;font-size:10px;\'/>'._BIZ_UNCHECKALL.'</a></div>';
        
        if (isset($userPerm[$siteId][$service]) && in_array("*", $userPerm[$siteId][$service]))
        {
        	$html .= '<script>document.getElementById(\'allChannelIds\').checked=1;'.$js3.';document.getElementById(\'list\').style.display=\'none\';document.getElementById(\'checkuncheck\').style.display=\'none\'; </script>';
        }    
        
        return $html;
    }
    
    /**
     * Display Xml structure in order to fit with treeview management
     * $alert : define mode alert which si a specific mode without local permissions management 
     * $alert : define if used un alert context
     * $taskId : for alert moode, get perimeter permissions from taskId
     * $excludeSites : siteId to exclude in xml permissions (array of id)
     * 
     * */
	public function getXmlTreeStructure($alert = null, $taskId = null, $excludeSites = null) 
 	{
 		require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');

 		$permissions = "";
 		
 		// use for accounts created by admin (root) - for several services use '|' separator
 		$defaultServicesForAlerts = "news";
 		
 		$account = new account();
 		$account->refreshByWcmUser($this->managerId);
 		// get parent permissions
 		$permissions = $account->getArrayPermissions();
 		// if alert, remove permission management else get account permissions
 		if (isset($alert))
 		{
 			if (isset($taskId) && !empty($taskId))
 			{
 				$relaxtask = new relaxTask(null, $taskId);
 				if (isset($relaxtask->id))
 					$localPerm = unserialize($relaxtask->perimeter);
 				else
 					$localPerm = array();	
 			}
 			else
 				$localPerm = array();
 		}
 		else
 			$localPerm = $this->getArrayPermissions();
 			
 		if (!empty($permissions))
 		{	
			echo "<tree id='0'>\n";
			
			// display all universes
			foreach ($permissions as $univers=>$services)
			{
				// special case, remove defined universe ( siteId(s) in array )
				if ((!isset($excludeSites)) || (isset($excludeSites) && !in_array($univers, $excludeSites)))
				{
					$universObj = new site();
	                $universObj->refresh($univers);
	                
	                // init channel array 
					$channels = channel::getArrayChannelChilds(null,$univers);
					
					$checkUniverse = "";
					if (isset($localPerm[$univers]['*']) && isset($alert)) $checkUniverse = "checked='true'";
					
					echo "<item id='".$univers."' im0='globe.gif' im1='globe.gif' im2='globe.gif' text='".$universObj->title."' type='checkbox' ".$checkUniverse." open='1' partnerfeeds='";
					if($universObj->partnerFeeds)
						echo "1";
					else
						echo "0";
					echo "'>\n";
					// display all services
					$hiddenService = true;
					
					if (isset($alert))
					{
						// if alert mode keep only "news" service, except for root account which have all permissions (*)
						if (!array_key_exists("*", $services))
						{
							foreach ($services as $service=>$rubriques)
								if ($service != "news")	unset($services[$service]);
						}
					}
					else
					{
						// si univers RELAXFIL -> ne pas afficher le service Event
						if ($univers == 6 && isset($services['event']))
							unset($services['event']);
					}
					
					foreach ($services as $service=>$rubriques)
					{				
						if ($service == "*")
						{
							$hiddenService = false;
							
							// keep only news service in alert Mode
							if (isset($alert))
								$serv = explode('|', $defaultServicesForAlerts);
							else
								$serv = explode('|', $universObj->services);
								
							// si univers RELAXFIL pour le cas particulier * -> ne pas afficher le service Event
							$keyArray = array_search("event", $serv);
							if ($univers == 6 && !empty($keyArray))
								unset($serv[$keyArray]);
								
							foreach ($serv as $servi)
							{
								if (!empty($servi)) 
								{
									$checkService = "";
									if (isset($localPerm[$univers][$servi]) && in_array("*", $localPerm[$univers][$servi]) && isset($alert)) $checkService = "checked='true'";			
									$open = "";
									if (isset($alert)) $open = " open='1'";
									
									echo "<item id='".$univers."_".$servi."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".getServiceTrad($universObj->language, $servi)."' type='checkbox' ".$checkService.$open." partnerfeeds='";
									if($universObj->partnerFeeds)
										echo "1";
									else
										echo "0";
									echo "'>\n";
									// display channels
									$this->getChannelXmlTreeStructure($channels, $localPerm, $univers, $servi, $rubriques);							
									echo "</item>\n";
								}
							}
						}
						else
						{
							$checkService = "";
							if (isset($localPerm[$univers]["$service"]["*"]) && isset($alert)) $checkService = "checked='1'";
							$open = "";
							if (isset($alert)) $open = " open='1'";
									
							echo "<item id='".$univers."_".$service."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".getServiceTrad($universObj->language, $service)."' type='checkbox' ".$checkService.$open." partnerfeeds='";
							if($universObj->partnerFeeds)
								echo "1";
							else
								echo "0";
							echo "'>\n";					
							// display channels
							$this->getChannelXmlTreeStructure($channels, $localPerm, $univers, $service, $rubriques);													
							echo "</item>\n";
						}
					}		
				}
				//if ($hiddenService)
				//	echo "<item id='x' type='hidden' im0='blank.gif' im1='blank.gif' im2='blank.gif' text='' disabled='1'></item>\n";
				if ((!isset($excludeSites)) || (isset($excludeSites) && !in_array($univers, $excludeSites)))				
					echo "</item>\n";
			}
			echo "</tree>";
 		}
 		else
 			echo "<tree id='0'></tree>";			
    }
    
    /**
     * Display Channel Xml structure in order to fit with treeview management
     */
    public function getChannelXmlTreeStructure($channels, $localPerm, $univers, $service, $rubriques, $alert = null) 
 	{
 		$universObj = new site();
        $universObj->refresh($univers);
 		// display only 2 level of channels
 		if (!empty($channels) && !empty($rubriques))
 		{
 			// * = full access we display all channels
 			if (in_array("*", $rubriques))
 			{
		 		foreach($channels as $idChannel=>$data)
				{
					// display first level
					if ($channels[$idChannel]['workflowState'] == "published")
					{
						$checkParents = "";
						if (isset($localPerm[$univers][$service]) && in_array($idChannel, $localPerm[$univers][$service])) 
							$checkParents = "checked='true'";
									
						echo "<item id='".$univers."_".$service."_".$idChannel."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".str_replace("&","&amp;",$channels[$idChannel]['title'])."' type='checkbox' ".$checkParents." partnerfeeds='";
						if($universObj->partnerFeeds)
							echo "1";
						else
							echo "0";
						echo "'>\n";															
					}
					
					// display second level if exist
					if (isset($channels[$idChannel]['childs']))
					{
						foreach($channels[$idChannel]['childs'] as $idChannel2=>$data2)
						{
							if ($data2['workflowState'] == "published")
							{
								$checkChilds = "";
								if (isset($localPerm[$univers][$service]) && in_array($idChannel2, $localPerm[$univers][$service])) 
									$checkChilds = "checked='true'";
								
								echo "<item id='".$univers."_".$service."_".$idChannel2."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".str_replace("&","&amp;",$data2['title'])."' type='checkbox' ".$checkChilds." partnerfeeds='";
								if($universObj->partnerFeeds)
									echo "1";
								else
									echo "0";
								echo "'></item>\n";															
							}
						}
						echo "</item>\n"; 
					}
					else
					{
						echo "</item>\n";
					} 
				}
 			}
 			else
 			{
 				if (!empty($rubriques))
 				{
 					// we need to browse all service channels and compare them with parent permissions channel
	 				foreach($channels as $idChannel=>$data)
					{
						// display first level
						if ($channels[$idChannel]['workflowState'] == "published")
						{
							$checkParents = "";
							if (isset($localPerm[$univers][$service]) && in_array($idChannel, $localPerm[$univers][$service])) 
								$checkParents = "checked='true'";

							if (in_array($idChannel, $rubriques) || in_array($idChannel, $this->getFullTreePermissions($rubriques))) {
								echo "<item id='".$univers."_".$service."_".$idChannel."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".str_replace("&","&amp;",$channels[$idChannel]['title'])."' type='checkbox' ".$checkParents." partnerfeeds='";
								if($universObj->partnerFeeds)
									echo "1";
								else
									echo "0";
								echo "'>\n";	
							}														
						}
						
						// display second level if exist
						if (isset($channels[$idChannel]['childs']))
						{
							foreach($channels[$idChannel]['childs'] as $idChannel2=>$data2)
							{
								if ($data2['workflowState'] == "published")
								{
									$checkChilds = "";
									if (isset($localPerm[$univers][$service]) && in_array($idChannel2, $localPerm[$univers][$service])) 
										$checkChilds = "checked='true'";
									
									if (in_array($idChannel2, $rubriques) || in_array($idChannel2, $this->getFullTreePermissions($rubriques))) {
										echo "<item id='".$univers."_".$service."_".$idChannel2."' im0='plus_ar.gif' im1='plus_ar.gif' im2='plus_ar.gif' text='".str_replace("&","&amp;",$data2['title'])."' type='checkbox' ".$checkChilds." partnerfeeds='";
										if($universObj->partnerFeeds)
											echo "1";
										else
											echo "0";
										echo "'></item>\n";
									}															
								}
							}
							if (in_array($idChannel, $rubriques) || in_array($idChannel, $this->getFullTreePermissions($rubriques)))
								echo "</item>\n"; 
						}
						else
						{
							if (in_array($idChannel, $rubriques) || in_array($idChannel, $this->getFullTreePermissions($rubriques)))
								echo "</item>\n";
						} 
					}
 					
 				}
 			}
 		}
 	}
 	
 	 /**
     * use to display channels parent(s) node(s) in the treeview
     * this function browse array permissions et return full permissions with id channel ancestors (if they don't exist)
     */
    public function getFullTreePermissions($defaultPerm) 
    {
    	$fullPermArray = array();
    	if (!empty($defaultPerm) && is_array($defaultPerm))
    	{
    		foreach ($defaultPerm as $channelId)
    		{
    			// "*" is a special case, it means all channels 
    			if ($channelId != "*")
    			{
    				$channel = new channel();
    				$channel->refresh($channelId);
    				
    				$temp = $channel->getArrayAncestors();
    				if (!empty($temp))
    				{
    					foreach ($temp as $idChannel)
    					{
    						if (!in_array($idChannel, $defaultPerm))
    							$fullPermArray[] = $idChannel;
    					}
    				}
    			}
    		}
    	}
    	return $fullPermArray;
    }
    
    /**
     * check parent permissions before allowing inserts in children permissions
     */
    public function checkPermBeforeInsert($univers, $service, $rubrique)
    {
    	// get parent permissions in order to check children permissions
		$parentAccount = new account();
		$parentAccount->refreshByWcmUser($this->managerId);
		$parentPermissions = $parentAccount->getArrayPermissions();
		
		if (!empty($parentPermissions))
		{
			if (($service == "*") && !isset($parentPermissions[$univers]["*"]))
				return false;
			else if (($rubrique == "*") && !isset($parentPermissions[$univers][$service]["*"]))
				return false;
			else if (!empty($rubrique) && isset($parentPermissions[$univers]["*"]))
				return true;
			else if (!empty($rubrique) && !in_array($rubrique, $parentPermissions[$univers][$service]))
				return false;
			else		
				return true;
		}
		else
			return false;
    }
    
 	/**
     * update family permission when ancestor has changed its permissions
     */
    public function updateFamilyPermissions()
    {
    	$family = self::getAccountsIds($this->wcmUserId, "family");
    	$permissions = $this->getSimpleIdArrayPermissions();
    	$check = false;
    	   	
    	if (!empty($family) && !empty($permissions))
    	{
    		$childAccount = new account();
    			
    		foreach ($family as $id)
    		{
    			$childAccount->refresh($id);
    			$childPermissions = $childAccount->getSimpleIdArrayPermissions();
    			if (!empty($childPermissions))
    			{
    				$compare = array_diff($childPermissions, $permissions);
    				if (!empty($compare))
    				{
    					foreach ($compare as $id2=>$val)
    					{
    						$sql = 'DELETE FROM #__accountPermission WHERE id=?';
        					$params = array($id2);  
        					$rs = $this->database->executeQuery($sql, $params);
        					wcmTrace("PERMISSIONS : delete permissions (id:".$id.") => ".$val);
        					$check = true;
    					}
    				}
    			}
    		}
    	}
    	
    	return $check;
    }
    
	/**
     * Returns string separate by "-" of all account's ancestors
     *
     * @return string with managerId ancestors
     */
    public function getAccountAncestors($separator="-")
    {
    	$result = array();
    	$idString = "";
    	$ancestors[] = $this->wcmUserId;
        $naccount = clone($this);
        	
        while ($naccount->managerId && !empty($naccount->managerId))
        {
        	$naccount->refreshByWcmUser($naccount->managerId);
            if ($naccount->id)
            	$ancestors[] = $naccount->wcmUserId;   
        }
        unset($naccount);
        
        $i=0;
        $result = array_reverse($ancestors);
        foreach ($result as $id)
        {
        	if ($i == 0)
        		$idString = $id;
        	else
        		$idString .= $separator.$id;

        	$i++;	
        }     	
        return $idString;
    }
    
    
	static function setAccountFamilyPerm($wcmUserId, $permArray, $cleanPerm = false) 
	{         
        $where = 'managerId='.$wcmUserId;
        
        $currentAccount = new account();
        $currentAccount->beginEnum($where);
        while ($currentAccount->nextEnum()) 
        {
            $currentAccount->refreshWcmUser();
            
            if ($cleanPerm) $currentAccount->cleanPermissions();
            $currentAccount->setPermissionsFromArray($permArray);           
            echo "Set permission : ".$currentAccount->id." - done !<br />";
            
            self::setAccountFamilyPerm($currentAccount->wcmUserId, $permArray);
        }
        
        $currentAccount->endEnum();
        return true;
    }
    
	static function checkAccountsEmptyPerm($wcmUserId) 
	{         
        $where = 'managerId='.$wcmUserId;
        
        $currentAccount = new account();
        $currentAccount->beginEnum($where);
        while ($currentAccount->nextEnum()) 
        {
            $currentAccount->refreshWcmUser();
            $permArray = $currentAccount->getSimpleIdArrayPermissions();
            
            if (empty($permArray)) 
            	echo "Account id : ".$currentAccount->id." - wcmUserId : ".$currentAccount->wcmUserId." - managerId : ".$currentAccount->managerId."  has no permissions !<br />";
            
            self::checkAccountsEmptyPerm($currentAccount->wcmUserId);
        }
        
        $currentAccount->endEnum();
        return true;
    }
    
	/**
     * init all managers path Id 
     * Used by external call (/automats/scripts/initAccountPath/initAccounts.php) for init
     */

    public function initAllManagerPathId($where = '', $orderBy = 'id') 
    {
    	$project    = wcmProject::getInstance();
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        $i = 0;
        while ($enum->nextEnum()) 
        {
            echo $className." : ".$enum->id."\n";
            
            $path = $enum->getAccountAncestors();
            
            if (!empty($path))
            {
			$connector  = $project->datalayer->getConnectorByReference("biz");
        	$db = $connector->getBusinessDatabase();
        	
        	echo "UPDATE ".$this->tableName." SET managerPathId='".$path."' WHERE id=".$enum->id."\n";
        	/*
            $query = 'UPDATE '.$this->tableName.' SET managerPathId=? WHERE id=?';     
        	if ($db->executeStatement($query, array($path, $enum->id)))    
                $i++;
            else
                echo $className." : ".$enum->id." - error !\n";
            */
            }  
        }
        
        echo $className." : ".$i." done\n";
        
        $enum->endEnum();
    }
}
