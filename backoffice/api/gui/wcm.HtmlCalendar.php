<?php
/**
 * Project:     WCM
 * File:        wcm.html.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The HTML Calendar class is a php wrapper to easily render a DHTML Calendar
 * Note: This code is strongly based (and close) to the original
 * code from http://www.dynarch.com/projects/calendar/
 */
class wcmHtmlCalendar
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
    public function __construct()
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
    function render($id, $value = null, $mode = 'date', $callback = null, $locked=false, $lang = "en", $css = 'calendar-mos', $dateFormat = 'Y-m-d', $timeFormat = 'H:i', $flat = false)
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
            $result  = '<input type="text" class="type-date';
            if ($mode == 'datetime') $result .= 'time';
            $result .= '" name="'.$id.'" id="'.$id.'" value="'.textH8($value).'" />';
            $result .= '<a href="" class="date-picker" style="cursor:pointer" id="trigger_'.$id.'"><span>' . _PICK_DATE . '</span></a>';
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