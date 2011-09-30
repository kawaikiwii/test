<?php
/**
 * Project:     WCM
 * File:        wcm.session.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

 /**
 * The session class represents a user's session
 *
 */

class wcmSession extends wcmObject {

    private static $singleton;

    /**
     * (int) Constant used for statistics
     * when user start a new session
     */
    const STAT_START_SESSION = 1;

    /**
     * (int) Constant used for statistics
     * when user navigate into the back-office
     */
    const STAT_NAVIGATE = 2;

    /**
     * (int) Constant used for statistics
     * when user view an object
     */
    const STAT_VIEW_OBJECT = 3;

    /**
     * (int) Constant used for statistics
     * when user save an object
     */
    const STAT_SAVE_OBJECT = 4;

    /**
     * (int) Constant used for statistics
     * when user delete an object
     */
    const STAT_DELETE_OBJECT = 5;

    /**
     * (int) Constant used for statistics
     * when user publish an object
     */
    const STAT_PUBLISH_OBJECT = 6;

    /**
     * (int) Constant used for statistics
     * when user search for something
     */
    const STAT_SEARCH = 7;

    /**
     * (int) Constant used for statistics
     * when user generate some content
     */
    const STAT_GENERATE = 8;

    /**
     * (int) Constant used for statistics
     * when user import some content
     */
    const STAT_IMPORT = 9;

    /**
     * Identifier of session's owner
     */
    public $userId      = null;

    /**
     * Name of session's owner
     */
    public $userName    = null;

    /**
     * When session has started
     */
    public $startDate   = null;

    /**
     * When session has ended (or null)
     */
    public $endDate     = null;

    /**
     * Private members
     */
    private $user = null;
    private $isAdmin = false;
    private $site = null;
    private $language;
    private $currentAction = 'login';
    private $lastPing = 0;

    /**
    * Cache of permissions
    */
    private $permissions = array();

    /**
     * Returns the current session
     *
     * @return wcmSession Current session object
     */

    static public function getInstance() {
        if (!isset(self::$singleton)) {
            $config = wcmConfig::getInstance();

            // Enable web session ?
            if (session_id() == null) {
                session_name($config['wcm.default.sessionName']);
                session_start();
            }

            // Try to load previous session form $_SESSION
            if (isset($_SESSION['wcmSession'])) {
                self::$singleton = $_SESSION['wcmSession'];
            } else {
                // Create a new session and persist in $_SESSION
                self::$singleton = new wcmSession();
                $_SESSION['wcmSession'] = self::$singleton;
            }
        }

        return self::$singleton;
    }

    /**
     * Set current session instance
     *
     * @param wcmSession Current session object
     */

    static public function setInstance($session) {
        // Enable web session ?
        if (session_id() == null) {
            session_name($config['wcm.default.sessionName']);
            session_start();
        }

        self::$singleton = $session;
        $_SESSION['wcmSession'] = self::$singleton;
    }
    
    /**
     * Constructor
     *
     * @param int $id Optional id (used to refresh object)
     *
     */

    public function __construct($id = null) {
        $this->database = wcmProject::getInstance()->database;
        $this->tableName = '#__session';
        parent::__construct($id);
    }

    /**
     * Login (open session)
     *
     * @author modified by M.Bully for account expiration
     * @param string $userLogin  User name
     * @param string $password  User password
     *
     * @return boolean True if login succeed, false otherwise
     */

    public function login($userLogin, $password) {
        $project = wcmProject::getInstance();

        // Search for user
        $query = "SELECT id,login,password FROM #__user WHERE login=? AND password=?";
        $parameters = array($userLogin, md5($password));

        // Check password validity and retrieve user information
        $id = $project->database->executeScalar($query, $parameters);
        if ($id == null) {
            $project->logger->logWarning('wcmSession::login failed: invalid login or password for '.$userLogin);
            return array(-1);
        }

        // Check expirationDate on existing Account
        $account = new account();
        $account->refreshByWcmUser($id);
        if ($account->id) {
            if ($account->expirationDate != NULL && $account->expirationDate < date("Y-m-d")) {
                $project->logger->logWarning('wcmSession::login failed: Account expired '.$userLogin);
                return array(-2, $account->managerId);
           }
        }

		// If everything Ok : start a session
        return $this->startSession($id);
    }

    /**
     * Login with admistrator password and impersonate another user
     *
     * @param string $userLogin Login of user to impersonate
     * @param string $rootPassword Password of the 'root' user (id=1)
     *
     * @return boolean True if login succeed, false otherwise
     *      */

    public function loginAs($userLogin, $rootPassword) {
        $project = wcmProject::getInstance();

        // Check administrator password
        $sql = "SELECT id FROM #__user WHERE id=? AND password=?";
        $parameters = array(wcmMembership::ROOT_USER_ID, md5($rootPassword));

        // Check root password validity
        $id = $project->database->executeScalar($sql, $parameters);
        if ($id == null) {
            $project->logger->logWarning('wcmSession::loginAs failed: invalid root password');
            return array(-1);
        }

        // Search id of userLogin
        $sql = "SELECT id FROM #__user WHERE login=?";
        $id = $project->database->executeScalar($sql, array(substr($userLogin, 1)));
        if ($id == null) {
            $project->logger->logWarning('wcmSession::loginAs failed: cannot find impersonated login: '.$userLogin);
            return array(-1);
        }

        return $this->startSession($id);
    }    

    /**
     * Start a new session with a specific user and a specific action
     * Note: this method does not check authentication
     *
     * @param int $userId ID of user's session
     * @param string $startingAction Optional starting action (or 'home' by default)
     */

    public function startSession($userId, $startingAction = 'home') {
        $project = wcmProject::getInstance();
        $config = wcmConfig::getInstance();

        // Retrieve user's information
        $this->user = new wcmUser($project, $userId);
        if (!$this->user->id) {
            $project->logger->logError("wcmSession::startSession failed: Invalid user ID ".$userId);
            return false;
        }

        $this->userId = $this->user->id;
        $this->userName = getConst($this->user->name);
        $this->isAdmin = $this->user->isAdministrator;

        // Set start date
        $this->startDate = date("Y-m-d H:i:s");

        // Set default values (language, workingSite, action)
        $this->setCurrentAction($startingAction);
        $this->setSiteId($config['wcm.default.siteId']);

        // Save session in db
        $_SESSION['wcmSession'] = $this;
        if (!$this->store()) {
            $project->logger->logError("wcmSession::startSession failed: ".$this->lastErrorMsg);
            return false;
        }

        // Add statistic
        $this->addStat(self::STAT_START_SESSION);
        $project->logger->logMessage('wcmSession::startSession #'.$this->id.' for user '.$this->userName);

        return true;
    }

    /**
     * Logout (close session)
     */

    public function logout() {
        $project = wcmProject::getInstance();

        // Trace logout information
        $project->logger->logMessage('wcmSession::endSession #'.$this->id.' for user '.$this->userName);

        // Set end date and update db
        $this->endDate = date("Y-m-d H:i:s");
        if ($this->userId != null) {
            @$this->store();
        }

        // Clear session
        @session_destroy();
        	
        // Clear resources and set next action to 'login'
        $this->currentAction = 'login';
        $this->userId = 0;
        $this->userName = null;
        $this->user = null;
    }

    /**
     * Ping the session to handle timeout
     */

    public function ping() {
        $this->lastPing = time();
    }

    /**
     * Check if session has expired
     *
     * @return boolean TRUE if session has expired
     */

    public function hasTimedout() {
        $config = wcmConfig::getInstance();
        $ct = time(); // current time
        
        if (isset($this->user)) {
            return ($ct >= ($this->lastPing ? $this->lastPing  + $config['wcm.default.sessionTimeout'] : strtotime($this->startDate)) + $config['wcm.default.sessionTimeout']);
        }
        
        // Timeout not applicable to non-logged in sessions
        return false;
    }

    /**
     * Set current action
     *
     * @param string $action Current action name
     */

    public function setCurrentAction($action) {
        // Don't change action is user is not logged-in
        if ($this->userId) {
            $this->currentAction = $action;
            $_SESSION['wcmSession'] = $this;
        }
    }

    /**
     * Get current action
     *
     * @return string The current action
     */

    public function getCurrentAction() {
        return $this->currentAction;
    }

    /**
     * Set current language (and load corresponding ressources)
     *
     * @param string|null $language Current language name (null means use configured default)
     *
     * @return bool False if the given language is invalid, true otherwise
     */

    public function setLanguage($language = null) {
        $config = wcmConfig::getInstance();

        if ($language === null)
            $language = $config['wcm.default.language'];
        
        $language = @$this->getSite()->language;

        $this->language = $language;
        $_SESSION['wcmSession'] = $this;

        // Note that re-defining existing constant raise a warning
        if (!DEFINED('_WCM_LANGUAGE') || 0 <> strcmp(_WCM_LANGUAGE, $this->language)) {
            // Load language resources (i.e. re-define constants)
            // => Retrieve default language and ensure it is supported
            $lgfile = WCM_DIR."/languages/".$this->language.".php";
            
            //echo "##".$lgfile;
            
            if (!file_exists($lgfile))
                return false;

            @require($lgfile);
        }

        // Re-defining existing constant raise a warning
        if (!DEFINED('_BIZ_WCM_LANGUAGE') || 0 <> strcmp(_BIZ_WCM_LANGUAGE, $this->language)) {
            // Load language resources (i.e. re-define constants)
            // => Retrieve default language and ensure it is supported
            $lgfile = WCM_DIR."/business/languages/".$this->language.".php";
            if (!file_exists($lgfile))
                return false;

            @require($lgfile);
        }

        return true;
    }

    /**
     * Get current language
     *
     * @return string The current language
     */

    public function getLanguage() {
        return $this->language;
    }

    /**
     * Returns the connected user
     *
     * @return wcmUser The connected user
     */

    public function getUser() {
        return $this->user;
    }

    /**
     * Set the working site
     *
     * @param site $site The working site
     */

    public function setSite($site) {
        $this->site = $site;
        $_SESSION['wcmSession'] = $this;
        $_SESSION['siteId'] = $site->id;
        
        $this->setLanguage($site->language);

        // Special operatiom: clear GUI cache
        // @TODO: Find a better way for that; event-driven?
        unset($_SESSION['wcmGUI_renderWorkingSite']);
    }

    /**
     * Set the working site
     *
     * @param int $siteId Id of the working site
     */

    public function setSiteId($siteId) {
        $site = new site;
        
        if ($siteId != 0) {
            $site->refresh($siteId);
            if ($site->id == 0) {
                wcmProject::getInstance()->logger->logError('wcmSession::setSite failed: Invalid site id '.$siteId);
            }
        }
        $this->setSite($site);
    }

    /**
     * Returns the working site
     *
     * @return site The current working site (or null)
     */

    public function getSite() {
        return $this->site;
    }

    /**
     * Returns the id of the current working site
     *
     * @return int ID of the current workfing site (or zero)
     */

    public function getSiteId() {
        return ($this->site) ? $this->site->id : 0;
    }

    /**
     * Returns the project instance
     *
     * @return wcmProject The project
     */

    public function getProject() {
        return wcmProject::getInstance();
    }

    /**
     * Checks whether current user is administrator
     *
     * @return boolean True is current is is an administrator
     *
     */

    public function isAdministrator() {
        return $this->isAdmin;
    }

    /**
     * Checks if a given action is allowed for current user (use session cache)
     *
     * @param mixed  $sysobject  sysobject (or permission target) on which to check permssion
     * @param int    $permission permission to check (constant from wcmPermission class)
     *
     * @return boolean True if role is granted, false if role is denied
     */

    public function isAllowed($sysobject, $permission) {
        if (!$this->userId)
            return false;

        return $permission == ($this->getPermissions($sysobject) & $permission);
    }

    /**
     * Get permission value (and cache result during session)
     *
     * @param mixed $sysobject  sysobject (or permission target) on which to check permssion
     *
     * @return permission value
     */

    public function getPermissions($sysobject) {
        if ($this->isAdmin)
            return wcmPermission::P_ALL;

        // Retrieve permission target
        $key = (is_string($sysobject)) ? $sysobject : $sysobject->getPermissionTarget();

        // Compute permission and store value in sesion cache?
        if (!isset($this->permissions[$key])) {
            // Handle P_NONE as special permission set (deny all)
            $permissions = $sysobject->getUserPermissions($this->userId);
            if (wcmPermission::P_NONE == $permissions)
                $permissions = 0;
            $this->permissions[$key] = $permissions;
            $_SESSION['wcmSession'] = $this;
        }

        return $this->permissions[$key];
    }

    /**
     * Gets an encrypted token representing this session.
     *
     * @return string The encrypted token
     */

    public function getToken() {
        // Session token == session ID + session creation time
        $sessionToken = $this->id.'_'.$this->startDate;

        // Encrypt and return the session token
        return wcmEncryption::getInstance()->encrypt($sessionToken);
    }
    
    /**
     * Exposes 'user' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The session's user getAssocArray() (or null)
     */

    public function getAssoc_user($toXML = false) {
        return ($this->user) ? $this->user->getAssocArray($toXML) : null;
    }

    /**
     * Exposes 'history' in the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The session's user history as an assoc array 
     *               of assoc array (url => (objectClass =>, objectId =>, info =>))
     */     

    public function getAssoc_history($toXML = false) {
        if ($toXML)
            return null;
        
        $history = array();

        $rs = $this->getViewHistory(null, 0, 10);
        if ($rs) {
            while ($rs->next()) {
                $row = $rs->getRow();
                $url = wcmMVC_Action::computeObjectURL($row['objectClass'], $row['objectId']);
                $history[$url] = array('objectClass'=>$row['objectClass'], 'objectId'=>$row['objectId'], 'info'=>getConst($row['info']));
            }
            unset($rs);
        }
        
        return $history;
    }
    
    /**
     * Adds an entry in the statistics table
     *
     * @param integer $kind Kind of event
     * @param object $relatedObject Related object (or null)
     * @param mixed $info Extra info (any serializable object, or null)
     *
     * @return integer Number of affected rows (should be 1, or -1 on error)
     */

    public function addStat($kind, $relatedObject = null, $info = null) {
        $sql = 'INSERT INTO #__statistics(sessionId,userId,userName,kind,'.'dateAndTime,objectClass,objectId,info) VALUES(?,?,?,?,?,?,?,?)';
        
        if ($relatedObject !== null) {
           $objectClass = get_class($relatedObject);
           $objectId = (property_exists($relatedObject, 'id')) ? $relatedObject->id : null;
        } else {
           $objectClass = $objectId = null;
       }

       // Prepare info for DB storage
        if ($info !== null && (is_array($info) || is_object($info))) {
           $info = serialize($info);
       }

        $params = array($this->id, $this->userId, $this->userName, $kind, date('Y-m-d H:i:s'), $objectClass, $objectId, $info);

        return $this->database->executeStatement($sql, $params);
    }

    /**
     * Returns a database resultset on the statistics table
     *
     * @param string $where Condition to apply on statistics table
     * @param array $params Optional parameters to substitute '?' chars in condition
     * @param integer $offset Optional offset to retrieve statistics
     * @param integer $limit Optional limit to retrieve statistics
     *
     * @return ResultSet Creole Resultset corresponding to query or null on failure
     */

    public function getStats($where, $params = null, $offset = 0, $limit = 0) {
        $sql = 'SELECT * FROM #__statistics';
        if ($where)
            $sql .= ' WHERE '.$where;

        return $this->database->executeQuery($sql, $params, $offset, $limit);
    }
    
    public function disableStatItem($argClass, $argId) {
        $sql = 'UPDATE #__statistics SET linkable=0 WHERE objectClass=? AND objectId=?';
        $this->database->executeQuery($sql, array($argClass, $argId));
    }

    /**
     * Returns a database resultset representing a view history
     * (list of previously viewed objects for current user)
     *
     * @param string $objectClass Optional object class
     * @param integer $offset Optional offset to retrieve statistics
     * @param integer $limit Optional limit to retrieve statistics
     *
     * @return ResultSet Creole Resultset corresponding to query or null on failure
     *                   With 'objectClass', 'objectId' and 'info' columns
     */

    public function getViewHistory($objectClass = null, $offset = 0, $limit = 20) {
        /*$sql = 'SELECT MAX(id), objectClass,objectId,info, linkable FROM #__statistics WHERE userId=? AND kind=?';
        if ($objectClass) $sql .= ' AND objectClass=?';
        $sql .= ' GROUP BY objectId, objectClass ORDER BY 1 DESC';
        $params = array($this->userId, self::STAT_VIEW_OBJECT, $objectClass);
        return $this->database->executeQuery($sql, $params, $offset, $limit);*/

	/* OPTIMISATION NSTEIN 29/06/2009 */
	$sql = 'SELECT MAX(id) id, objectClass, objectId, info, linkable, dateAndTime FROM #__statistics';
        $sql .= ' WHERE userId='.$this->userId;
        $sql .= ' AND kind='.self::STAT_VIEW_OBJECT;
        if ($objectClass) {
	    $sql .= " AND objectClass='$objectClass'";
	}
	$sql .= ' GROUP BY objectClass, objectId';
	$sql .= ' ORDER BY id DESC';
	
        return $this->database->executeQuery($sql, null, $offset, $limit);
    }
    
    public function removeHistoricItem($objectClass, $objectId) {
        $sql = 'DELETE FROM #__statistics WHERE objectId=? AND objectClass=?';
        $this->database->executeQuery($sql, $objectClass, $objectId);
    }
}
