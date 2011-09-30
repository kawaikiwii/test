<?php 
/**
 * Project:     WCM
 * File:        biz.folder.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
 /**
 * Definition of a folder
 */

class folder extends bizobject {
    /**
     * (int) site Id
     */
    public $siteId;
    
    /**
     * (int) parent Id
     */
    public $parentId = null;
    
    /**
     * (int) rank
     */
    public $rank;
    
    /**
     * (string) title
     */
    public $title;
    
    /**
     * (string) description
     */
    public $description;
    
    /**
     * (int) template id
     */
    public $templateId;
    
    /**
     * (string) image path
     */
    public $imagePath = "";
    
    /**
     * (string) keywords
     */
    public $keywords;
    
    /**
     * (string) management kind
     */
    public $managementKind;
    
    /**
     * (string) native request used to get content
     */
     
    public $request;
    
    /**
     * (int) publication Id
     */
    public $publicationId;
    
    /**
     * (string) type
     */
    public $type;
    
    /**
     * (string) css information
     */
     
    public $css;
    
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */

    protected function setDefaultValues() {
        parent::setDefaultValues();
        
        $this->parentId = null;
        $this->rank = 0;
        $this->managementKind = 'date';
        $config = wcmConfig::getInstance();
        $this->request = null; //serialize(array("query" => $config['wcm.folder.query']));
    }
    
    /**
     * Returns the type of the folder
     * @return array
     */

    static function getTypeList() {
        return array("perm"=>_BIZ_FOLDER_PERM, "auto"=>_BIZ_FOLDER_AUTO, "temp"=>_BIZ_FOLDER_TEMP);
    }
    
	/**
     * get folder Id from its sourceId
     *
     * @param int $sourceId folder's sourceId
     */
    public function getIdFromSourceId($sourceId)
    {
    	$sql = "SELECT id FROM ".$this->tableName." WHERE sourceId=".$sourceId;
        $result = $this->database->executeScalar($sql);	
    	
    	if (!empty($result)) return $result;
    	else return false;
    }
    
    /**
     * Returns the label type of the folder
     * @return array
     */

    static function getLabelFromType($type) {
        switch ($type) {
            case "perm":
                return _BIZ_FOLDER_PERM;
                break;
            case "auto":
                return _BIZ_FOLDER_AUTO;
                break;
            case "temp":
                return _BIZ_FOLDER_TEMP;
                break;
        }
    }
    
    /**
     * Refresh folder by its title
     *
     * @param string $title Folder's title
     */

    public function refreshByTitle($title) {
        return $this->refresh($this->database->executeScalar("SELECT id FROM $this->tableName WHERE title=?", array($title)));
    }
    
    /**
     * Return the parent folder of the object
     *
     * @return folder parent folder or null
     */

    public function getParentFolder() {
        $folder = new folder(null, $this->parentId);
        return ($folder->id) ? $folder : null;
    }
    
    /**
     * @return folder array
     */

    function getListFolders($where, $orderBy = 'id') {
        $folders = array();
        
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum())
            $folders[$enum->id] = $enum->title;
            
        $enum->endEnum();
        
        return $folders;
    }
    
    /**
     * @return folder array
     */

    function getListFoldersForGetvars($where, $orderBy = 'id') {
        $folders = array();
        
        $className = $this->getClass();
        $enum = new $className();
        
        if (!$enum->beginEnum($where, $orderBy))
            return null;
            
        while ($enum->nextEnum())
            $folders['folders'][] = array('id'=>$enum->id, 'label'=>getConst($enum->title), 'workflowState'=>$enum->workflowState, 'idParent'=>$enum->parentId);
            
        $enum->endEnum();
        
        return $folders;
    }
    
    /**
     * Inserts or Updates object in database
     *
     * @return boolean true on success, false on failure
     */

    protected function store() {
        if (!parent::store())
            return false;
            
        // Update cache of folders (of site)
        wcmCache::setElem('folder_of_site'.$this->siteId, $this->id, $this);
        return true;
    }
    
    /**
     * Deletes current object from database
     *
     * @return true on success or an error message (string)
     */

    public function delete($userId = null) {
        $sid = $this->siteId;
        $id = $this->id;
        if (!parent::delete())
            return false;
            
        // Update cache
        wcmCache::unsetElem('folder_of_site'.$sid, $id);
        
        return true;
    }
    
    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */

    function ofClause($of) {
        if ($of == null || !is_array($of))
            return;
            
        $sql = null;
        
        foreach ($of as $key=>$value) {
            switch ($key) {
                case "site":
                    if ($sql != null)
                        $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;
            }
        }
        return $sql;
    }
    
    /**
     * Says whether the id of the instance is present in the relation array
     *
     * @param  $objectid - object to test
     * @param $relations - array of relation ids
     *
     * @return true if the id is prensent
     */

    public function scanlist($objectid, $objectclass, $relations) {
        foreach ($relations as $key=>$value) {
            $arr = explode('-', $value);
            $relclass = $arr[0];
            $relid = $arr[1];
            
            if ($objectid == $relid && $objectclass == $relclass)
                return true;
        }
        return false;
    }
    
    /**
     * Returns content of the folder
     *
     * @param  int  $limit Optional maximum number of objects to return
     * @param  date $date  Optional date used to compute folder's content (default is today's date)
     * @param  bool $returnBizobject Return related bizobject when a bizrelation if found (default is false)
     * @param  bool $toXML TRUE when this method is invoked in the context of 'toXML()' (default is false)
     *
     * @return array Array of bizobjects (or null if management method is invalid)
     */

    public function getContent($limit = 10, $date = null, $returnBizobject = false, $toXML = false) {
        // set default value for date parameter?
        if ($date === null)
            $date = date('Y-m-d');
            
        //get query
        $search_query = unserialize($this->request);
        
        $query = (isset($search_query['query']) ? $search_query['query'] : '');
        //replace smart tag @folderId if any
        $query = str_replace("@folderId", $this->id, $query);
        
        $orderBy = isset($search_query['orderBy']) ? $search_query['orderBy'] : null;
        $limit = (isset($search_query['limit']) && is_numeric($search_query['limit'])) ? $search_query['limit'] : $limit;
        
        $results = array();
        if ($query != "") {
            $config = wcmConfig::getInstance();
            $engine = $config['wcm.search.engine'];
            $search = wcmBizsearch::getInstance($engine);
            $total = $search->initSearch('quickSearch', $query, $orderBy);
            $results = $search->getDocumentRange(0, $limit, 'quickSearch', false);
        }
        //get relations
        $relations = wcmBizrelation::getBizobjectRelations($this, wcmBizrelation::IS_COMPOSED_OF, null, $toXML);
        
        //sort them in order
        $mixedresults = array();
        $forcedcontentids = array();
        foreach ($relations as $rel) {
            $destclass = $rel['destinationClass'];
            $destid = $rel['destinationId'];
            $forcedcontentids[] = $destclass."-".$destid;
            
            if ($returnBizobject)
                $mixedresults[$rel['rank']] = new $destclass(wcmProject::getInstance(), $destid);
            else
                $mixedresults[$rel['rank']] = $rel;
                
        }
        $counter = 0;
        $counter2 = 1;
        for ($i = 1; $i < $limit; $i++) {
            if (isset($results[$counter]) && $this->scanlist($results[$counter]->id, get_class($results[$counter]), $forcedcontentids)) {
                $counter++;
            } else {
                if (!isset($mixedresults[$counter2]) && isset($results[$counter])) {
                    $mixedresults[$counter2] = $results[$counter];
                    $counter++;
                } else if (!isset($mixedresults[$counter2])) {
                    $mixedresults[$counter2] = null;
                }
                
                $counter2++;
            }
            
        }
        //sort using indexes (which are the ranks)
        ksort($mixedresults);
        
        return $mixedresults;
    }
    
    /**
     * Returns hierarchical array of folder's paths for current site
     *
     * @param array $folders Array to update (should be initialized)
     * @param int $excludeId Optional ID of menu to exclud forom the list
     * @param string $prefix Initial prefix (used for recursive call)
     * @param int $parentId Initial folder (or zero to take root folders, by default)
     * @param string $separator Separator tu use in path (default is ' :: ')
     *
     * @return array An assoc array of folders in hierarchical order (key is id, value is folder path)
     */

    static function getFolderHierarchy(array &$folders = null, $excludeId = 0, $prefix = null, $parentId = 0, $separator = ' :: ') {
        if (!$folders)
            $folders = array();
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();
        
        $enum = new folder($project);
        if (!$enum->beginEnum('siteId = '.$session->getSiteId(), 'type, title'))
            return null;
        $folderList = array();
        while ($enum->nextEnum()) {
            $folderList[$enum->id] = clone ($enum);
        }
        $enum->endEnum();
        
        foreach ($folderList as $folder) {
            if ($folder->parentId == $parentId) {
                //echo $folder->title."<br />\n";
                $folders[""] = null;
                // Add menu (unless it is excluded)
                $path = ($prefix) ? $prefix.$separator.getConst($folder->title) : getConst($folder->title);
                if ($folder->id != $excludeId) {
                    $workflowstate = "style= 'font-weight : bold ; color : ";
                    switch ($folder->workflowState) {
                        case "draft":
                            $workflowstate .= "red";
                            break;
                        case "approved":
                            $workflowstate .= "blue";
                            break;
                        case "published":
                            $workflowstate .= "green";
                            break;
                        default:
                            $workflowstate .= "red";
                            break;
                    }
                    $workflowstate .= ";'";
                    $folders[$folder->id] = "<span ".$workflowstate.">".$path."</span>";
                }
                
                // Recursively add sub-menus
                folder::getFolderHierarchy($folders, $excludeId, $path, $folder->id, $separator);
            }
        }
        return $folders;
    }

    static function renderBrowseFoldersPanelForGUI() {
    	$session = wcmSession::getInstance();
		
        $html = '<div class="resultBar">';
        $html .= _BIZ_FOLDERS_HIERARCHY;
        $html .= '</div>';
        $html .= '<ul class="browse">';
        
        $folders = folder::getFolderHierarchy();
        
        $enum = new folder();
        $where = "siteId = '".$session->getSiteId()."' AND workflowstate != 'archived'";
        if ($enum->beginEnum($where, "workflowState DESC, rank ASC")) {
            while ($enum->nextEnum()) {
                $folder = new folder();
                $folder->refresh($enum->id);
                $workflowstateIcon = "";
                switch ($folder->type) {
                    case "perm":
                        $workflowstateIcon .= "folder_perm";
                        break;
                    case "temp":
                        $workflowstateIcon .= "folder_temp";
                        break;
                    case "auto":
                        $workflowstateIcon .= "folder_auto";
                        break;
                }
                // make a nice display for current channel
                 $html .= '<li class="'.$workflowstateIcon.'" title="'.folder::getLabelFromType($folder->type).'">';
                $html .= '<a href="';
                $html .= wcmMVC_Action::computeObjectURL('folder',$enum->id);
                $html .= '">'.$enum->title.'</a> <span style="font-size:xx-small">['.$enum->workflowState.']</span></li>';
            }
        }
		
		
		/*
        foreach ($folders as $id=>$path) {
            $folder = new folder();
            $folder->refresh($id);
            $workflowstateIcon = "";
            switch ($folder->type) {
                case "perm":
                    $workflowstateIcon .= "folder_perm";
                    break;
                case "temp":
                    $workflowstateIcon .= "folder_temp";
                    break;
                case "auto":
                    $workflowstateIcon .= "folder_auto";
                    break;
            }
            // make a nice display for current channel
            $parts = explode(' :: ', $path);
            $html .= '<li class="'.$workflowstateIcon.' level-'.strtolower(chr(64 + min(5, count($parts)))).'" title="'.folder::getLabelFromType($folder->type).'">';
            $html .= '<a href="';
            $html .= wcmMVC_Action::computeObjectURL('folder', $id);
            $html .= '">'.array_pop($parts).'</a></li>';
        }*/
		
        
        
        unset($folders);
        $html .= '</ul>';
        
        return $html;
    }
    
    /**
     * Generate html content to display Special folders
     *
     */

    function getSpecialfoldersHtml($bizobject) 
    {
        $session = wcmSession::getInstance();
        $siteId = $session->getSiteId();
        $folderType = folder::getTypeList();
        
        $foldersIds = array();
        
        if (isset($bizobject->folderIds) && !empty($bizobject->folderIds))
        {
        	if (!is_array($bizobject->folderIds)) $foldersIds = unserialize($bizobject->folderIds);
        	else $foldersIds = $bizobject->folderIds;
        }
        	
        //$foldersIds = (isset($bizobject->folderIds) && $bizobject->folderIds != '') ? unserialize($bizobject->folderIds) : array();
        krsort($folderType);
        $html = '<table class="channels_choice" cellpadding="0" cellspacing="0"><tr>';
        
        foreach ($folderType as $type=>$value) {
            $listFolder = $this->getListFolders("siteId ='".$siteId."' AND type='".$type."' AND (workflowState = 'published' OR workflowState = 'approved')", "rank ASC");
            if (! empty($listFolder)) {
                $html .= '<td class="channel_choice_block">'.$value.'<br/><br/>';
                foreach ($listFolder as $folderId=>$folderTitle) {
                    $folder = new folder();
                    $folder->refresh($folderId);
                    $labelColor = ($folder->workflowState == 'published') ? 'green' : 'blue';
                    
                    // no checkboxes for "auto" folder's type
                    if ($type != "auto") {
                        $currentChannelIdFound = false;
                        $html .= '<input type="checkbox" name="folderIds[]" id="'.$folderId.'" value="'.$folderId.'"';
                        
                        foreach ($foldersIds as $currentChannelId) {
                            if ($currentChannelId == $folderId && !$currentChannelIdFound) {
                                $html .= ' checked />';
                                $currentChannelIdFound = true;
                                break;
                            }
                        }
                        if (!$currentChannelIdFound)
                            $html .= ' />';
                    }
                    
                    $html .= '<label for="'.$folderId.'" style="color:'.$labelColor.';">'.getConst($folderTitle)."</label><br />";
                }
                $html .= '</td>';
            }
        }
        $html .= '</tr></table>';
        return $html;
    }
    
    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */

    public function checkValidity() {
        if (!parent::checkValidity())
            return false;
            
        if (trim(' '.$this->title) == '') {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->title) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_TITLE_TOO_LONG;
            return false;
        }
        
        if ($this->keywords && strlen($this->keywords) > 255) {
            $this->lastErrorMsg = _BIZ_ERROR_KEYWORDS_TOO_LONG;
            return false;
        }
        
        return true;
    }

    public function save($source = null) {
    	if (isset($source["query"])) {
    		$request = array();
			$request["query"] = $source["query"];
			$request["orderBy"] = $source["orderBy"];
			$request["limit"] = $source["limit"];
			$source["request"] = $request;
    	}
		
        if (!parent::save($source)) 
        {
            return false;
        } 
        else 
        {
        	// update site perm if exist
    		if (isset($source["siteList"]) && !empty($source["siteList"])) 
    			$this->updateSitePermissions($source["siteList"]);
    	
            return $this->storeObjects();
        }
    }
    
    /*
     * gestion des permissions sur les dossiers par univers
     */
     public function updateSitePermissions($sitePerm) 
     {
     	if (is_array($sitePerm) && !empty($sitePerm))
     		$this->cleanSitePermissions();
     	
     	foreach ($sitePerm as $univers) 
     	{
        	$sql = 'INSERT INTO #__folderPermission (folderId,univers) VALUES (?,?)';
            $params = array($this->id, $univers);
            $this->database->executeQuery($sql, $params);
        }
     }
    
    /*
     * supprime les  permissions existantes du dossier courant
     */
	public function cleanSitePermissions() 
	{
        $sql = 'DELETE FROM #__folderPermission WHERE folderId=?';
        $params = array($this->id);
        $this->database->executeQuery($sql, $params);
    }
    
	/*
     * récupère les permissions existantes
     */
	public function getSitePermissions() {
        $sql = 'SELECT * FROM #__folderPermission WHERE folderId=?';
        $params = array($this->id);
        
        $rs = $this->database->executeQuery($sql, $params);     
        $permissionsArray = array();
        
        if ($rs) 
        {
            while ($rs->next()) 
                $permissionsArray[] = $rs->get('univers');
        }
        return $permissionsArray;
    }
    
    
	/*
     * Créée pour les univers transverses type BIPH (Olivier)
     */
    public function getFoldersMultiUniverse($universe) {
    	$sql = 'SELECT folderId FROM #__folderPermission WHERE univers=?';
        $params = array($universe);
        
        $rs = $this->database->executeQuery($sql, $params);
        
        $folderArray = array();
        while ($rs->next()) {
            $folder = $rs->get('folderId');
            if (!in_array($folder,$folderArray))
                $folderArray[] = $folder;
        }
        
        return ($folderArray);
    }
}
