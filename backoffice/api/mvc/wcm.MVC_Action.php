<?php

/**
 * Project:     WCM
 * File:        wcm.MVC_Action.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class implements the default system action for the
 * MVC controller
 *
 * Basically it instanciate the sysobject matching $_REQUEST['_wcmClass']
 * and execute the specific action according to $_REQUEST['_wcmTodo']
 */
abstract class wcmMVC_Action
{
    protected $id;
    protected $class;
    protected $params;
    protected $treeId;

    /**
     * (string) Action to execute
     */
    public $todo;

    /**
     * (object) Instance of object used for current action
     */
    public $context;

    /**
     * The constructor automatically instanciate the context,
     * execute the action and store result in session
     *
     * @param array $parameters An array of initial parameters to protected properties (id, todo, class, ...)
     */
    public function __construct(array $parameters = null)
    {
        if (!is_array($parameters))
            $parameters = array();
        $this->params = $parameters;

        // Set initial properties
        $this->id = getArrayParameter($parameters, 'id', getArrayParameter($_REQUEST, 'id', null));
        if ($this->id !== null) $this->id = intval($this->id);
        $this->todo = getArrayParameter($parameters, 'todo', getArrayParameter($_REQUEST, '_wcmTodo', null));
        $this->class = getArrayParameter($parameters, 'class', getArrayParameter($_REQUEST, '_wcmClass', null));
        $this->treeId = getArrayParameter($parameters, 'tree', wcmSession::getInstance()->getCurrentAction());

        // Clear message if there is no action todo
        self::clearMessage();

        // Get context and execute action
        $session = wcmSession::getInstance();
        $project = wcmProject::getInstance();
        $this->setContext($session, $project);

        // Execute action
        $this->executeAction($session, $project);

        // Store new state
        $this->store();
    }

    /**
     * Store current action in session
     */
    protected function store()
    {
        // Memorize current action in $_SESSION
        if (!isset($_SESSION['wcmAction']))
            $_SESSION['wcmAction'] = array();

        $_SESSION['wcmAction']['todo'] = $this->todo;
        $_SESSION['wcmAction']['treeId'] = $this->treeId;
        $_SESSION['wcmAction']['context'] = $this->context;
    }

    /**
     * Redirect page to a new URL
     *
     * @param string $url New URL
     */
    protected function redirect($url)
    {
        header('location:'.$url);
        exit();
    }

    /**
     * Instanciate context
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     *
     * @return object Instanciated context
     */
    protected abstract function setContext($session, $project);

    /**
     * Default action executed when $todo is not set
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     *
     * @return string Action message to display
     */
    protected abstract function on($session, $project);

    /**
     * Execute specific action
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function executeAction($session, $project)
    {
        $method = 'on'.ucfirst($this->todo);

        if (method_exists($this, $method))
            $this->$method($session, $project);
        else
            $this->on($session, $project);
    }

    /**
     * Clear last action message
     */
    public static function clearMessage()
    {
        self::setMessage(null);
    }

    /**
     * Defines the last action message
     *
     * @param string $message Last action message
     * @param int $messageKind One of WCMLOG_MESSAGE, WCMLOG_WARNING or WCMLOG_ERROR
     */
    public static function setMessage($message, $messageKind = WCMLOG_MESSAGE)
    {
        if (!isset($_SESSION['wcmAction'])) $_SESSION['wcmAction'] = array();
        $_SESSION['wcmAction']['messageKind'] = $messageKind;
        $_SESSION['wcmAction']['message'] = $message;
    }

    /**
     * Defines the last action message (as a warning message)
     *
     * @param string $message Last action message
     */
    public static function setWarning($message)
    {
        self::setMessage($message, WCMLOG_WARNING);
    }

    /**
     * Defines the last action message (as a warning message)
     *
     * @param string $message Last action message
     */
    public static function setError($message)
    {
        self::setMessage($message, WCMLOG_ERROR);
    }

    /**
     * Returns the last action message
     *
     * @return string Last action message
     */
    public static function getMessage()
    {
        return (!isset($_SESSION['wcmAction'])) ? null : getArrayParameter($_SESSION['wcmAction'], 'message', null);
    }

    /**
     * Returns the last action message's kind
     *
     * @return int Last action message's kind
     */
    public static function getMessageKind()
    {
        return (!isset($_SESSION['wcmAction'])) ? null: getArrayParameter($_SESSION['wcmAction'], 'messageKind', 0);
    }

    /**
     * Returns the current URL
     *
     * @param string $newAction Optional action to override current action
     * @param string $newTodo Optional todo to override current todo
     * @param int @newId Optional id to override current id
     */
    public static function getURL($newAction=null, $newTodo=null, $newId=0)
    {
        $config = wcmConfig::getInstance();
        $url = $config['wcm.backOffice.url'];

        $url .= '?_wcmAction=' . ($newAction) ? $newAction : self::getAction();
        $url .= '&amp;_wcmTodo=' . ($newTodo) ? $newTodo : self::getTodo();
        if ($newId)
        {
            $url .= '&amp;id=' . $newId;
        }
        else
        {
            $context = self::getContext();
            if ($context && $context->id)
                $url .= '&amp;id=' . $context->id;
        }

        return $url;
    }

    /**
     * Returns the current action
     *
     * @return string Current action
     */
    public static function getAction()
    {
        return wcmSession::getInstance()->getCurrentAction();
    }

    /**
     * Returns the tree id
     *
     * @return string Tree id
     */
    public static function getTreeId()
    {
        return (!isset($_SESSION['wcmAction'])) ? null : getArrayParameter($_SESSION['wcmAction'], 'treeId', null);
    }

    /**
     * Returns the action to-do
     *
     * @return string Action to-do
     */
    public static function getTodo()
    {
        return (!isset($_SESSION['wcmAction'])) ? null : getArrayParameter($_SESSION['wcmAction'], 'todo', null);
    }

    /**
     * Returns the last action sysobject
     *
     * @return wcmObject Last action sysobject
     */
    public static function getContext()
    {
        return (!isset($_SESSION['wcmAction'])) ? null : getArrayParameter($_SESSION['wcmAction'], 'context', null);
    }

    /**
     * Returns a human-friendly title
     *
     * @return string A title for current action
     */
    public static function getTitle()
    {
        $title = wcmProject::getInstance()->title . ' :: ';

        $context = self::getContext();
        if ($context)
        {
            if ($context instanceOf wcmSysobject)
            {
                $title .= getConst($context->getMasterclass()->name);
            }
            elseif ($context instanceOf wcmObject)
            {
                $title .= get_class($context);
            }
            if (isset($context->id) && $context->id)
                $title .= ' #' . $context->id;
        }
        else
        {
            $title .= ucfirst(wcmSession::getInstance()->getCurrentAction());
        }

        return $title;
    }

    /**
     * Returns the tree associated to the last action
     *
     * @return wcmTree Last tree (or null)
     */
    public static function getTree()
    {
        $config = wcmConfig::getInstance();
        $treeParts = explode('/', self::getTreeId());
        $treeId = array_pop($treeParts);
        if (substr(self::getTreeId(), 0, 9) === 'business/')
        {
            $url = $config['wcm.backOffice.url'] . '/business';
            $modulePath = WCM_DIR . '/business/modules/tree/';
        }
        else
        {
            $url = null;
            $modulePath = null;
        }
        $tree = new wcmTree($treeId, $url, null, null, null, $modulePath);
        $tree->initFromSession();

        return $tree;
    
    }

    /**
     * Compute URL to view a specific object
     *
     * @param string $objectClass Name of object class
     * @param int $objectId Id of object
     * @param string $todo Optional todo ('view' by default)
     *
     * @return string URL associated to sysobject
     */
    public static function computeObjectURL($objectClass, $objectId = null, $todo = null, $params = null)
    {
        // Assume all system class starts with 'wcm' prefix
        if (substr($objectClass, 0, 3) == 'wcm')
        {
            $action = lcfirst(substr($objectClass, 3));
        }
        else
        {
            $action = 'business/'.$objectClass;
        }

        if ($objectId !== null)
        {
            if (!is_array($params)) $params = array();
            $params['id'] = $objectId;
        }

        return self::computeURL($action, $todo, $params);
    }

    /**
    /**
     * Compute the URL corresponding to a specific action controller
     *
     * @param string $action    Action name (or null for current)
     * @param string $todo      Action to execute
     * @param array  $params    Extra parameters (optional assoc array)
     */
    public static function computeURL($action = null, $todo = null, $params = null)
    {
        if (!$action)
        {
            $action = wcmSession::getInstance()->getCurrentAction();
        }

        $config  = wcmConfig::getInstance();
        $url = $config['wcm.backOffice.url'];

        if (substr($action, 0, 9) == 'business/')
        {
            $action = 'business/' . substr($action, 9);
        }
        $url .= 'index.php?_wcmAction='.$action;

        if ($todo)
        {
            $url .= '&_wcmTodo='.$todo;
        }

        if (is_array($params))
        {
            foreach($params as $key => $value)
            {
                $url .= '&'. urlencode($key) . '=' . urlencode($value);
            }
        }

        return $url;
    }

    /**
     * Execute system action.
     *
     * This function will try to locate {$action}.php in the 'actions' directory and assume
     * that the file contains a class named [wcm]{$action}Action which extends wcmMVC_Action
     * if the file does not exists, the function will instanciate wcmMVC_Sys|BizAction
     *
     * @param string $action   Action to execute
     * @param array  $params   Default parameters (such as 'class' => {aClassName}, ...)
     *
     * @return wcmMVC_Action   An instance of the executed action
     */
    public static function execute($action, $params = null)
    {
        // Set current action
        wcmSession::getInstance()->setCurrentAction($action);

        // Check file existence for action and determine class name
        if (substr($action, 0, 9) == 'business/')
        {
            $action = substr($action, 9);
            $filename = WCM_DIR . '/business/actions/' . $action . '.php';
            if (file_exists($filename))
            {
                $className = $action . 'Action';
                require_once($filename);
            }
            else
            {
                $className = 'wcmMVC_BizAction';
            }
        }
        else
        {
            $filename = WCM_DIR . '/actions/' . $action . '.php';
            if (file_exists($filename))
            {
                require_once($filename);
                $className = 'wcm' . ucfirst($action) . 'Action';
            }
            else
            {
                $className = 'wcmMVC_SysAction';
            }
        }

        // Instanciate action (will automatically execute action todo)
        return new $className($params);
    }
}
