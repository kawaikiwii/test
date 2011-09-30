<?php
/**
 * Project:     WCM
 * File:        biz_toolbox_dam.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

class ToolboxDam
{
    public $soapClient;
    public $token;
    public $login;
    public $password;
    public $language = 'US';
    public $webserviceUrl;
    
    /**
     * Constructor
     *
     * @param string $webserviceUrl   DAM host name or IP address
     * @param string $login Login
     * @param string $password  Password
     *
     */
    public function __construct($webserviceUrl, $login, $password)
    {
        $this->login = $login;
        $this->password = $password;
        $this->webserviceUrl = $webserviceUrl;
        $connexion  = array('login' => $this->login, 'password' => $this->password, 'language' => $this->language);
        $this->soapClient = new SoapClient($this->webserviceUrl, $connexion);
        $this->token = $this->soapClient->__soapCall("Authenticate", array('parameters' => $connexion));
        if ($this->token && $this->soapClient)
            return true;
        else
            return false;
    }
    /**
     * 
     * Return an xml which contains the history of a DAM object
     * 
     * @param string $classIdentifier The DAM class of the object
     * @param string $objectId The objectId of the DAM object
     * 
     */
    public function getDamBizObjectHistory($classIdentifier, $objectId)
    {
        $params = array( 'token' => $this->token,
                            'classIdentifier' => $classIdentifier,
                            'id' => $objectId );
        $history = $this->soapClient->__soapCall("GetHistory",array("parameters" => $params));
        if ($history)
        {
            $resultArray = array();
            if (!is_array($history->GetHistoryResult->string)) {
                  $resultArray[0] = $history->GetHistoryResult->string;
                  return $resultArray;
            }
            for ($i = 0; $i <= count($history->GetHistoryResult->string); $i++ )
            {
                $resultArray[$i] = $history->GetHistoryResult->string[$i];
            }
            return $resultArray;
        }
        else
            return false;
    }
    /**
     * 
     * Return an xml which contains a version of a DAM object
     * 
     * @param string $classIdentifier The DAM class of the object
     * @param string $objectId The objectId of the DAM object
     * @param int $version The number version you want to get.
     * 
     */
    
    public function getDamBizObjectVersion($classIdentifier, $objectId, $version)
    {
        $params = array( "token" => $this->token,
                    "classIdentifier" => $classIdentifier,
                    "id" => $objectId,
                    "version" => $version);
        $version = $this->soapClient->__soapCall("GetVersion", array("parameters" => $params));
        if ($version)
           return $version;
        else
           return false;
    }
}
?>
