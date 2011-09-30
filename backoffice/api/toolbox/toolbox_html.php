<?php
/**
 * Project:     WCM
 * File:        wcm.toolbox_html.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

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
function renderHtmlDialogButton($title, $dialog, $parameters = null, $width = 740, $height = 540, $className = null)
{
    if ($className) $className = 'class="' . $className . '"';
    echo '<a' . $className . ' href="' . wcmDialogUrl($dialog, $parameters, $width, $height) . '">' . $title . '</a>';
}

/**
 * Render HTML <option> list
 *
 * @param $options        associative array (value => label) for each option
 * @param $currentOption  current option value
 * @param boolean $echo TRUE to echo result (rendering)
 */
function renderHtmlOptions($options, $currentOption = null, $echo = true)
{
    if (!is_array($options)) return null;

    $result = '';
    foreach($options as $value => $caption)
    {
        $result .= '<option value="' . textH8($value) . '" style="cursor: pointer;"';
        if ($currentOption === $value) $result .= ' selected="selected"';

        // $caption is an array used to deactivate options from select box
        // (ex : $caption[0] = valeur, $caption[1] = disabled)
        if (is_array($caption))
        {
            $result .= ' '.$caption[1];
            if ($caption == null)
                $result .= '>' . textH8($value) . '</option>' . "\r\n";
            else
                $result .= '>' . textH8($caption[0]) . '</option>' . "\r\n";
        }
        else
        {
            if ($caption == null)
                $result .= '>' . textH8($value) . '</option>' . "\r\n";
            else
                $result .= '>' . textH8($caption) . '</option>' . "\r\n";
        }
    }

    if ($echo) echo $result;
    return $result;
}

/**
 * get Array <option> list
 *
 * @param $options        associative array (value => label) for each option
 * @param $currentOption  current option value
 */
function getArrayOptions($options, $currentOption = null)
{
    $arrayResult = array();

    if ($options)
    {
        foreach($options as $value => $caption)
        {
            $result = '';
            $result .= "<option value=\"" . textH8($value) . "\" style=\"cursor: pointer;\"";
            if ($currentOption === $value) $result .= " selected";
            /* $caption est un array si l'on souhaite désactiver certaines options du menu déroulant (ex : $caption[0] = valeur, $caption[1] = disabled)*/
            if (is_array($caption))
            {
                $result .= " ".$caption[1];
                if ($caption == null)
                    $result .= ">" . textH8($value) . "</option>";
                else
                    $result .= ">" . textH8($caption[0]) . "</option>";
            }
            else
            {
                if ($caption == null)
                    $result .= ">" . textH8($value) . "</option>";
                else
                    $result .= ">" . textH8($caption) . "</option>";
            }
            $arrayResult[] = $result;
        }
    }

    return $arrayResult;
}

/**
 * get Array <input type="checkbox"> list
 *
 * @param $options        associative array (value => label) for each option
 * @param $currentOption  current option value
 */
function getArrayCheckbox($options, $currentOption = null)
{
    $arrayResult = array();
    foreach($options as $value => $caption)
    {
        $result = '';
        $result .= "<input type='checkbox' value='" . textH8($value) . "' style='cursor: pointer;'";
        if ($currentOption === $value) $result .= " checked";
        /* $caption est un array si l'on souhaite désactiver certaines options du menu déroulant (ex : $caption[0] = valeur, $caption[1] = disabled)*/
        if (is_array($caption))
        {
            $result .= " ".$caption[1];
            if ($caption == null)
                $result .= ">" . textH8($value) . "</option>";
            else
                $result .= ">" . textH8($caption[0]) . "</option>";
        }
        else
        {
            if ($caption == null)
                $result .= ">" . textH8($value) . "</option>";
            else
                $result .= ">" . textH8($caption) . "</option>";
        }
        $arrayResult[] = $result;
    }

    return $arrayResult;
}

/**
 * Render HTML Connector list
 *
 * @param $currentOption  current option value
 *
 */
function renderHtmlConnectorList($currentOption = null)
{
    $project        = wcmProject::getInstance();
    $dataLayer      = new wcmDatalayer($project);
    $connectors     = $dataLayer->getConnectors();
    $connectorArray = array();

    foreach ($connectors as $connector)
    {
        $connectorArray[$connector->id] = $connector->name;
    }

    return renderHtmlOptions($connectorArray, $currentOption, false);
}


/**
 * Get table names
 *
 * @param   string  $currentOption  selected option
 * @param   int     $connectorId    default is null - generate list according to correct connecter
 * @param   string  $prefix         default is biz_ - used to select tables
 *
 * @return  array   $tables         business tables names
 */
function renderBizTableList($currentOption = null, $connectorId = null, $prefix = 'biz_')
{
    if ($connectorId)
    {
        $project      = wcmProject::getInstance();
        $connector    = new wcmConnector($project, $connectorId);
        $schema       = $connector->getSchema();
        if ($schema)
        {
            $dbTables     = $schema->getTables();
            $systemTables = wcmDatalayer::getSystemDBTables();

            foreach($dbTables as $dbTable)
            {
                $name = $dbTable->getName();
                if (!(strpos($name, $prefix) === 0) || !in_array($name, $systemTables))
                    $tables[$name] = $name;
            }
            return renderHtmlOptions($tables, $currentOption, false);
        }
    }
}

/**
 * Get table names
 *
 * @param   string  $currentOption  selected option
 * @param   int     $connectorId    default is null - generate list according to correct connecter
 * @param   string  $prefix         default is biz_ - used to select tables
 *
 * @return  array   $tables         business tables names
 */
function renderSysTableList($currentOption = null, $connectorId = null, $prefix = 'sys_')
{
    $config = wcmConfig::getInstance();

    if ($connectorId)
    {
        $project      = wcmProject::getInstance();
        $connector    = new wcmConnector($project, $connectorId);
        $connector->connectionString = $config['wcm.systemDB.connectionString'];

        $schema       = $connector->getSchema();
        if ($schema)
        {
            $dbTables     = $schema->getTables();
            $systemTables = wcmDatalayer::getSystemDBTables();

            foreach($dbTables as $dbTable)
            {
                $name = $dbTable->getName();
                if (!(strpos($name, $prefix) === 0) || !in_array($name, $systemTables))
                    $tables[$name] = $name;
            }
            return renderHtmlOptions($tables, $currentOption, false);
        }
    }
}

/**
 * Render HTML Workflow states list
 *
 * @param $currentOption  current option value
 *
 */
function renderWorkflowStates($currentOption = null)
{
    $project         = wcmProject::getInstance();
    $workflowManager = new wcmWorkflowManager($project);
    $workflowStates  = $workflowManager->getWorkflowStates();
    $workflowStatesArray  = array();
    foreach ($workflowStates as $workflowState)
    {
        $workflowStatesArray[$workflowState->code] = $workflowState->name;
    }
    return renderHtmlOptions($workflowStatesArray, $currentOption, false);
}

/**
 * Render HTML Workflow script list
 *
 * @param $currentOption  current option value
 *
 */
function renderWorkflowScripts($currentOption = null)
{
    $project         = wcmProject::getInstance();
    $workflowManager = new wcmWorkflowManager($project);
    $workflowScripts  = $workflowManager->getWorkflowScripts();
    $workflowScriptsArray  = array();

    foreach ($workflowScripts as $workflowScript)
    {
        $workflowScriptsArray[$workflowScript] = $workflowScript;
    }

    return renderHtmlOptions($workflowScriptsArray, $currentOption, false);
}

/**
 * Render HTML Workflow script list
 *
 * @param $currentOption  current option value
 *
 */
function renderWorkflows($currentOption = null)
{
    $project         = wcmProject::getInstance();
    $workflowManager = new wcmWorkflowManager($project);
    $workflows       = $workflowManager->getWorkflows();
    $workflowsArray  = array();

    foreach ($workflows as $workflow)
    {
        $workflowsArray[$workflow->id] = $workflow->name;
    }
    return renderHtmlOptions($workflowsArray, $currentOption, false);
}

/**
 * Render HTML boolean (Checkboxes -> true false)
 *
 * @param $name     Name of field
 * @param $form     Name of form (default is frmEdit as it's often used)
 * @param $value    Value passed (default is false - 0)
 * @param $locked   True to lock control (default is false)
 *
 * Echoes result in HTML
 *
 */
function renderHtmlBoolean($name, $form = 'frmEdit', $value = 0, $locked = false)
{
    $result = '';
    ?>
    <script type="text/javascript">
    function changeHtmlBoolean(form, field)
    {
        if (eval("document.forms['" + form + "'].box_" + field + ".checked"))
            eval("document.forms['" + form + "']." + field + ".value=1")
        else
            eval("document.forms['" + form + "']." + field + ".value=0")
    }
    </script>
    <?php
    $result .= '<input type="checkbox" value="' . textH8($value) . '" name="box_' . $name . '"';
    if ($value)
        $result .= ' checked="checked"';
    if ($locked)
        $result .= ' disabled="disabled"';
    $result .= '" onclick="changeHtmlBoolean(\'' . $form . '\', \'' . $name . '\');" />';

    $result .= '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . (($value) ? '1' : '0') . '" />';

    return $result;
}

/**
 * Render HTML calendar
 *
 * @param $id       Id (and name) or input control
 * @param $value    Starting date (a parsable string)
 * @param $mode     Render mode (default "date", possible mode is "datetime")
 * @param $locked   True to lock control (default is false)
 * @param $callback JS callback (or null for default behaviour)
 * @param $hidden
 * @param $lang     Lang to be used
 * @param $css      Css to be used
 * @param $dateFormat (php date format)
 * @param $timeFormat (php time format)
 * @param $flat     bool true if calendar is to be flat (default=false)
 *
 * @retun the HTML/javascript calendar
 *
 */

function renderHTMLCalendar($id, $value = null, $mode = 'date', $locked=false, $callback = null, $hidden = false, $lang = "en", $css = 'calendar-mos', $dateFormat = 'Y-m-d', $timeFormat = 'H:i', $flat = false)
{
    $calendar = new DHTML_Calendar();
    $result = $calendar->renderHTMLCalendar($id, $value, $mode, $locked, $callback, $hidden, $lang, $css, $dateFormat, $timeFormat, $flat);

    return $result;
}

/**
 * Render Ajax Items (set headers for XML and return valid XML response)
 *
 * @param array $items Associative array of items to render (key is id, value is innerHTML)
 */
function renderAjaxItems($items = array())
{
    // No browser cache
    header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );

    // Xml output
    header( 'Content-Type: text/xml' );
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo "\n";

    // Write ajax response
    echo '<ajax-response>';
    echo "\n";
    foreach($items as $id => $content)
    {
        echo '<response type="item" id="'.$id.'"><![CDATA['.$content.']]></response>';
        echo "\n";
    }
    echo '</ajax-response>';
}

/**
 * Render HTML Editor (using Tiny MCE)
 *
 * @param   string  $id         Id of textarea to create
 * @param   string  $content    Text content (default null)
 * @param   bool    $locked     True to lock editor (default false)
 * @param   array   $params     Multiple parameters that can be used to modify display of textarea/editor
 *                              Defaults are :
 *                              'width'       =>400              width of editor
 *                              'height'      =>120              height of editor
 *                              'textArClass' =>'modAreaTinyMCE' class of textarea
 *
 * @return  string  $result     XHTML to print the editor
 */
function renderHtmlEditor($id, $content = null, $locked = false, $params = null)
{
    // Set default values for parameters
    if (!is_array($params)) $params = array();
    if (!isset($params['width'])) $params['width'] = 400;
    if (!isset($params['height'])) $params['height'] = 120;

    $session = wcmSession::getInstance();
    $result = null;
    //@TODO: Fix tinyMCE bug with AJAX tabs...
    if (true || $locked)
    {
        // we strip HTML tags for better rendering in the text area. =
        $result .= '<div name="' . $id . '" id="' . $id . '" class="modAreaTinyMCE" style="overflow: auto;">' . $content . '</div>';
    }
    else
    {
        // initialise TinyMCE script
        $result .= '<script language="javascript" type="text/javascript">';
        $result .= 'tinyMCE.init({';
        $result .= '  auto_reset_designmode : true';
        $result .= ', elements : "' . $id . '"';
        $result .= ', entities : "160,nbsp"';
        $result .= ', entity_encoding : "named"';
        $result .= ', language : "' . $session->getLanguage() . '"';
        $result .= ', mode : "exact"';
        $result .= ', theme : "advanced"';
        $result .= ', theme_advanced_buttons1 : "cut,copy,paste,undo,redo,separator,bold,italic,underline,separator,link,anchor,code"';
        $result .= ', theme_advanced_buttons2 : ""';
        $result .= ', theme_advanced_buttons3 : ""';
        $result .= ', theme_advanced_layout_manager : "SimpleLayout"';
        $result .= ', theme_advanced_path : false';
        $result .= ', theme_advanced_resize_horizontal : false';
        $result .= ', theme_advanced_resizing : true';
        $result .= ', theme_advanced_statusbar_location : "bottom"';
        $result .= ', theme_advanced_toolbar_location : "top"';
        $result .= ', theme_advanced_toolbar_align : "left"';
        $result .= ', width : "' . $params['width'] . '"';
        $result .= ', height : "' . $params['height'] . '"';
        $result .= '});';
        $result .= '</script>';
        $result .= '<textarea name="' . $id . '" id="' . $id . '" class="mceEditor">' . textH8($content) . '</textarea>';
    }

    return $result;

}

/**
 * Render a dhtml text field with Auto Complete options
 *
 * @param int    $fieldId
 * @param string $text
 *
 * @return string The dhtml representation of the text field
 *
 */
function renderHtmlTextField($fieldId, $input = null)
{
    // Get current project
    $project = wcmProject::getInstance();

    // Include Projax class and intialize it
    require_once(WCM_DIR . '/includes/projax/projax.php');

    $textField = new Projax();

    $task = getArrayParameter($_REQUEST, 'task', 'view');

    if ($task != 'ajax')
    {
        $result .= $input . '<br />';
        $result .= $textField->text_field_with_auto_complete($fieldId, null, array('url' => WCM_DIR . '/business/ajax/biz.searchNFinder.php', null));
        return $result;
    }
}

/**
 *
 * The DHTML_Calendar class is a php wrapper to easily render a DHTML Calendar
 * For further details on the DHTML Calendar code, see :
 * http://www.dynarch.com/projects/calendar/
 *
 */
class DHTML_Calendar
{
    /**
     * Those options will be used while using the setup method of the Calendar (JS object)
     */
    public $calendar_options;

    /**
     * array DateFormats :
     * Key is PHP format
     * Value is calendar.js format
     * Only values with PHP equivalent are allowed (see constructor)
     */
    public $dateFormats;


    /**
     * Public constructor
     */
    public function DHTML_Calendar()
    {
        $this->calendar_options = array('ifFormat' => '%Y-%m-%d', 'daFormat' => '%Y-%m-%d');
        $this->dateFormats = array (
        'D' => '%a',
        'l' => '%A',
        'M' => '%b',
        'F' => '%B',
        'd' => '%d',
        'j' => '%e',
        'H' => '%H',
        'h' => '%I',
        'z' => '$j',
        'G' => '%k',
        'g' => '%l',
        'm' => '%m',
        'i' => '%M',
        'A' => '%p',
        'a' => '%P',
        's' => '%S',
        'w' => '%w',
        'y' => '%y',
        'Y' => '%Y');

    }

    /**
     * Add an option in the calendar_options array.
     * Those options will be used while using the setup method of the Calendar (JS object)
     *
     * @param $name     name of the option
     * @param $value    value of the option
     */
    public function set_option($name, $value)
    {
        $this->calendar_options[$name] = $value;
    }

    /**
     * This method will create the Javascript code to display a calendar
     *
     * @param $name     name of the option
     * @param $value    value of the option
     */
    private function createJavaScriptCode($other_options = array(), $id = null)
    {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));

        $code  = '<script type="text/javascript">Calendar.setup({';
        $code .= $js_options.'});</script>';

        return $code;
    }

    /**
     * Build the HTML and JS code to render a calendar
     *
     * @param $id       Id (and name) or input control
     * @param $value    Starting date (a parsable string)
     * @param $mode     Render mode (default "date", possible mode is "datetime")
     * @param $locked   True to lock control (default is false)
     * @param $callback JS callback (or null for default behaviour)
     * @param $hidden
     * @param $lang     Lang to be used
     * @param $css      Css to be used
     * @param $dateFormat (php date format)
     * @param $timeFormat (php time format)
     * @param $flat     bool true if calendar is to be flat (default=false)
     *
     */
    function renderHTMLCalendar($id, $value = null, $mode = 'date', $locked=false, $callback = null, $hidden = false, $lang = "en", $css = 'calendar-mos', $dateFormat = 'Y-m-d', $timeFormat = 'H:i', $flat = false)
    {
        $project = wcmProject::getInstance();
        $config = wcmConfig::getInstance();

        $result = '';

        $this->set_option("inputField", $id);
        $this->set_option("displayArea", 'dt_'.$id);
        $this->set_option("button", "trigger_".$id);

        $this->set_option("daFormat", $this->parseFormat($dateFormat));
        $this->set_option("ifFormat", "%Y-%m-%d");

        if($value != null)
        $this->set_option("date", $value);

        if ($flat)
        {
            $this->set_option("flat", $id);
        }

        if($callback != null)
        {
            $this->set_option(($flat) ? "flatCallback" : "onUpdate", $callback);
        }

        if($mode == "datetime")
        {
            if (!$flat)
            {
                $this->set_option("daFormat", $this->parseFormat($dateFormat)." ".$this->parseFormat($timeFormat));
                $this->set_option("ifFormat", "%Y-%m-%d %H:%M");
            }
            $this->set_option("showsTime", true);
        }

        if ($mode == 'date' && $value)
        $datePrint = date($dateFormat, strtotime($value));
        else if ($mode == 'datetime' && $value)
        $datePrint = date($dateFormat . ' ' . $timeFormat, strtotime($value));
        else
        $datePrint = '';

        if($locked && !$flat)
        {
            $result .= '<span style="margin-left:4px" id="dt_'.$id.'">' . $datePrint . '</span>';
            $result .= $this->createJavaScriptCode();
        }
        else if (!$flat)
        {
            // Render the calendar div and javascript
            $result  = '<input type="hidden" name="'.$id.'" id="'.$id.'" value="'.textH8($value).'" />';
            $result .= '<img hspace="4" src="'.$config['wcm.backOffice.url'].'img/calendar.gif" style="cursor:pointer" alt="" id="trigger_'.$id.'" align="absMiddle">';
            $result .= '<span style="margin-left:4px" id="dt_'.$id.'">' . $datePrint . '</span>';
            $result .= $this->createJavaScriptCode();
        }
        else
        {
            // Render the calendar div and javascript
            $result  = '<div id="'.$id.'" style="width:220px;"></div>';
            $result .= $this->createJavaScriptCode();
        }

        return $result;
    }

    /**
     * Function parseFormat
     * Parse format to make it compatible with calendar.js
     *
     * @param $format string required format
     *
     * @return $formatCal
     */
    function parseFormat($format)
    {
        $formatCal = $format;

        foreach ($this->dateFormats as $phpDate => $calDate)
        {
            if (stristr($format, $phpDate))
            {
                $formatCal = str_replace($phpDate, $calDate, $formatCal);
            }
        }
        return $formatCal;
    }


    /**
     * create ash to set javascript options for calendar
     *
     * @param array $array array to be parsed
     *
     * @return string   string that javascript can understand
     */
    function _make_js_hash($array)
    {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array))
        {
            if (is_bool($val))
            $val = $val ? 'true' : 'false';

            else if (!is_numeric($val) && $key != "flatCallback" && $key != "onUpdate")
            $val = '"'.$val.'"';

            if ($jstr) $jstr .= ',';

            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    /**
     * Parses an array to transform it into a string
     *
     * @param array $array
     * @return string
     */
    function _make_html_attr($array)
    {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array))
        {
            if($key == "flatCallback"|| $key == "onUpdate")
                $attrstr .= $key . '=' . $val;
            else
                $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
}

/**
 * Render the generic asset bar
 *
 * @param string $header  Header to display before the title
 * @param string $title   Title bar
 * @param string $actions Array of menu actions
 * @param string $sysinfo Optional information (appears below the asset bar)
 */
function renderAssetBar($header, $title, $actions = array(), $workflowInfo = null, $sysinfo = null)
{
    // Render asset bar
    $dhtml  = '';
    $dhtml .= '<div id="assetbar">';
    $dhtml .= '<h3><em>' . $header . '</em> ' . $title . '</h3>' . "\n";
    $dhtml .= '<ul>';
    foreach($actions as $action)
    {
        $class = getArrayParameter($action, 'code', '');
        if (isset($action['child'])) $class .= ' parent';

        $url = null;
        if (getArrayParameter($action, 'inactive', 0))
        {
            $class .= ' inactive';
        }
        elseif(isset($action['url']))
        {
            $url   = ' href="' . $action['url'] . '"';
        }

        $dhtml .= '<li><a class="' . $class . '"' . $url . '>' . getArrayParameter($action, 'name', '?') . '</a>';
        if (isset($action['child']))
        {
            $dhtml .= '<ul>';
            foreach($action['child'] as $action)
            {
                $dhtml .= '<li><a class="' . getArrayParameter($action, 'code', '') .'" href="' . getArrayParameter($action, 'url', '#') . '">' . getArrayParameter($action, 'name', '?') . '</a></li>';
            }
            $dhtml .= '</ul>';
        }

        $dhtml .= '</li>';
    }
    $dhtml .= '</ul>';
    $dhtml .= '</div>';
    $dhtml .= '<div id="sysinfo">';
    if ($workflowInfo)
    {
        $dhtml .= '<div id="workflow">';
        $dhtml .= '<h4 class="' . $workflowInfo['code'] . '"><strong>' . getConst($workflowInfo['name']) . '</strong></h4>';
        if (isset($workflowInfo['child']))
        {
            $dhtml .= '<ul>';
            foreach($workflowInfo['child'] as $transition)
            {
                $dhtml .= '<li><a class="' . $transition['code'] .'" href="' . $transition['url'] . '">' . getConst($transition['name']) . '</a></li>';
            }
            $dhtml .= '</ul>';
        }
        $dhtml .= '</div>';
    }
    $dhtml .= '<ul class="info">';
    $dhtml .= '<li class="stats">' . $sysinfo . '</li>';
    $dhtml .= '</ul>';
    $dhtml .= '</div>';

    echo $dhtml;
}
?>