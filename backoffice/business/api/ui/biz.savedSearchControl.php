<?php

/**
 * Project:     WCM
 * File:        biz.savedSearchControl.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     3.2
 *
 */


class savedSearchControl
{
    protected $project;
    protected $session;

    public function __construct()
    {
        $this->session = wcmSession::getInstance();
        $this->project = wcmProject::getInstance();
    }
    
    
    function getSavedSearches($action)
    {
        $savedSearches = array();
        $search = new wcmSavedSearch();
        $where = "userId = ".$this->session->userId;

        if ($search->beginEnum($where, "name"))
        {
            while ($search->nextEnum())
            {
                $savedSearches[$search->id] = array('name' => $search->name,
                                                    'queryString' => $search->queryString,
                                                    'description' => $search->description);
            }
        }
        return $savedSearches;
    }

    /**
     *
     * Function that lists the current user's saved searches
     *
     */
    public function initialLoad($action)
    {
        $_SESSION['savedSearchAction'] = $action;

        echo '<h4>'._BIZ_SEARCHES.'</h4>';
        echo "<select name=\"selectSearches\" id=\"selectSearches\" onChange=\"manageSaveSearch(this.value, '', '', '', '', '', 'searches', '');\">";

        echo '<option value="showSavedSearches" ';
        if ($action == 'showSavedSearches') echo 'SELECTED';
        echo '>'._BIZ_MY_SAVED_SEARCHES.'</option>';

        echo '<option value="showPublicSavedSearches" ';
        if ($action == 'showPublicSavedSearches') echo 'SELECTED';
        echo '>'._SHARED_SAVED_SEARCHES.'</option>';
        
        echo '<option value="showHistory"';
        if ($action == 'showHistory') echo 'SELECTED';
        echo '>'._BIZ_SEARCH_HISTORY.'</option>';

        echo '</select>';

        echo '<div class="scroll">';
            $this->showSavedSearches($action);
            $this->showHistory($action);
            $this->showPublicSavedSearches($action);
        echo '</div>';

        echo '<ul class="menu">';
        echo '<li><a href="" onclick="_wcmSaveSearch(\'' . _BIZ_SAVE_SEARCH . '\');return false;">'._BIZ_SAVE.'</a> '._BIZ_CURRENT_SEARCH.'</li>';
        echo '</ul>';
    }

    public function showSavedSearches($action)
    {
        $search = new wcmSavedSearch();
        $where = "userId = ".$this->session->userId;

        $config = wcmConfig::getInstance();
        $session = wcmSession::getInstance(); 
          
        if ($search->beginEnum($where, "name"))
        {
            echo '<ul id="savedSearches" '.(($action != "showSavedSearches")?'style="display:none;"':'').'>';
            while ($search->nextEnum())
            {
            	if(in_array($session->userId,($search->showui == "")?array():json_decode($search->showui)))
            	{
	            	$name = htmlspecialchars($search->name);
	
	                if ($search->queryString)
	                    $title = htmlspecialchars($search->queryString);
	                else
	                    $title = '('._BIZ_ALL.')';
	                if ($search->description)
	                    $title .= ' - ' . htmlspecialchars($search->description);
	
	                $onClickRemove =
	                    "manageSaveSearch('remove', '', '', '', '', ".$search->id.", 'searches', '')";
	                $onClickSearch =
	                    "window.location = '".$config['wcm.backOffice.url']."index.php?".$search->url."';";
	
	                echo '<li><a href="#" title="' . _REMOVE . '" class="remove" onClick="'.$onClickRemove.'">';
	                echo '<span>'._BIZ_REMOVE.'</span></a>';
	                echo '<a href="#" class="view" onClick="'.$onClickSearch.'" ';
	                echo 'title="'.addslashes($title).'">'.$name.'</a></li>';
            	}
            }
            echo "</ul>";
        }
    }
 	public function showPublicSavedSearches($action)
    {
    	$search = new wcmSavedSearch();
        $where = "userId != ".$this->session->userId." AND shared='1' ";

        $config = wcmConfig::getInstance();
        $session = wcmSession::getInstance(); 
          
        if ($search->beginEnum($where, "name"))
        {
            echo '<ul id="savedSearches" '.(($action != "showPublicSavedSearches")?'style="display:none;"':'').'>';
            while ($search->nextEnum())
            {
            	if(in_array($session->userId,($search->showui == "")?array():json_decode($search->showui)))
            	{
	            	$name = htmlspecialchars($search->name);
	
	                if ($search->queryString)
	                    $title = htmlspecialchars($search->queryString);
	                else
	                    $title = '('._BIZ_ALL.')';
	                if ($search->description)
	                    $title .= ' - ' . htmlspecialchars($search->description);
	
	                $onClickRemove =
	                    "manageSaveSearch('remove', '', '', '', '', ".$search->id.", 'searches', '')";
	                $onClickSearch =
	                    "window.location = '".$config['wcm.backOffice.url']."index.php?".$search->url."';";
	
	                echo '<li><a href="#" title="' . _REMOVE . '" class="remove" onClick="'.$onClickRemove.'">';
	                echo '<span>'._BIZ_REMOVE.'</span></a>';
	                echo '<a href="#" class="view" onClick="'.$onClickSearch.'" ';
	                echo 'title="'.addslashes($title).'">'.$name.'</a></li>';
            	}
            }
            echo "</ul>";
        }
    }
    public function showHistory($action)
    {
        if (isset($_SESSION['searchHistory']) && $_SESSION['searchHistory'])
        {
            echo '<ul '.(($action != "showHistory")?'style="display:none;"':'').'>';
            foreach ($_SESSION['searchHistory'] as $query => $date)
            {
                if ($query)
                    $text = htmlspecialchars($query);
                else
                    $text = '('._BIZ_ALL.')';

                echo "<li>";
                ?>
                <a href="#" onClick="javascript: document.getElementById('search_query').value = '<?php echo addslashes(str_replace('"' , "&quot;" , $query));?>'; launchSearch();" title="<?php echo $date;?>" class="view"><?php echo $text;?></a>
                <?php
                echo "</li>";
            }
            echo "</ul>";
        }
    }

    /**
     * Function that helps to save a search
     *
     * @param string      $name        Search name
     * @param string      $description Search description
     * @param string      $queryString Lucene-syntax query string
     * @param string|null $url         Optional Search URL
     * @param bool        $dashboard   Whether to add search to dashboard
     */
    function saveSearch($name, $description, $queryString, $url = null, $dashboard = false, $shared = false)
    {
        $savedSearch = new wcmSavedSearch();
        $savedSearch->name = $name;
        $savedSearch->description = $description;
        $savedSearch->userId = $this->session->userId;
        $savedSearch->queryString = $queryString;
        $savedSearch->url = $url;
        $savedSearch->dashboard = ($dashboard == "false")?0:1;
        $savedSearch->shared = ($shared == "false")?0:1;
        
        $session = wcmSession::getInstance();
        $savedSearch->showui = json_encode(array($session->userId));
                
        return $savedSearch->save();
    }

    /**
     * Function that helps deleting a saved search
     *
     * @param $savedSearchId int  //id de la recherche à supprimer
     */
    function removeSearch($savedSearchId)
    {
        $savedSearch = new wcmSavedSearch($savedSearchId);
        return $savedSearch->delete();
    }
    
}