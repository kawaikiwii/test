<?php 
/**
 * Project:     WCM
 * File:        wcm.GUI.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
 /**
 * This class contains helper to render
 * the graphical user interface
 */

class wcmGUI extends wcmFormGUI {
    /**
     * Renders the typical form for a wcmObject, including some
     * hidden fields (class, id, todo) and mainActionController
     *
     * @param wcmObject $wcmObject Related wcmObject
     */

    static function openObjectForm($wcmObject, $attributes = null) {
        if (!is_array($attributes))
            $attributes = array();
        $attributes['class'] = 'mainForm';
        echo '<script type="text/javascript">var wcmActionController  = new WCM.ActionController();</script>';
        self::openForm('mainForm', wcmMVC_Action::computeURL(), array('_wcmClass'=>$wcmObject->getClass(), 'id'=>$wcmObject->id, '_wcmTodo'=>($wcmObject->isLockOptimistic()) ? 'save' : 'checkin'), $attributes);
    }
    
    /**
     * Renders an object given its class and ID by calling
     * wcmGUI::renderObject().
     *
     * @param string     $objectClass The class of the object to render
     * @param int        $objectId    The ID Of the object to render
     * @param string     $template    The template, XSL, etc. to use
     * @param array|null $params      Assoc. array of contextual parameters
     *
     * @return string|null The result of rendering the object, or null on error
     */

    public static function renderObjectById($objectClass, $objectId, $xsl = null, $xslParams = null) {
        if (!$objectClass || !is_subclass_of($objectClass, 'wcmSysObject'))
            return null;
            
        $object = new $objectClass(wcmProject::getInstance(), $objectId);
        return self::renderObject($object, $template, $params);
    }
    
    /**
     * Renders an object using a template, an XSL, etc.
     *
     * Determines the nature of $template from the file name extension:
     *
     *     .php => PHP script to include
     *     .tpl => Smarty template to execute
     *     .xsl => XSL stylesheet to process
     *
     * If $template is relative, the base directory corresponding to
     * the nature of $template is prepended:
     *
     *     .php => WCM_DIR
     *     .tpl => wcmConfig['wcm.templates.path']
     *     .xsl => {business/}xsl (depending on object type)
     *
     * @param wcmSysObject $object   The object to render
     * @param string       $template The template, XSL, etc. to use
     * @param array        $params   Assoc. array of contextual parameters
     *
     * @return string The result of rendering the object
     */

    public static function renderObject(wcmSysObject $object, $template, array $params = array()) {
        $config = wcmConfig::getInstance();
        $project = wcmProject::getInstance();
        
        $params['object'] = $object;
        
        if (!isset($params['callback']))
            $params['callback'] = 'onSelectItem';
            
        if ($object->createdBy && !isset($params['createdByUserName'])) {
            $user = new wcmUser($object);
            if ($user->refresh($object->createdBy))
                $params['createdByUserName'] = $user->name;
        }
        
        if ($object->modifiedBy && !isset($params['modifiedByUserName'])) {
            $user = new wcmUser($object);
            if ($user->refresh($object->modifiedBy))
                $params['modifiedByUserName'] = $user->name;
        }
        
        if ($object->workflowState && !isset($params['workflowState'])) {
            $workflowState = $project->workflowManager->getWorkflowStateByCode($object->workflowState);
            if ($workflowState)
                $params['workflowState'] = $workflowState->name;
        }
        
        // get available transitions
        if (!isset($params['transitions'])) {
            $transitions = array();
            foreach ($object->getAvailableTransitions() as $transition) {
                $transitions[] = array('id'=>$transition->id, 'name'=>getConst($transition->name));
            }
            
            $params['transitions'] = $transitions;
        }
        
        if (!isset($params['locked'])) {
            if (method_exists($object, 'isLocked') && $object->isLocked())
                $locked = 'TRUE';
            else if (!method_exists($object, 'getLockInfo'))
                $locked = 'FALSE';
            else if ($object->getLockInfo()->userId == wcmSession::getInstance()->userId)
                $locked = 'ME';
            else
                $locked = 'FALSE';
            $params['locked'] = $locked;
        }
        
        if (!isset($params['lockedBy'])) {
            $lockedBy = $object->getLockInfo()->userId;
            $params['lockedBy'] = $object->getProject()->membership->getUserById($lockedBy)->name;
        }
        
        $extension = pathinfo($template, PATHINFO_EXTENSION);
        switch ($extension) {
            case '':
            case 'php':
                if ($template[0] != '/')
                    $template = WCM_DIR.$template;
                extract($params);
                ob_start();
                require ($template);
                $html = ob_get_clean();
                break;
                
            case 'tpl':
                if (!ereg("^(\\\\|[a-zA-Z]:/|/)", $template))
                    $template = $config['wcm.templates.path'].$template;
                    
                $templateGenerator = new wcmTemplateGenerator($project);
                $html = $templateGenerator->executeTemplate($template, $params);
                break;
                
            case 'xsl':
                if ($template[0] != '/') {
                    $isBizobject = is_subclass_of($object, 'wcmBizobject');
                    $template = WCM_DIR.($isBizobject ? '/business' : '').'/xsl/'.$template;
                }
                
                $xmlDoc = new DOMDocument();
                if (!$xmlDoc->loadXML($object->toXML())) {
                    $msg = 'renderObject: invalid XML representation for object %s(%d)';
                    $project->logger->logError(sprintf($msg, $object->getClass(), $object->id));
                    return null;
                }
                
                $xslDoc = new DOMDocument();
                if (!$xslDoc->load($template)) {
                    $msg = 'renderObject: invalid XSL stylesheet "%s"';
                    $project->logger->logError(sprintf($msg, $template));
                    return null;
                }
                
                $xsltProc = new XSLTProcessor;
                $xsltProc->importStyleSheet($xslDoc);
                $xsltProc->registerPHPFunctions();
                
                if ($params) {
                    foreach ($params as $name=>$value)
                        $xsltProc->setParameter('', $name, $value);
                }
                $html = $xsltProc->transformToXML($xmlDoc);
                
            default:
                $msg = 'renderObject: unsupported template type "%s"';
                $project->logger->logError(sprintf($msg, strtoupper($extension)));
                $html = null;
                break;
        }
        
        return $html;
    }
    
    /**
     * Renders an object as a search result given its class and ID.
     *
     * @param string      $objectClass   The class of the object to render
     * @param int         $objectId      The ID of the object to render
     * @param string|null $xmlParameters XML-encoded parameters if any
     *
     * @return string The result of rendering the object
     */

    public static function renderSearchResultObject($objectClass, $objectId, $xmlParameters = null) {
        $xsl = null;
        
        $parameters = wcmXML::xmlToArray($xmlParameters);
        
        if (isset($parameters['viewName'])) {
            $isBizobject = is_subclass_of($objectClass, 'wcmBizobject');
            $xslPrefix = WCM_DIR.($isBizobject ? '/business' : '').'/xsl/';
            $xslSuffix = '.xsl';
            
            $searchConfig = wcmBizsearchConfig::getInstance();
            $view = $searchConfig->getView($parameters['configId'], $parameters['pageType'], $parameters['viewName']);
            if ($view) {
                $xsls = $view->xpath('bizObjects/bizObject[@name="'.$objectClass.'"]/xsl');
                if ($xsls) {
                    $xsl = array_shift($xsls);
                    if ($xsl) {
                        if (!file_exists($xslPrefix.$xsl.$xslSuffix))
                            $xsl = null;
                    }
                }
                
                if (!$xsl) {
                    $xsls = $view->xpath('bizObjects/bizObject[@name="default"]/xsl');
                    if ($xsls) {
                        $xsl = array_shift($xsls);
                        if ($xsl) {
                            if (!file_exists($xslPrefix.$xsl.$xslSuffix))
                                $xsl = null;
                        }
                    }
                }
            }
        }
        
        if (!$xsl) {
            $xsl = $objectClass;
            $parameters['viewName'] = $xsl;
        }
        
        $xslParams = array();
        $xslParams['xmlParameters'] = wcmXML::arrayToxml($parameters);
        
        return self::renderObjectById($objectClass, $objectId, $xsl, $xslParams);
    }
    
    /**
     * Returns DHTML string representing a quick search box
     *
     * @param string $filter Optional filter to apply to the query
     * @param string $xml Optional xsl code (default 'quickSearch')
     * @param int $pageSize Optional page size (default 8)
     * @param string $uid Optiona unique id to save info in session (default 'quickSearch')
     * $param int $start_search if set, the search is started automatically on page load
     *
     * Note: this real XSL will be in /business/xsl/$xsl.xsl
     *
     * @return string DHTML
     */

    static function renderQuickSearchBox($filter = null, $xsl = 'quickSearch', $pageSize = 10, $uid = 'quickSearch', $start_search = null) {
        ob_start();
        ob_implicit_flush(false);
        
?>
<script type="text/javascript">
            leftHand_quickSearch = function()
            {
                relationSearch.search('quickSearch',$('_wcm_qs_query').value, {
                    useLinkManager: false,
                    filter: '<?php echo $filter; ?>',
                    pageSize: '<?php echo $pageSize; ?>',
                    style: 'quickSearch',
                    currentPage: 1,
                    resultSet: '_wcm_qs_resultSet',
                    ajaxHandler: 'search/quickSearch',
                    uid: 'quickSearch'
                });
            }
            
            <?php
                if($start_search)
                    echo "window.onload = leftHand_quickSearch;";
            ?>
            
</script>
<div id="quickSearch">
    <form onsubmit="leftHand_quickSearch(); return false">
        <input type="text" name="query" id="_wcm_qs_query" value="" /><input type="submit" value="<?php echo _SEARCH; ?>">
    </form>
    <div id="_wcm_qs_resultSet">
    </div>
</div>
<?php 
$html = ob_get_contents();
ob_end_clean();

return $html;
}

/**
 * Returns DHTML string representing a browse panel
 * (a hierarchy of sections for current site)
 *
 * @return string DHTML
 */

static function renderBrowsePanel() {
    $html = '<div class="resultBar">';
    $html .= _BIZ_SECTIONS_HIERARCHY;
    $html .= '</div>';
    $html .= '<ul class="browse">';
    
    $channels = channel::getChannelHierarchy();
    
    foreach ($channels as $id=>$path) {
        // make a nice display for current channel
        $parts = explode(' :: ', $path);
        $html .= '<li class="channel level-'.strtolower(chr(64 + min(5, count($parts)))).'">';
        $html .= '<a href="';
        $html .= wcmMVC_Action::computeObjectURL('channel', $id);
        $html .= '">'.array_pop($parts).'</a></li>';
    }
    
    unset($channels);
    $html .= '</ul>';
    
    return $html;
}

/**
 * Returns DHTML string representig the first 30 last records from selected Object's Class
 *
 */

static function renderBrowseObject($classname, $where = null, $orderby = null) {
    $config = wcmConfig::getInstance();
    
    $html = '<div class="resultBar">';
    $html .= _BIZ_LATEST_OBJECTS_BROWSE;
    $html .= ' <b>'.$classname.'</b>';
    $html .= '</div>';
    $html .= '<ul class="browse" style="padding-left:0; margin-left:0;">';
    
    $objects = wcmBizobject::getBizobjects($classname, $where, $orderby);
    foreach ($objects as $object) {
        $html .= '<li style="padding-left:0; margin-left:0;"><a href="'.$config['wcm.backOffice.url'].'?_wcmAction=business/'.$classname.'&id='.$object->id.'" title="'.$object->title.'">'.substr($object->title, 0, 27).'...</a></li>';
    }
    
    $html .= '</ul>';
    return $html;
}

/**
 * Returns DHTML string representing the user history
 *
 * @param string $objectClass Optional class object to restrict history
 * @param integer $offset Optional offset to retrieve statistics
 * @param integer $limit Optional limit to retrieve statistics
 *
 * @return string DHTML
 */

static function renderObjectHistory($objectClass = null, $offset = 0, $limit = 12) {
    $rs = wcmSession::getInstance()->getViewHistory($objectClass, $offset, $limit);
    if ($rs) {
        $html = '<div class="resultBar">';
        $html .= _LAST_VIEWED_ITEMS;
        $html .= '</div>';
        $html .= '<ul class="history">';
        while ($rs->next()) {
            $item = $rs->getRow();
            $html .= '<li class="'.$item['objectClass'].'">';
            
            if ($item['linkable']) {
                $html .= '<a href="'.wcmMVC_Action::computeObjectURL($item['objectClass'], $item['objectId']).'">'.getConst($item['info']).'</a>';
            } else {
                $html .= getConst($item['info']);
            }
            $html .= '</li>';
        }
        unset($rs);
        $html .= '</ul>';
        
        return $html;
    }
}

/**
 * Renders the main DHTML menu of the application
 */

static function renderMainMenu() {
    $session = wcmSession::getInstance();
	$html = null;
	foreach (wcmProject::getInstance()->layout->getRootMenus() as $rootMenu)
	{
		// Manage Menu Permission
		if (!$session->isAllowed($rootMenu, wcmPermission::P_READ))
			continue;
		$html .= '<ul class="'.$rootMenu->name.'">';
		$html .= self::renderSubMenus($rootMenu->id, true);
		$html .= '</ul>';
	}
     
	echo $html;
    //return;
    
    /* OPTIMISATION NSTEIN 29/06/2009 */
    /*$config = wcmConfig::getInstance();
    $key = $config['wcm.project.guid'].'htmlmenu';
    
    // Store in session because it is proper for each user
    // and we want to refresh it on Logout
    if (!isset($_SESSION[$key])) {
        $session = wcmSession::getInstance();
        
        $html = null;
        foreach (wcmProject::getInstance()->layout->getRootMenus() as $rootMenu) {
            // Manage Menu Permission
            if (!$session->isAllowed($rootMenu, wcmPermission::P_READ))
                continue;
                
            $html .= '<ul class="'.$rootMenu->name.'">';
            $html .= self::renderSubMenus($rootMenu->id, true);
            $html .= '</ul>';
        }
        $_SESSION[$key] = $html;
    }
    echo $_SESSION[$key];*/
}

/**
 * Returns a DHTML string corresponding to a specific menu entry
 * Note: this method is invoked from renderMainMenu.
 *
 * @param int       The menu id to display
 * @param boolean   True to exclude bizMenus
 */

static function renderSubMenus($menuid, $parentIsRoot = false) {
    $session = wcmSession::getInstance();
    $project = wcmProject::getInstance();
    $config = wcmConfig::getInstance();
    
    // Display menus
    $dhtml = '';
    
    $menus = $project->layout->getMenus();
    if ($menuid == null)
        $menuid = 0;
        
    foreach ($menus as $menu) {
        if ($menu->parentId != $menuid)
            continue;
            
        // Manage Menu Permission
        if (!$session->isAllowed($menu, wcmPermission::P_READ))
            continue;
            
        // Use constants from DB for I18N
        $name = (defined($menu->name)) ? addslashes(constant($menu->name)) : addslashes($menu->name);
        
        $dhtml .= '<li>';
        
        if (!$menu->isDynamic && $menu->subMenusCount() <= 0 && $parentIsRoot) {
            $url = isset($menu->action) ? $config['wcm.backOffice.url'].'?_wcmAction='.$menu->action : '';
            $dhtml .= '<a href="'.$url.'" class="single">';
            $dhtml .= stripslashes($name);
            $dhtml .= '</a>';
        } else if (!$menu->url && !$parentIsRoot) {
            $dhtml .= '<span class="">';
            $dhtml .= stripslashes($name);
            $dhtml .= '</span>';
        } else if (!$menu->url && $parentIsRoot) {
            $dhtml .= '<a href="">';
            $dhtml .= stripslashes($name);
            $dhtml .= '</a>';
        } else {
            $url = '';
            // determine menu url
            if (strpos($menu->url, 'javascript:') !== 0) {
                // prefix with back-office absolute url
                $url .= $config['wcm.backOffice.url'].$menu->url;
                // suffix with action code
                $url .= (strpos($url, '?')) ? '&amp;' : '?';
                $url .= '_wcmAction='.$menu->action;
            } else {
                $url .= $menu->url;
            }
            $dhtml .= '<a href="'.$url.'">';
            $dhtml .= stripslashes($name);
            $dhtml .= '</a>';
        }
        
        // Add sub-menus ?
        if ($menu->isDynamic) {
            $dhtml .= '<ul>';
            $dhtml .= self::renderSubMenusDynamic($menu);
            $dhtml .= '</ul>';
        } else if ($menu->subMenusCount() > 0) {
            $dhtml .= '<ul>';
            $dhtml .= self::renderSubMenus($menu->id);
            $dhtml .= '</ul>';
        }
        
        $dhtml .= '</li>'."\r\n";
    }
    
    return $dhtml;
}

/**
 * Returns a DHTML string corresponding to a specific dynamic menu entry
 * Note: this method is invoked from renderSubMenus.
 *
 * @param int       The menu id to display
 * @param boolean   True to exclude bizMenus
 */

static function renderSubMenusDynamic($menu) {
    $config = wcmConfig::getInstance();
    
    $dhtml = '';
    
    switch ($menu->name) {
        case '_MENU_USER_ADVANCED_SEARCH':
            $searchConfig = wcmBizsearchConfig::getInstance();
            $searches = $searchConfig->getSearches();
            foreach ($searches as $search) {
                if ($search['menuAction']) {
                    $configId = $search['id'];
                    $page = $searchConfig->getPage($configId, $search['defaultPage']);
                    
                    $url = $config['wcm.backOffice.url'].'index.php'.'?_wcmAction='.$search['menuAction'].'&_wcmTodo='.$page['todo'].'&configId='.$configId;
                    
                    $dhtml .= '<li><a href="'.$url.'">';
                    $dhtml .= htmlspecialchars(getConst($search['name']));
                    $dhtml .= '</a></li>';
                }
            }
            break;
            
        case '_MENU_USER_SAVED_SEARCHES':
            $savedSearch = new wcmSavedSearch();
            $where = "userId = ".wcmSession::getInstance()->userId;
            
            if ($savedSearch->beginEnum($where, "name")) {
                while ($savedSearch->nextEnum()) {
                    $url = $config['wcm.backOffice.url'].'index.php?'.$savedSearch->url;
                    
                    $dhtml .= '<li><a href="'.$url.'">';
                    $dhtml .= htmlspecialchars($savedSearch->name);
                    $dhtml .= '</a></li>';
                }
            }
            break;
    }
    
    return $dhtml;
}

/**
 * Returns a DHTML string corresponding to the current
 * object menu, retrieved from wcmMVC_Action::getContext()
 *
 * @param bool $renderMainDiv TRUE to render main 'objectMenuBar' div (by default)
 */

static function renderObjectMenu($renderMainDiv = true, $disabled = array()) {

    echo '<style type="text/css">#objectMenu h3 { font-size:14px; position:absolute; }</style>';
    
    $sysobject = wcmMVC_Action::getContext();
    
    if ($renderMainDiv) {
        if (isset($sysobject->workflowState) && ($sysobject->workflowState == 'published')) {
            echo '<div id="objectMenu" style="background:red;">&nbsp;';
        } else {
            echo '<div id="objectMenu">';
        }
    }
    
    if ($sysobject && $sysobject instanceOf wcmObject) {
        $session = wcmSession::getInstance();
        $project = wcmProject::getInstance();
        
        // Retrieve information from sysobject
        $id = $sysobject->id;
        $className = $sysobject->getClass();
        if ($sysobject instanceOf wcmSysObject)
            $classLabel = getConst($sysobject->getMasterClass()->name);
        else
            $classLabel = getConst('_'.strtoupper($className));
            
        // Retrieve lock information
        $isEditable = $sysobject->isEditable();
        $lockInfo = $sysobject->getLockInfo(false);
        $isLocked = ($lockInfo->userId != 0);
        $lockUser = new wcmUser($project, $lockInfo->userId);
        
        // Lock is obsolete is user has been deleted
        if ($isLocked && !$lockUser->id) {
            $lockInfo->delete();
            $lockInfo->userId = 0;
            $isLocked = false;
        }
        
        // Is bizobject part of the current working site?
        if (property_exists($sysobject, 'siteId') && isset($sysobject->siteId) && $sysobject->siteId != $session->getSiteId()) {
            $disabled['save'] = true;
            $disabled['lock'] = true;
            $disabled['unlock'] = true;
            $disabled['checkin'] = true;
            $disabled['checkout'] = true;
            $disabled['undocheckout'] = true;
            $disabled['delete'] = true;
            
?>
<script type="text/javascript">
                    var params = {
                        url: 'business/modules/modalbox/wrong_site.php',
                        parameters: {
                        currentSiteId: <?php echo $session->getSiteId(); ?>,
                        objectSiteId: <?php echo $sysobject->siteId; ?>
                        }
                    };
                    
                    wcmModal.showOkCancel('Object is in a Different Site', params, function(argResponse) {
                        if (argResponse == 'OK')
                        {
                            newLoc = window.location + '&_wcmSiteId=<?php echo $sysobject->siteId; ?>';
                            window.location = newLoc;
                        }
                    });
    
                    
</script>
<?php 
}

?>
<script type="text/javascript">
    // wcmModal.show('Change','warning');
</script>
<?php 
// Compute permissions
$permissions = $session->getPermissions($sysobject);

$workflowInfo = null;
if (property_exists($sysobject, 'workflowState')) {
    $workflowState = $project->workflowManager->getWorkflowStateByCode($sysobject->workflowState);
    if ($workflowState) {
        if ($sysobject->id && $isEditable) {
            // get available transitions
            $transitions = array();
            foreach ($sysobject->getAvailableTransitions() as $transition) {
                $transitions[] = array('code'=>$transition->code, 'name'=>getConst($transition->name), 'url'=>self::getTodoURL('transition', array('transition'=>$transition->id)));
            }
            $workflowInfo = array('code'=>'workflowState', 'wkCode'=>$workflowState->code, 'name'=>getConst($workflowState->name), 'url'=>null, 'child'=>$transitions);
        } else {
            $workflowInfo = array('code'=>'workflowState', 'name'=>$workflowState->name);
        }
    }
}

$a_save = array("code"=>"save", "name"=>_SAVE, "url"=>self::getTodoURL('save'), 'inactive'=>in_array('save', $disabled));

$a_refresh = array("code"=>"reload", "name"=>_RELOAD, "url"=>self::getTodoURL('reload'), 'inactive'=>in_array('reload', $disabled) || (!$sysobject->isLockOptimistic() && $isEditable));

$a_locked = array("code"=>"locked", "name"=>_LOCKED_BY.'...', "title"=>_LOCKED_BY.' '.getConst($lockUser->name).' '._SINCE.' '.$lockInfo->lockDate, "url"=>"mailto:".$lockUser->email, 'inactive'=>in_array('locked', $disabled));

// do not show lock button if it's a new object or if the object is locked by another user
$a_lock = array("code"=>"lock", "name"=>_LOCK, "url"=>self::getTodoURL('lock'), 'inactive'=>(!$id || ($lockInfo->userId == $session->userId)) || in_array('lock', $disabled));

// do not show unlock button if the user who locked the object is not the current user
$a_unlock = array("code"=>"unlock", "name"=>_UNLOCK, "url"=>self::getTodoURL('unlock'), 'inactive'=>(!$id || $lockInfo->userId != $session->userId) || in_array('unlock', $disabled));

// do not show checkout button if it's a new object or if it's already checked out by another user
$a_checkout = array("code"=>"checkout", "name"=>_CHECKOUT, "url"=>self::getTodoURL('checkout'), 'inactive'=>(!$id || ($lockInfo->userId == $session->userId)) || in_array('checkout', $disabled));

// do not show checkin button if it's a new object or if it's already checked out by another user
$a_checkin = array("code"=>"checkin", "name"=>_CHECKIN, "url"=>self::getTodoURL('checkin'), 'inactive'=>($id && ($lockInfo->userId != $session->userId)) || in_array('checkin', $disabled));

// do not show undo checkout button if it's a new object or if it's already checked out by another user
$a_undocko = array("code"=>"undocheckout", "name"=>_CANCEL, "url"=>self::getTodoURL('undocheckout'), 'inactive'=>(!$id || ($lockInfo->userId != $session->userId)) || in_array('undocheckout', $disabled));

// do not show delete button if it's a new object or if the user doesn't have delete privileges
$a_delete = array("code"=>"delete", "name"=>_DELETE, "url"=>self::getTodoURL('delete'), 'inactive'=>((($sysobject instanceof wcmSysclass) && !($sysobject instanceof wcmBizclass)) || !$id || 0 == ($permissions & wcmPermission::P_DELETE)) || in_array('delete', $disabled));

// Build menu actions
$actions = array();

if (!$isEditable) {
    if ($sysobject->isLockOptimistic()) {
        $actions[] = $a_refresh;
        $actions[] = $a_locked;
    } elseif ($isLocked) {
        $actions[] = $a_refresh;
        $actions[] = $a_locked;
    } else {
        $actions[] = $a_checkin;
        $actions[] = $a_refresh;
        $actions[] = $a_checkout;
        $actions[] = $a_undocko;
    }
} else {
    if (0 != ($permissions & wcmPermission::P_WRITE)) {
        if ($sysobject->isLockOptimistic()) {
            $actions[] = $a_save;
            $actions[] = $a_refresh;
            $actions[] = $a_lock;
            $actions[] = $a_unlock;
        } else {
            $actions[] = $a_checkin;
            $actions[] = $a_refresh;
            $actions[] = $a_checkout;
            $actions[] = $a_undocko;
        }
    }
    $actions[] = $a_delete;
}

// Determine sysobject title
if ($sysobject->id) {
    $title = getObjectLabel($sysobject);
    
    // Cut title to 35 chars max :: needs to fit in 1024 resolution
    if (strlen($title) > 35)
        $title = substr($title, 0, 35).'...';
} else {
    $title = '&lt;'._BIZ_NEW.'&gt;';
}

// Compute system information
if ($id && ($sysobject instanceOf wcmSysObject)) {
    $sysinfo = '';
    if ($sysobject->revisionNumber > 0)
        $sysinfo .= _REVISION_NUMBER.' <em>'.$sysobject->revisionNumber.'</em>';
    $sysinfo .= '  - '._MODIFIED_ON.' <em>'.$sysobject->modifiedAt.'</em>';
} else {
    $sysinfo = '&nbsp;';
}
if ($sysobject->id) {
    $url = smartyModifiers::getInstance()->url($sysobject->getAssocArray(false));
    if ($url && $sysobject instanceOf wcmBizObject) {
        $title = '<a href="'.$url.'?preview=1" class="preview" title="'._PREVIEW.'" target="_blank"><span>'._PREVIEW.'</span></a>'.getConst($title);
    }
}
} else {
    $classLabel = 'Unexpected context';
    $title = 'The context is not an instance of wcmObject';
    $actions = array();
    $sysinfo = null;
    $workflowInfo = null;
}

self::renderAssetBar($classLabel, $title, $actions, $workflowInfo, $sysinfo);

if ($renderMainDiv)
    echo '</div>';
}

/**
 * Compute the URL to perform a todo on current page
 * Note: this will invoke the wcmActionController.triggerEvent() method
 *
 * @param string $action Action to perform
 * @param mixed $params Optional Assoc array representing parameters
 *
 * @return string URL to perfom the specific action
 */

static function getTodoURL($todo, $params = null) {
    if ($todo == 'transition') {
        $js = 'javascript:wcmActionController.triggerEvent(\''.$todo.'\','.self::getJavascriptOptions($params).');';
        //$js .= 'wcmActionController.triggerEvent(\'save\',{});';
    } else {
        $js = 'javascript:wcmActionController.triggerEvent(\''.$todo.'\','.self::getJavascriptOptions($params).');';
    }
    
    return $js;
}

/**
 * Echo a DHTML string corresponding the generic asset bar
 *
 * @param string $header  Header to display (before the title)
 * @param string $title   Title to display
 * @param array  $actions Array of menu actions (can be null)
 * @param array  $workflowInfo Optional array representing the workflow information
 * @param string $sysinfo Optional information which appears below the asset bar
 * @param bool $renderInnerHTML TRUE to only render innerHTML (false by default)
 */

static function renderAssetBar($header, $title, $actions = array(), $workflowInfo = null, $sysinfo = null) {
    // Render asset bar
    $dhtml = '';
    
    $bizobject = wcmMVC_Action::getContext();
    /*
     if (isset($bizobject->id) && $bizobject->id != 0 && ($bizobject->getClass() == 'news' || $bizobject->getClass() == 'event' || $bizobject->getClass() == 'slideshow' || $bizobject->getClass() == 'video'))
     {
     // Overview
     $dhtml .= '<div style="float:right; padding-bottom:10px;"><a style="text-decoration:underline; padding-right:15px;" href=\'javascript:openDialog("business/pages/overview.php","600","800","600","800","");\'><span><span style="background-image:none">Overview</span></span></a>';
     // Duplication
     $dhtml .= '<a style="text-decoration:underline; padding-right:15px;" href=\'javascript:openDialog("business/pages/duplication.php","400","600","400","600","");\'><span><span style="background-image:none">Duplication</span></span></a></div>';
     }
     */
    
    $dhtml .= '<div id="assetbar">';
    $dhtml .= '<h3><em>'.$header.'</em> '.getConst($title).'</h3>';
    $dhtml .= '<ul>';
    
    // yul
    // update by relaxnews
    // devteam@relaxnews.com
    
    foreach ($actions as $action) {
        $class = getArrayParameter($action, 'code', '');
        if (isset($action['child']))
            $class .= ' parent';
            
        $url = null;
        if (getArrayParameter($action, 'inactive', 0)) {
            $class .= ' inactive';
        } elseif (isset($action['url'])) {
            $url = ' href="'.$action['url'].'"';
        }
        
        if (isset($action['title'])) {
            $url .= ' title="'.$action['title'].'"';
        }
        
        $dhtml .= '<li><a class="'.$class.'"'.$url.'><span><span>'.getArrayParameter($action, 'name', '?').'</span></span></a>';
        if (isset($action['child'])) {
            $dhtml .= '<ul>';
            foreach ($action['child'] as $action) {
                $dhtml .= '<li><a class="'.getArrayParameter($action, 'code', '').'" href="'.getArrayParameter($action, 'url', '#').'"><span><span>'.getArrayParameter($action, 'name', '?').'</span></span></a></li>';
            }
            
            $dhtml .= '</ul>';
        }
        
        $dhtml .= '</li>';
        
    }
    $dhtml .= '</ul>';
    
    $dhtml .= '</div>';
    $dhtml .= '<div id="sysinfo">';
    if ($workflowInfo) {
        $dhtml .= '<div id="workflow">';
        $dhtml .= '<h4 class="'.strtolower($workflowInfo['code']).'"><strong>'.getConst($workflowInfo['name']).'</strong></h4>';
        if (isset($workflowInfo['child'])) {
            $dhtml .= '<ul>';
            foreach ($workflowInfo['child'] as $transition) {
                $dhtml .= '<li><a class="'.strtolower($transition['code']).'" href="'.$transition['url'].'">'.getConst($transition['name']).'</a></li>';
            }
            $dhtml .= '</ul>';
        }
        $dhtml .= '</div>';
    }
    $dhtml .= '<ul class="info">';
    $dhtml .= '<li class="stats">'.$sysinfo.'</li>';
    $dhtml .= '</ul>';
    
    $dhtml .= '</div>';
    
    echo $dhtml;
}

/**
 * Echo a DHTML string corresponding to current working site
 * and list of available web sites
 */

static function renderWorkingSite() {
    if (!isset($_SESSION['wcmGUI_renderWorkingSite'])) {
        $session = wcmSession::getInstance();
        $config = wcmConfig::getInstance();
        $site = $session->getSite();
        
        $html = '<strong>'._WORKING_IN.'</strong>';
        $html .= '<a href="'.$config['wcm.backOffice.url'].'index.php?_wcmAction=business/site&amp;id=';
        $html .= $site->id.'" class="edit" title="'._EDIT.'"><span>'._EDIT.'</span></a>';
        $url = $site->url;//smartyModifiers::getInstance()->url($site->getAssocArray(false));
        $html .= '<a href="'.$url.'" class="preview" title="'._PREVIEW.'" target="web"><span>'._PREVIEW.'</span></a>';
        $html .= textH8($site->title);
        
        // Display available sites
        $htmlList = null;
        $enumSite = new site();
        if ($enumSite->beginEnum()) {
            while ($enumSite->nextEnum()) {
                if (($session->isAllowed($enumSite, wcmPermission::P_READ)) && ($enumSite->id != $site->id)) {
                    $htmlList .= '<li><a href="'.$_SERVER['PHP_SELF'].'?_wcmAction=home&_wcmSiteId='.$enumSite->id.'">'.$enumSite->title.'</a></li>';
                }
            }
            $enumSite->endEnum();
        }
        unset($enumSite);
        
        if ($htmlList) {
            $html .= '<ul id="siteSelector">';
            $html .= '<li><a href="" title="'._CHANGE_SITE.'" onclick="openmodal(\''._CHANGE_SITE.'\'); modalPopup(\'changesite\',\'list\', \'\'); return false;" class="title"><span><span>'._CHANGE.'</span></span></a></li>';
            $html .= '</ul>';
        }
        $_SESSION['wcmGUI_renderWorkingSite'] = $html;
    }
    echo $_SESSION['wcmGUI_renderWorkingSite'];
}

/**
 * Enter description here...
 *
 * @param string $id
 * @param string $label
 * @param bool   $expanded
 * @param string $info  optionnal extra html
 */

static function openCollapsablePane($label, $expanded = true, $info = null) {
    echo '<div class="collapsable">';
    echo '<div class="action-header">';
    echo '<h3 onclick="$(this).up().siblings().shift().toggle(); if ($(this).className == \'collapsed\') $(this).removeClassName(\'collapsed\'); else $(this).addClassName(\'collapsed\');">';
    echo $label.'</h3>';
    echo $info;
    echo '</div>';
    echo '<div';
    if (!$expanded)
        echo ' style="display:none;"';
    echo '>';
}

static function closeCollapsablePane() {
    echo '</div>';
    echo '</div>';
}

/**
 * Render the html code used to open a dialog (an anchor tag)
 *
 * @param string $title         Text of the dialog button
 * @param string $dialog        Dialog name
 * @param mixed  $parameters    Query string or associative array
 * @param int    $width         Dialog width (default 740)
 * @param int    $height        Dialog height (default 540)
 * @param string $className     Optional class for the dialog button
 */

static function renderHtmlDialogButton($title, $dialog, $parameters = null, $width = 740, $height = 540, $className = null) {
    if ($className)
        $className = 'class="'.$className.'"';
    echo '<a'.$className.' href="'.wcmDialogUrl($dialog, $parameters, $width, $height).'">'.$title.'</a>';
}


static function renderModalButton($label, $command, $kind, $id) {
    echo '<a href="" onclick="openmodal(\''.$label.'\'); modalPopup(\''.$command.'\',\''.$kind.'\', \''.$id.'\'); return false;" class="title"><span>'.$label.'</span></a>';
}
}
