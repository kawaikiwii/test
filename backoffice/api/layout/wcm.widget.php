<?php
/**
 *  Widget
 *
 */
abstract class wcmWidget
{
    const VIEW_FRAME = 1;
    const VIEW_SETTINGS = 2;
    const VIEW_CONTENT = 4;
    const IS_FIXED = 8;
    const VIEW_ALL = 7;

    private static $config = null;
    /**
     *  (Int) Widget's guid
     */
    public $guid = 0;

    /**
     *  (Int) widget's display mode
     */
    public $mode = null; 

    /**
     *  (Mixed) widget's context
     */
    public $context = null;

    /**
     *  (Array) widget's settings
     */
    public $settings = array();

    /**
     *  (Smarty) Smarty instance
     */
    public $smarty = null;
    
    /**
     * Constructor
     *
     *  @params Int         $mode       Display mode
     *  @params Array|null  $settings   Settings array
     *  @params Mixed|null  $context    Default context
     *  @param  Int         $guid       Widget's guid
     *  @param  Smarty      $smarty     Smarty instance
     */
    public function __construct($mode, $settings = null, $context = null, $guid = false, $smarty = null)
    {
        $this->guid = ($guid===false)?rand(0,999):$guid;
        $this->mode = $mode;
        $this->init($settings, $context);
        $this->smarty = $smarty;
    }

    /**
     * Return the label of the box in designMode
     *
     * @return String   Label to display
     */
    public function getLabel()
    {
        return get_class($this);
    }

    /**
     *  Initialise the widget's settings and context
     *  
     *  @param Array|null   $settings   Settings array
     *  @param Mixed|null   $context    Default context
     */
    public function init($settings = null, $context = null)
    {
        $this->initSettings($settings);
        $this->initContext($context);
    }

    /**
     * Initialise the widget's context
     *  
     * @param Mixed|null    $context    Default context
     */
    public function initContext($context = null) 
    {
        $this->context = $context;
    }

    /**
     * Initialise the widget's settings
     *
     *  @params Array|null  $settings   Settings array
     */
    public function initSettings($settings = null)
    {
        $this->settings = $settings;
    }

    /**
     * Save the data of the widget
     *
     * @params  Array   $dataSet    Array of data to save
     */
    public function saveData($dataSet)
    {
        $modifiedObjects = array();

        foreach($dataSet as $key => $value)
        {
            $identifiers = explode('-', $key);

            if (count($identifiers) == 3 && class_exists($identifiers[0]))
            {
                $objectKey = $identifiers[0] .'-' . $identifiers[1];
                $propertie = $identifiers[2];

                if(!array_key_exists($objectKey, $modifiedObjects))
                    $bizobject = $modifiedObjects[$objectKey] = new $identifiers[0](wcmProject::getInstance(), $identifiers[1]);
                else 
                    $bizobject = $modifiedObjects[$objectKey];

                $bizobject->$propertie = $value;
            }
            else
            {
                $method = array_shift($identifiers);
                if(method_exists($this, $method))
                        $this->$method($identifiers, $value);
            }
        }
        foreach($modifiedObjects as $bizobject)
                $bizobject->save();
    }

    /**
     *  Display the widget in preview mode
     *
     *  @params Array|null  $settings   Settings array
     *  @return String      HTML of the widget
     */
    public function preview($settings = null)
    {
        $this->mergeSettings($settings);
        $this->mode &= ~wcmWidget::VIEW_SETTINGS;
        return $this->render(); 
    }

    /**
     *  Display the widget in edit mode
     *
     *  @params Array|null  $settings   Settings array
     *  @return String      HTML of the widget
     */
    public function edit($settings = null)
    {
        $this->mergeSettings($settings);
        $this->mode |= wcmWidget::VIEW_SETTINGS;
        return $this->render();
    }


    /**
     *  Merge the settings and the given array
     *
     *  @params Array   $settings   settings array
     */
    public function mergeSettings($settings)
    {
        if(is_array($settings))
                array_merge($this->settings, $settings);
    }

    /**
     *  Function which render the widget
     *  
     *  @params String|null     $templateFile   Path to the template file, if null the default template will be take
     *  @return     HTML string of the widget
     */
    public function render($templateFile = null)
    {
        if($templateFile == null) $templateFile = self::getPath(get_class($this)).'widget.tpl';
        $generator = ($this->smarty!=null) ? $this->smarty : new wcmTemplateGenerator(null, false, $this->mode);
        return $generator->executeTemplate($templateFile, array('owidget' => $this, 'widget' => $this->getAssocArray(false)));
    }

    /**
     *  Function which return the innerHTML of the settings form
     *
     *  @return     HTML string or false if no settings
     */
    public function displaySettings()
    {
        return false;
    }

    /**
     * Returns an associative array containing public properties and their values
     * The following keys and values are added to the array:
     *      'acontext' => an associative array with actual context->getAssocArray(false)
     *
     * @return array
     */
    public function getAssocArray($toXML)
    {
        $assocArray = array();
        foreach(getPublicProperties($this) as $key => $val)
        {
            $assocArray[$key] = $val;
        }

        // Expose context
        if ($this->context && method_exists($this->context, 'getAssocArray'))
        {
            $assocArray['acontext'] = $this->context->getAssocArray($toXML);
        }
        
        return $assocArray;
    }

    /**
     *  Function which display the HTML of the widget depending on the widget's mode
     *
     *  @return HTML of the widget
     */
    final public function display()
    {
        $className = get_class($this);
        $widgetId = $className.'-'.$this->guid;

        $html = '';

        if($this->mode & wcmWidget::VIEW_FRAME)
                $html .= '<div class="block" id="'. $widgetId .'">';

        if($this->mode & wcmWidget::VIEW_SETTINGS) 
        {
            $settings = $this->displaySettings();

            $html .= '<div class="handle">';

            if(!($this->mode & wcmWidget::IS_FIXED))
                    $html .= '    <div class="block-remove" title="' . _BIZ_REMOVE . '" onclick="$(\''.$widgetId.'\').remove();"><span>x</span></div>';

            if($this->mode & wcmWidget::VIEW_CONTENT)
            {
                $html .= '<div class="block-savedata" title="' . _BIZ_SAVE_WIDGETS . '" onclick="javascript:portal.getWidget(\''.$widgetId.'\').saveData();"><span>Save data</span></div>';
                $html .= '<div class="block-refresh" title="' . _BIZ_REFRESH_WIDGET_BUGS . '" onclick="javascript:portal.getWidget(\''.$widgetId.'\').refreshData();"><span>Refresh Data</span></div>';
            }

            if($settings)
                    $html .= '    <div class="block-editsettings" title="' . _BIZ_EDIT_SETTINGS . '" onclick="javascript:portal.getWidget(\''.$widgetId.'\').displaySettings();"><span>Edit</span></div>';

            $html .= $this->getLabel();
            $html .= '</div>';

            if($settings) 
            {
                $html .= '<div class="settings">';
                $html .= '<div id="'.$widgetId.'-settings">'.$settings.'</div>';
                $html .= '<div class="settingsToolbar">';
                $html .= '<a href="#" onclick="javascript:return portal.getWidget(\''.$widgetId.'\').saveSettings();">' . _BIZ_SAVE . '</a>';

                if($this->mode & wcmWidget::VIEW_CONTENT)
                        $html .= '<a href="#" onclick="javascript:return portal.getWidget(\''.$widgetId.'\').applySettings();">' . _BIZ_APPLY . '</a>';

                $html .= '<a href="#" onclick="javascript:return portal.getWidget(\''.$widgetId.'\').cancelSettings();">' . _BIZ_CANCEL . '</a>';
                $html .= '</div>';
                $html .= '</div>';
            }
        } 
        elseif($this->mode & wcmWidget::VIEW_FRAME) 
        {
            $html .= '<div>' . $this->getLabel() . '</div>';
        }

        if($this->mode & wcmWidget::VIEW_SETTINGS)
                $html .= '<div class="content">' . PHP_EOL;

        if($this->mode & wcmWidget::VIEW_FRAME && $this->mode & wcmWidget::VIEW_CONTENT)
                $html .= '<form id="'. $className .'-'. $this->guid .'-context">';

        if($this->mode & wcmWidget::VIEW_CONTENT)
                $html .= $this->render();

        if($this->mode & wcmWidget::VIEW_FRAME && $this->mode & wcmWidget::VIEW_CONTENT)
                $html .= '</form>';

        if($this->mode & wcmWidget::VIEW_SETTINGS)
                $html .= '</div>';

        if($this->mode & wcmWidget::VIEW_FRAME)
                $html .= '</div>';

        return $html;
    }



    /**
     *  Get the path of a given object
     */
    public static function getPath($widgetName)
    {
        static $pathArray = null;
        if($pathArray == null)
        {
            $project = wcmProject::getInstance();
            $pathArray = array();
            foreach($project->layout->loadWidgetConfig() as $widget)
            {
                $pathArray[(string)($widget->class)] = $widget->path . DIRECTORY_SEPARATOR;
            }
        }
        if(array_key_exists($widgetName, $pathArray))
                return $pathArray[$widgetName];
        else
                return false;
    }

}
