<?php
/**
 * Project:     WCM
 * File:        wcm.logger.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Some constants used to trace messages
 */
define('WCMLOG_ERROR',   1);
define('WCMLOG_WARNING', 2);
define('WCMLOG_MESSAGE', 3);
define('WCMLOG_VERBOSE', 4);
define('WCMLOG_DEBUG',   5);

/**
 * The logger class helps implementation of a simple log tool
 *
 */
class wcmLogger
{
    /**
     * Stack of messages : an array of array ($kind, $time, $message)
     */
    public $messages = array();
    
    protected $verbose;
    protected $debug;
    protected $flushToFile;

    private $previousHandler = null;
    
    /**
     * Constructor
     *
     * @param bool   $verbose       Store verbose message (default true)
     * @param bool   $debug         Display error message (default false when error is handled)
     * @param string $flushToFile   Name of file to flush (default null)
     * @param bool   $handleError   Handle all PHP error (default false)
     */
    public function __construct($verbose = false, $debug = false, $flushToFile = null, $handleError = false)
    {
        $this->debug = $debug;
        $this->verbose = $verbose;
        $this->flushToFile = $flushToFile;
        
        if ($handleError)
            $this->startErrorHandling();
    }
    
    /**
     * Destructor
     */
    function __destruct()
    {
        if ($this->previousHandler != null)
            $this->stopErrorHandling();
        $this->eraseLog();
        $this->messages = null;
    }

    /**
     * Stops error handling
     */
    private function stopErrorHandling()
    {
        if ($this->previousHandler != null)
            restore_error_handler($this->previousHandler);
    }
    
    /**
     * Starts error handling
     */
    private function startErrorHandling()
    {
        $this->previousHandler = set_error_handler(array(&$this, 'errorHandler'));
    }

    /**
     * Custom error handler
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        // Does caller has preceded function invokation with '@' ?
        if (error_reporting() == 0) return;
                     
        // Format error message
        $message = "$errstr : $errfile, line #$errline";
        
        switch ($errno)
        {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_ERROR:
            case E_COMPILE_WARNING:
            case E_USER_ERROR:
                $this->logError($message, $this->debug);
                break;

            case E_WARNING:
            case E_NOTICE:
            case E_USER_WARNING:
                $this->logWarning($message, $this->debug);
                break;

            case E_USER_NOTICE:
                $this->logMessage($message, $this->debug);
                break;

            case E_STRICT:
                $this->logVerbose($message, $this->debug);
                break;

            default:
                // We should never reach this part of the code !
                $this->logError($message, $this->debug);
                break;
        }
    }
        
    /**
     * Erase all messages
     */
    public function eraseLog()
    {
        unset($this->messages);
        $this->messages = array();
    }
    
    /**
     * Adds a new log message into the stack
     *
     * @param int $kind         Log kind (WCMLOG_XXX)
     * @param string $message   Mesage to log
     */
    public function addLog($kind = WCMLOG_MESSAGE, $message)
    {
        // Display verbose message ?
        if (($kind == WCMLOG_VERBOSE) && ($this->verbose == false))
            return;

        // Add message to log stack
        $t = date("Y-m-d H:i:s");
        
        // Flush to file ?
        if ($this->flushToFile)
        {
            $text = null;
            switch($kind)
            {
            case WCMLOG_ERROR:
                $text = "ERR: [$t] $message" . PHP_EOL;
                break;
            case WCMLOG_WARNING:
                $text = "WRN: [$t] $message" . PHP_EOL;
                break;
            case WCMLOG_MESSAGE:
                $text = "MSG: [$t] $message" . PHP_EOL;
                break;
            case WCMLOG_VERBOSE:
                $text = "VRB: [$t] $message" . PHP_EOL;
                break;
            case WCMLOG_DEBUG:
                $text = "DBG: [$t] $message" . PHP_EOL;
                break;
            }

            // Is the output a resource (STDOUT, STDERR)?
            if (is_resource($this->flushToFile))
            {
                fwrite($this->flushToFile, $text);
            }
            else
            {
                // Save text in "append" mode
                saveToFile($this->flushToFile, $text, true);
            }
        }
        else
        {
            // store message in memory
            $this->messages[] = array($t, $kind, $message);
        }
    }
    
    /**
     * Log an error message
     *
     * @param $message Message to log
     */
    public function logError($message)
    {
        $this->addLog(WCMLOG_ERROR, $message);
    }

    /**
     * Log a warning message
     *
     * @param $message Message to log
     */
    public function logWarning($message)
    {
        $this->addLog(WCMLOG_WARNING, $message);
    }

    /**
     * Log an standard message
     *
     * @param $message Message to log
     */
    public function logMessage($message)
    {
        $this->addLog(WCMLOG_MESSAGE, $message);
    }

    /**
     * Log a verbose message
     *
     * @param $message Message to log
     */
    public function logVerbose($message)
    {
        if (!$this->verbose) return;
        $this->addLog(WCMLOG_VERBOSE, $message);
    }

    /**
     * Dump log content into a filename
     *
     * @param string  $filename Destination file name
     * @param boolean $append   Append log content (default false)
     */
    public function dump($filename, $append = false)
    {
        $text = null;
        foreach($this->messages as $message)
        {
            $t = $message[0];
            $n = $message[1];
            $s = $message[2];
            switch($n)
            {
            case WCMLOG_ERROR:
                $text .= "[$t] ERR: $s\r\n";
                break;
            case WCMLOG_WARNING:
                $text .= "[$t] WRN: $s\r\n";
                break;
            case WCMLOG_MESSAGE:
                $text .= "[$t] MSG: $s\r\n";
                break;
            case WCMLOG_VERBOSE:
                $text .= "[$t] VRB: $s\r\n";
                break;
            case WCMLOG_DEBUG:
                $text .= "[$t] DBG: $s\r\n";
                break;
            }
        }
        
        // Save content to file
        saveToFile($filename, $text, $append);
    }
    
    /**
     * Display log content (echo)
     *
     * @param boolean $html Display html rather than raw text (format <li class="???">...</li>)
     * @param boolean $return   Return result instead of echo (default false)
     */
    function display($html = true, $return = false)
    {
        $buffer = null;
        
        foreach($this->messages as $message)
        {
            $t = $message[0];
            $n = $message[1];
            $s = $message[2];

            switch($n)
            {
            case WCMLOG_ERROR:
                if ($html)
                    $buffer .= "<li class='ERR'>[$t] $s</font></li>";
                else
                    $buffer .= "[$t] ERR: $s\r\n";
                break;
            case WCMLOG_WARNING:
                if ($html)
                    $buffer .= "<li class='WRN'>[$t] $s</font></li>";
                else
                    $buffer .= "[$t] WRN: $s\r\n";
                break;
            case WCMLOG_MESSAGE:
                if ($html)
                    $buffer .= "<li class'MSG'>[$t] $s</font></li>";
                else
                    $buffer .= "[$t] MSG: $s\r\n";
                break;
            case WCMLOG_VERBOSE:
                if ($html)
                    $buffer .= "<li class='VRB'>[$t] $s</font></li>";
                else
                    $buffer .= "[$t] VRB: $s\r\n";
                break;
            case WCMLOG_DEBUG:
                if ($html)
                    $buffer .= "<li class'DBG'>[$t] $s</font></li>";
                else
                    $buffer .= "[$t] DBG: $s\r\n";
                break;
            }
        }
        
        // Return buffer instead of echoing it ?
        if ($return) return $buffer;

        // Echo buffer
        echo $buffer;
    }

    /**
     * Returns the file where log is written
     *
     * @return string File where log is written (or null)
     */
    public function getFile()
    {
        return $this->flushToFile;
    }

    /**
     * Format textual log (e.g. ERR: [datetime] {msg} PHP_EOL ....) in a
     * HTML friendly output (<li class="ERR"> [datetime] {msg} </li> ...)
     *
     * @param string $text Text from logger
     *
     * @return string HTML text
     */
    static function formatTextLog($text)
    {
        $output = null;

        $lines = explode("\n", $text);
        foreach($lines as $line)
        {
            $line = trim($line);
            if ($line)
            {
                $parts = explode(':', $line, 2);

                // handle special output of error when php.ini is set to output them!
                if ($parts[0] == 'Fatal error') $parts[0] = 'ERR';

                if (count($parts) == 2)
                {
                    $output .= '<li class="' . $parts[0] . '">' . $parts[1] . '</li>' . PHP_EOL;
                }
            }
        }
        unset($lines);
        
        return $output;
    }
}