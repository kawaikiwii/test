<?php
/**
 * Project:     WCM
 * File:        biz.webuser.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Definition of a webuser
 * 
 * A webuser is a user account created from outside of WCM's backend. This default implementation
 * of a user account contains standard properties to identifty the user including email address
 * and geographical location.
 * 
 * Passwords for the webuser are stored as MD5 hashes, however when creating a webuser you can use
 * the {@link webuser::setPassword() setPassword()} method in order to temporarily store an
 * unhashed copy of the password. This unhashed copy will not be stored, but can be useful for
 * say, emailing a notification to the webuser that his account is created.
 * 
 * Webusers can also be subscribed to other bizobjects through the {@link subscription subscription} bizobject.
 * 
 * @see subscription
 */
class webuser extends bizobject
{
    /**
     * EMail (unique index)
     * 
     * @var string
     */
    public $email;

    /**
     * Password (MD5 hash string)
     * 
     * @var string
     */
    public $password;

    /**
     * Nickname (unique index)
     * 
     * @var string
     */
    public $username;

    /**
     * First name
     * 
     * @var string
     */
    public $firstname;

    /**
     * Last name
     * 
     * @var string
     */
    public $lastname;

    /**
     * Postal address
     * 
     * @var string
     */
    public $address;

    /**
     * Postal code
     * 
     * @var string
     */
    public $postalCode;

    /**
     * City
     * 
     * @var string
     */
    public $city;

    /**
     * State
     * 
     * @var string
     */
    public $state;

    /**
     * Country
     * 
     * @var string
     */
    public $country;

    /**
     * Phone number
     * 
     * @var string
     */
    public $phone;

    /**
     * Last login of the user
     * 
     * @var string
     */
    public $lastLogin;

    /**
     *  Status constants
     *
     * @var string
     */
    const STATUS_WAITING = 'submitted';
    const STATUS_VALID = 'valid';
    const STATUS_BANNED = 'banned';


    /**
     * Set the user password (password is then encoded)
     *
     * @param string $password Clean password (or null to remove it)
     */
    public function setPassword($password)
    {
        $this->password = ($password == null) ? null : md5($password);
    }
    
    /**
     * Compare given password to password of webuser.
     *
     * @param  string $password Password to check
     *
     * @return bool   True if $password match the user password
     */
     public function checkPassword($password)
     {
        // null password?!
        if ($password == null)
            return ($this->password == null);

        return ($this->password == md5($password));
     }

    /**
     * Binds an assoc array to this object
     *
     * @param array  $assocArray (or null to ignore bindings)
     *
     * @return true on success, false otherwise
     */
     public function bind(array $assocArray = null)
     {
        if (is_array($assocArray))
        {
            // encode password when provided
            if (getArrayParameter($assocArray, 'password', null))
            {
                // same password?
                if ($assocArray['password'] != $this->password)
                {
                    $assocArray['password'] = md5($assocArray['password']);
                }
            }
            else
            {
                unset($assocArray['password']);
            }
        }

        return parent::bind($assocArray);
     }

    /**
    * Returns the captions needed to be displayed in the search page
    * 
    * @return array
    */
    public function getWorkflowStateList()
    {
        return array(
            webuser::STATUS_BANNED  => _BIZ_BANNED,
            webuser::STATUS_WAITING => _BIZ_WAITING,
            webuser::STATUS_VALID   => _BIZ_VALID
        );
    }
    
    /**
     * Checks to see if the username and password are valid
     * Returns the ID if true
     * 
     * @return mixed Boolean false if credentials fail, webuser id if credentials are valid
     */
    static function checkCredentials($argUsername, $argPassword)
    {
        $project    = wcmProject::getInstance();
        $connector  = $project->datalayer->getConnectorByReference("biz");
        $db = $connector->getBusinessDatabase();
        $query = 'SELECT id FROM biz_webuser WHERE username=? AND password=?';
        $pw = md5($argPassword);
        $id = $db->executeScalar($query, array($argUsername, $pw));
        if (!$id)
        {
            return false;
        } else {
            return $id;
        }
    }
    
    /**
     * Updated the lastLogin date of the webuser.
     * 
     */
    public function loggedIn()
    {
        $query = 'UPDATE biz_webuser SET lastLogin=NOW() WHERE id=?';
        $project    = wcmProject::getInstance();
        $connector  = $project->datalayer->getConnectorByReference("biz");
        $db = $connector->getBusinessDatabase();
        $db->executeStatement($query, array($this->id));        
    }

    /**
     * Gets the number of contributions related to this user.
     *
     * @return int The number of contributions related to the object
     */
    public function getContributionCount()
    {
        // recover the business database to execute the query
        $connector = $this->getProject()->datalayer->getConnectorByReference("biz");
        $businessDB = $connector->getBusinessDatabase();

        $sql  = 'SELECT COUNT(*) FROM #__contribution WHERE';
        $sql .= ' webuserId=?';

        $params = array($this->id);

        return $businessDB->executeScalar($sql, $params);
    }    
    
    /**
     * Get all the contributions made by this webuser.
     * 
     * Returns an array populated with {@link contribution} instances.
     *
     * @see contribution
     * @return array
     */    
    public function getContributions()
    {
        $where  = "webuserId='".$this->id."'";
        return bizobject::getBizobjects("contribution", $where);
    }
    
    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */
    public function checkValidity()
    {
        if (!parent::checkValidity())
            return false;

        if (trim(' ' . $this->username) == '')
        {
            $this->lastErrorMsg = _BIZ_ERROR_USERNAME_IS_MANDATORY;
            return false;
        }
        
        if (strlen($this->username) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_USERNAME_TOO_LONG;
            return false;
        }
        
        if ($this->email && strlen($this->email) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_EMAIL_TOO_LONG;
            return false;
        }
        
        if ($this->firstname && strlen($this->firstname) > 128)
        {
            $this->lastErrorMsg = _BIZ_ERROR_FIRSTNAME_TOO_LONG;
            return false;
        }
        
        if ($this->lastname && strlen($this->lastname) > 128)
        {
            $this->lastErrorMsg = _BIZ_ERROR_LASTNAME_TOO_LONG;
            return false;
        }
        
        if ($this->city && strlen($this->city) > 64)
        {
            $this->lastErrorMsg = _BIZ_ERROR_CITY_TOO_LONG;
            return false;
        }
        
        if ($this->state && strlen($this->state) > 64)
        {
            $this->lastErrorMsg = _BIZ_ERROR_STATE_TOO_LONG;
            return false;
        }
        
        if ($this->country && strlen($this->country) > 64)
        {
            $this->lastErrorMsg = _BIZ_ERROR_COUNTRY_TOO_LONG;
            return false;
        }
        
        if ($this->postalCode && strlen($this->postalCode) > 6)
        {
            $this->lastErrorMsg = _BIZ_ERROR_POSTAL_CODE_TOO_LONG;
            return false;
        }

        if ($this->phone && strlen($this->phone) > 16)
        {
            $this->lastErrorMsg = _BIZ_ERROR_PHONE_TOO_LONG;
            return false;
        }

        return true;
    }
}