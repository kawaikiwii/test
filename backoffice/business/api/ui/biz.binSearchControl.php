<?php
/**
 * Project:     WCM
 * File:        biz.binSearchControl.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Saved-search bin control
 */
class binSearchControl
{
    protected $project;
    protected $session;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->session = wcmSession::getInstance();
        $this->project = wcmProject::getInstance();
    }

    /**
     * Loads the contents of the bin.
     */
    public function initialLoad($argSelected = false)
    {
        echo "<h4>"._BIZ_SELECTED_BIN."</h4>";
        echo "<select id=\"selectBin\" name=\"bins\" onChange=\"manageBin('renderBinData','','','', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value,'binData', '')\">";

        // If there are no bins, create the unremovable default bin
        $bin = new wcmBin($this->project);
        $where = "userId = ".$this->session->userId;
        if (!$bin->beginEnum($where, "name"))
        {
            $bin->name = getConst(_BIZ_MY_BIN);
            $bin->description = getConst(_BIZ_MY_BIN);
            $bin->dashboard = 0;
            $bin->userId = $this->session->userId;
            $bin->save();
        }

        // List of bins
        $binId = 0;
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum($where, "name"))
        {
            $cpt = 0;
            while ($bin->nextEnum())
            {
                if ($cpt == 0)
                    $binId = $bin->id;
                    
                if ($bin->name == $argSelected)
                {
                    $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo "<option value=\"".$bin->id."\" ".$selected.">".$bin->name."</option>";
                $cpt++;
            }
        }
        echo "</select>";

        // Bin contents
        echo "<div class=\"scroll\" id=\"binData\">";
        if ($binId != 0)
            $this->renderBinData($binId);
        echo "</div>";
        
        // Bin operations
        echo "<ul class=\"menu\">";
        echo "<li><a href=\"#\" onClick=\"openmodal('" . _BIZ_CREATE_BIN . "'); modalPopup('bin','createEmpty', ''); return false;\" title=\""._BIZ_SAVE."\">"._BIZ_CREATE."</a> "._BIZ_EMPTY_BIN."</li>";
        echo "<li><a href=\"#\" onClick=\"openmodal('" . _BIZ_EDIT . "'); modalPopup('bin','updateBin', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value); return false;\" title=\""._BIZ_EDIT."\">"._BIZ_EDIT."</a> "._BIZ_SELECTED_BIN."</li>";
        echo "<li><a href=\"#\" onClick=\"manageBin('display', $('selectBin').options[$('selectBin').selectedIndex].innerHTML, '', '', $('selectBin').options[$('selectBin').selectedIndex].value, 'bins', '')\" title=\"\">"._BIZ_LIST_DISPLAY."</a> "._BIZ_SELECTED_BIN."</li>";
        echo "<li><a href=\"#\" onClick=\"manageBin('remove', $('selectBin').options[$('selectBin').selectedIndex].innerHTML, '', '', $('selectBin').options[$('selectBin').selectedIndex].value, 'bins', '')\" title=\"\">"._BIZ_REMOVE."</a> "._BIZ_SELECTED_BIN."</li>";
        echo "<li><a href=\"#\" onClick=\"manageBin('clear', '', '', '', '', 'bins', '')\" title=\"\">"._BIZ_REMOVE."</a> "._BIZ_ALL_BINS."</li>";
        echo "<li><a href=\"#\" onClick=\"openmodal('" . _BIZ_EXPORT . "'); modalPopup('bin','exportBin', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value); return false;\" title=\""._BIZ_EXPORT."\">"._BIZ_EXPORT."</a> "._BIZ_SELECTED_BIN."</li>";
        echo "<li><a href=\"#\" onClick=\"openmodal('" . _WEB_PRINT . "'); modalPopup('bin','printBin', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value); return false;\" title=\""._WEB_PRINT."\">"._WEB_PRINT."</a> "._BIZ_SELECTED_BIN."</li>";
        echo "</ul>";
    }

    /**
     * Renders the content of a given bin.
     *
     * @param int $id The bin's ID
     */
    function renderBinData($id)
    {
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id))
        {
            while ($bin->nextEnum())
            {
                $contentArray = explode('/', $bin->content);
                if ($contentArray)
                {
                    echo "<ul>";
                    foreach ($contentArray as $content)
                    {
                        if ($content)
                        {
                            list($objectClass, $objectId) = explode('_', $content, 2);
                            if ($objectClass && $objectId)
                            {
                                $bizobject = new $objectClass($this->project);
                                if ($bizobject->refresh($objectId))
                                {
                                    echo "<li>";
                                    echo "<a title='"._REMOVE."' href=\"#\" onClick=\"manageBin('removeFromSelectedBin', '', '', '$content', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value, 'binData', '')\" class=\"remove\"><span>Remove</span></a> <a href=\"#\" onClick=\"onSelectItem('business/".$objectClass."',".$objectId.", '')\" class=\"view\">".getObjectLabel($bizobject)."</a>";
                                    echo "</li>";
                                }
                            }
                        }
                    }
                    echo "</ul>";
                }
            }
        }
    }

    /**
     * Renders the content of a given bin for search display.
     *
     * @param int $id The bin's ID
     */
    function renderBinDataForSearch($id)
    {
    	$value = "";
                    
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id))
        {
            while ($bin->nextEnum())
            {
                $contentArray = explode('/', $bin->content);
                if ($contentArray)
                {
                    $ini = 0;
                    foreach ($contentArray as $content)
                    {
                        if ($content)
                        {
                            list($objectClass, $objectId) = explode('_', $content, 2);
                            if ($objectClass && $objectId)
                            {
	                            if ($ini == 0)
	                            	$value .= "(classname:".$objectClass." and objectId:".$objectId.")";
	                            else 
	                            	$value .= " OR (classname:".$objectClass." and objectId:".$objectId.")";
	                            
	                            	$ini++;
                            }
                        }
                    }
                }
            }
        }
        return $value;
    }
    /**
     * Saves a given bin.
     *
     * @param string $name        The bin's name
     * @param string $description The bin's description
     * @param string $content     The bin's content
     * @param bool   $dashboard   Whether to add the bin to the dashboard (default is false)
     * @param int    $id          The bin's ID (default is 0, which means we are saving a new bin)
     */
    function saveBin($name, $description, $content, $dashboard = false, $id = 0)
    {
        $bin = new wcmBin($this->project);
        if ($id)
            $bin->refresh($id);

        $bin->name = $name;
        $bin->description = $description;
        $bin->userId = $this->session->userId;
        $bin->content = $content;
        $bin->dashboard = ($dashboard == 'true' ? 1 : 0);

        $bin->save();
    }

    /**
     * Removes a given bin.
     *
     * @param int $id The bin ID
     */
    function removeBin($id)
    {
        $bin = new wcmBin($this->project);
        $bin->id = $id;
        $bin->removeOne();
    }

    /**
     * Removes all bins.
     */
    function clearBins()
    {
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum())
        {
            while ($bin->nextEnum())
                $bin->removeOne();
        }
    }

    /**
     * Adds a given item to the selected bin.
     *
     * @param int    $id      The bin ID
     * @param string $content The item content to add
     */
    function addToSelectedBin($id, $content)
    {
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id))
        {
            while ($bin->nextEnum())
            {
                $contentArray = explode('/', $bin->content);
                $key = array_search($content, $contentArray);
                if ($key === false)
                    $contentArray[] = $content;

                $bin->content = implode('/', $contentArray);
                $bin->save();
            }
        }
    }

    /**
     * Removes a given item from the selected bin.
     *
     * @param int    $id      The bin ID
     * @param string $content The item content to remove
     */
    function removeFromSelectedBin($id, $content)
    {
        $bin = new wcmBin($this->project);
        if ($bin->beginEnum("id=".$id))
        {
            while ($bin->nextEnum())
                $contentArray = explode('/', $bin->content);
        }
        $key = array_search($content, $contentArray);
        if ($key !== false)
            unset($contentArray[$key]);

        $bin->content = implode('/', $contentArray);
        $bin->save();
    }
}
?>