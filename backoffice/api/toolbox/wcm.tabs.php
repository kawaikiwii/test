<?php
/**
 * PnW Tabs
 * Uses "Tab Pane" created by Erik Arvidsson
 *
 */
class wcmTabs
{
    private $useCookies = false;
    private $paneId = 'tabs';

    /**
     * Constructor
     *
     * Includes files needed for displaying tabs and sets cookie options
     * => Invoke this method in the <HEAD> tag
     *
     * @param int useCookies When set to 1 cookie will hold last used tab
     *
     */
    public function __construct($useCookies = 0)
    {
        $this->useCookies = $useCookies;
    }

    /**
     * Renders start of a tab pane
     *
     * @param string The Tab Pane Name
     *
     */
    public function startPane($id)
    {
        $this->paneId = $id;
        echo "<div id='$id' class='tab-page'>";
        echo "<script type='text/javascript'>\n";
        echo "   var tabPane_$id = new WebFXTabPane( document.getElementById('$id'), ";
        echo ($this->useCookies) ? '1);' : '0);';
        echo "</script>\n";
    }

    /**
     * Renders end of a tab pane
     *
     */
    public function endPane()
    {
        echo "</div>";
    }

    /**
     * Renders a new tab page
     *
     * @param tabText The tab title
     * @param tabId   Id of then Pane where the tab should be created
     *
     */
    public function startTab( $tabText, $tabId, $selected=false)
    {
        echo "<div class=\"tab-page\" id=\"".$tabId."\">";
        echo "<h2 class=\"tab\">".$tabText."</h2>";
        echo "<script type=\"text/javascript\">\n";
        echo "  var tp = tabPane_".$this->paneId.".addTabPage( document.getElementById( \"".$tabId."\" ) );";
        if ($selected)
        echo "  tabPane_".$this->paneId.".setSelectedIndex(tp.index);";
        echo "</script>";
    }

    /**
     * Renders the end of a tab page
     *
     */
    public function endTab()
    {
        echo "</div>";
    }
}