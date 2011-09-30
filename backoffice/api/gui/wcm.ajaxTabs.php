<?php
/**
 * Project:     WCM
 * File:        wcm.ajaxTabs.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class helps to manage dhtml tabs loaded on demand
 * through ajax requests
 */
class wcmAjaxTabs
{
    private static $wait = null;

    private $guid;
    private $useCookies;
    private $tabs;
    private $ajaxURLs;

    /**
     * Constructor
     *
     * @param string $guid Unique id
     * @param bool   $useCookies TRUE to persist selected tab through cookie
     * @param array  $tabs Optional array of assoc array representing each tab
     *                     Excepted keys are 'id, caption, selected, content, ajaxURL'
     *
     */
    public function __construct($guid, $useCookies = 0, $tabs = null)
    {
        $this->guid = 'wcmTabs_'.$guid;
        $this->useCookies = ($useCookies) ? '1' : '0';
        $this->tabs = (is_array($tabs)) ? $tabs : array();
        $this->ajaxURLs = array();
    }

    /**
     * Add a new tab
     *
     * @param string    $id Tab id (used for persistence in cookie
     * @param string    $caption Tab caption
     * @param bool      $selected TRUE to mark this tab as selected
     * @param string    $content Inner content for tab (or null)
     * @param string    $ajaxURL Optional URL used to refresh content (or null)
     */
    public function addTab($id, $caption, $selected = false, $content = null, $ajaxURL = null)
    {
        // Add piece of HTML to render when tab is loading...
        if (!self::$wait) self::$wait = '<div class="wait">' . _LOADING . '</div>';

        $this->tabs[] = array('id' => 'wcmTab_'.$id,
                              'caption' => $caption,
                              'selected' => $selected,
                              'content' => ($content != null) ? $content : (($ajaxURL != null) ? self::$wait : null),
                              'ajaxURL' => $ajaxURL);
    }

    /**
     * Render the HTML code for the tabs
     */
    public function render($overwriteCookie = false)
    {
        // Render html
        echo "<div id='" . $this->guid . "' class='tab-page'>";
        foreach($this->tabs as $tab)
        {
            echo "<div class='tab-page' id='" . $this->guid . '_' . $tab['id'] . "'>";
            echo "<h2 class='tab'>" . $tab['caption'] . "</h2>";
            echo $tab['content'];
            echo "</div>";
        }
        echo "</div>";

        // Prepare javascript content
        $ajaxArray = null;
        $selected = $index = 0;
        foreach($this->tabs as $tab)
        {
            $url = getArrayParameter($tab, 'ajaxURL', null);
            if (!$ajaxArray)
            {
                $ajaxArray = ($url) ? "['" . str_replace("'", "\'", $url) . "'" : '[ null';
            }
            else
            {
                $ajaxArray .= ($url) ? ",'" . str_replace("'", "\'", $url) . "'" : ', null';
            }

            if (getArrayParameter($tab, 'selected', false))
            {
                $selected = $index;
            }
            $index++;
        }
        if ($ajaxArray)
            $ajaxArray .= ']';
        else
            $ajaxArray = 'null';


        // Render javascript
        echo "<script type='text/javascript' defer='defer'>//<![CDATA[\n";
        echo "   var tabPane_" . $this->guid . " = new WebFXTabPane($('" . $this->guid . "'), ";
        echo $this->useCookies . ", " . $ajaxArray . ", " . $selected . ", " . ($overwriteCookie?'true':'false') . ");";
        foreach($this->tabs as $tab)
        {
            echo "tabPane_" . $this->guid . ".addTabPage($('" . $this->guid . '_' . $tab['id'] . "')); ";
        }
        echo "//]]>\n</script>";
    }
}
