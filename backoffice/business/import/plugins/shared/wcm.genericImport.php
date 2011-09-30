<?php

/**
 * Project:     WCM
 * File:        genericImport.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Abstract class
 * 
 * Generic import class that all import plugins extend 
 */
abstract class wcmGenericImport
{
    /**
    * Logger
    * 
    * @var wcmLogger
    */
    protected $logger;
  
    /**
    * (array) a generic associative array to transmit extra parameters to inherited classes
    */
    protected $parameters;
  
    /**
    * (bool) set this property to TRUE to cancel import process (checked within each process loop)
    */
    protected $cancelled = false;
    
    /**
     * FRFR logFile
     * 
     */
 	protected $logFile = null;
 	
 	protected $polling = false;
 	
 	protected $total;
 	
 	protected $totalProcessed;
  
   /**
    * Constructor
    *
    * @param array $parameters A list of parameters for the plugin
    */
    public function __construct(array $parameters)
    {
        $this->parameters = (is_array($parameters)) ? $parameters : array();
        $this->logFile = (isset($parameters['logFile']))? $parameters['logFile'] : 'import_'.date('YmdHis');
        if (!isset($parameters['logger']) || !($parameters['logger'] instanceof wcmLogger))
        {
            $this->logger = new wcmLogger(true, true, WCM_DIR.'/logs/traces/'.$this->logFile, false);
        } else {
            $this->logger = $parameters['logger'];
        }
    }

    /**
     * Returns current logger
     * @return wcmLogger Current logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Set current logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
    
    /*
    * Launch import process
    *
    */
    abstract public function process();
    
    public function importFromObj(wcmObject $argObj, array $argParams = array())
    {
        return true;
    }
    
    public function importFromXML($argXml, array $argParams = array())
    {
        return true;
    }
    
    public function enablePolling()
    {
        $this->polling = true;
        $this->pollFile = $this->logFile.'_polling.log';
        $this->logger->logMessage('Enabled polling to :'.$this->pollFile);
        touch(WCM_DIR.'/logs/traces/'.$this->pollFile);
    }
    
    public function writePollData($argPercent)
    {
        if ($this->polling)
        {
        $fp = fopen(WCM_DIR . '/logs/traces/'.$this->pollFile, 'wb+');
        fwrite($fp, $argPercent);
        fclose($fp);
        }
    }

    /**
    * This method is used to notify import that processing should be cancel
    *
    * @param string $message An optional message to log before cancelling
    */
    protected function cancelImport($message = null)
    {
        if ($message) $this->logger->logInfo($message);
        $this->cancelled = true;
    }
    
    abstract public function getTotal();
}
?>
