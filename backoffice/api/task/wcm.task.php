<?php
/**
 * Project:     WCM
 * File:        api/task/wcm.task.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class represent a task that can be launch
 * by the task manager (@see wcmTaskManager)
 */
class wcmTask
{
    private $uid;
    private $status;
    private $parameters;
    private $lastErrorMsg;
    private $pidfile;
    private $stdout;
    private $stderr;

    /**
     * Status 'READY' when the task is ready to start
     */
    const READY = 1;
    
    /**
     * Status 'STARTING' when the task is starting
     */
    const STARTING = 2;

    /**
     * Status 'STARTED' when the task is running
     */
    const STARTED = 3;
    
    /**
     * Status 'ENDED' when the task has ended normally
     */
    const ENDED = 4;
    
    /**
     * Status 'ABORTED' when the task has been aborted
     */
    const ABORTED = 5;

    /**
     * Status 'FAILED' when the task initialization has failed
     */
    const FAILED = 6;


    /**
     * (string) A user-friendly string representing the task
     */
    public $name;

    /**
     * (string) Path to the PHP file representing the process to execute
     */
    public $process;
    
    /**
     * (date) When the task has started (or null when not started)
     */
    public $startedAt;

    /**
     * (date) When the task has ended (or null while the task is running)
     */
    public $endedAt;
    
    /**
     * Builds a new task
     *
     * @param string $uid        A unique identifier representing the task
     * @param string $name       A user-friendly name describing the task
     * @param string $process    PHP process to execute (match file "business/tasks/{$process}.php")
     * @param array  $parameters An optional assoc array of parameters
     */
    public function __construct($uid, $name, $process, array $parameters = array())
    {
        $this->uid = ($uid != null) ? $uid : 'task_'.uniqid();
        $this->name = ($name != null) ? $name : $this->uid;
        $this->process = WCM_DIR . '/business/tasks/'.$process.'.php';
        $this->parameters = $parameters;
        $this->status = self::READY;
    }

    /**
     * Starts the task's process
     *
     * @return boolean TRUE on success, FALSE on failure
     *
     * Note: When an error occurs, the last error is available through getErrorMsg() method
     */
    public function start()
    {
        $project = wcmProject::getInstance();
        $session = wcmSession::getInstance();
        $config  = wcmConfig::getInstance();

        // check status
        if ($this->status == self::STARTING || $this->status == self::STARTED)
        {
            $this->lastErrorMsg = 'Task is already started!';
            return false;
        }
        
        $this->lastErrorMsg = null;
        
        // start process
        $this->startedAt = date('Y-m-d H:i:s');
        $this->status = self::STARTING;
        
        // prepare command to execute php.exe with its php.ini
        $bootstrap = dirname(__FILE__) . '/wcm.taskBootstrap.php';
        $command = '"' . $config['php.exe'] . '" -q -c "'.$config['php.ini'].'" -f ' . escapeshellarg($bootstrap);

        // prepare output folder (for pid, stdout and stderr)
        $path = $config['wcm.logging.path'] . 'tasks/' . $this->uid . '/';
        if (!is_dir($path))
        {
           @mkdir($path, 0755, true);
        }
        $this->pidfile = $path . 'pid.txt';
        $this->stdout = $path . 'stdout.txt';
        $this->stderr = $path . 'stderr.txt';

        // add bootstrap parameters
        $parameters['_wcmProcess'] = $this->process;
        $parameters['_wcmLogPath'] = $path;
        $parameters['_wcmLanguage'] = $session->getLanguage();
        $parameters['_wcmSiteId'] = $session->getSiteId();
        $parameters['_wcmParams'] = ($this->parameters == null) ? null : base64_encode(serialize($this->parameters));
        
        // add extra parameters
        foreach ($parameters as $arg => $val)
        {
            $command .= ' ' . $arg . '=' . escapeshellarg($val);
        }

        // set stdout and stderr
        $command .= ' >' . escapeshellarg($this->stdout);
        $command .= ' 2>' . escapeshellarg($this->stderr);

        // log launching
        $project->logger->logMessage('Starting task ' . $this->uid);
        $project->logger->logVerbose('Starting task ' . $this->uid . ' => ' . $command);
        
        // launch process (OS-dependant)
        if (strtoupper(substr(php_uname(), 0, 7)) == 'WINDOWS')
        {
            $resource = @popen('start /b "" ' . $command, 'r');
            if (is_resource($resource))
            {
                pclose($resource);
            }
        }
        else
        {
            exec($command . ' &');
        }
    }

    /**
     * Abort the current task's process
     *
     * @return boolean TRUE on success, FALSE on failure
     *
     * Note: When an error occurs, the last error is available through getErrorMsg() method
     */
    public function abort()
    {
        $pid = $this->getPID();
        if ($pid)
        {
            // log killing
            wcmProject::getInstance()->logger->logMessage('Killing task ' . $this->uid);

            // kill process (OS-dependant)
            if (strtoupper(substr(php_uname(), 0, 7)) == 'WINDOWS')
            {
                exec('taskkill /f /pid ' . $pid);
            }
            else
            {
                exec('kill -9 ' . $pid);
            }
        }
        
        $this->lastErrorMsg = null;
        $this->endedAt = date('Y-m-d H:i:s');
        $this->status = self::ABORTED;
    }

    /**
     * Returns the last error message
     *
     * @return string A descriptive error message (or null)
     */
    public function getErrorMsg()
    {
        return $this->lastErrorMsg;
    }
    
    /**
     * Returns the parameters associated with the task's process
     *
     * @return array An assoc array of parameters passed to the task's process
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Refresh then current task status
     *
     * @return int One of wcmTask::READY, wcmTask::STARTED, wcmTask::ENDED or wcmTask::ABORTED
     */
    public function getStatus()
    {
        // starting? wait a while...
        if ($this->status == self::STARTING)
            sleep(1);
        
        // aborted?
        if ($this->status == self::ABORTED)
            return $this->status;

        // check error output           
        $err = $this->getError();
        if ($err === FALSE)
        {
            // running?
            if ($this->getPID() == 0)
            {
                $this->status = self::ENDED;
            }
            else
            {
                $this->status = self::STARTED;
            }
        }
        else
        {
            // ended with error
            $this->status = self::FAILED;
        }

        return $this->status;
    }

    /**
     * Returns a human-friendly string representing
     * the task status
     *
     * @param int $status The status (or null to refresh status)
     */
    public function GetStatusAsString($status = null)
    {
        if (!$status) $status = $this->getStatus();
        
        switch($status)
        {
            case self::STARTING: return _TASK_STATUS_STARTING;
            case self::STARTED:  return _TASK_STATUS_STARTED;
            case self::ENDED:    return _TASK_STATUS_ENDED;
            case self::ABORTED:  return _TASK_STATUS_ABORTED;
            case self::FAILED:   return _TASK_STATUS_FAILED;
        }
        
        return _TASK_STATUS_READY;
    }
    
    /**
     * Returns the task unique identifier
     *
     * @return string A unique identifier representing the task
     */
    public function getUID()
    {
        return $this->uid;
    }

    /**
     * Returns the task process identifier
     *
     * @return int The task's process identifier (or zero when no process is running)
     */
    public function getPID()
    {
        $pid = @file_get_contents($this->pidfile);
        if ($pid === FALSE) return 0;

        return intval($pid);
    }

    /**
     * Returns the path of the file when the task's process standard output is written
     *
     * @return string Path to the file containing the stdout
     */
    public function getOutputFile()
    {
        return $this->stdout;
    }
    
    /**
     * Returns the standard output flow of the task's process
     *
     * @return string Standard output
     */
    public function getOutput()
    {
        $size = @filesize($this->stdout);
        if (!$size) return FALSE;
        
        $output = @file_get_contents($this->stdout);

        if ($this->status == self::ABORTED)
        {
            $output .= 'WRN: [' . date('Y-m-d H:i:s') . '] ' . _TASK_HAS_BEEN_ABORTED . PHP_EOL;
        }
        
        return $output;
    }

    /**
     * Returns the path of the file when the task's process standard error output is written
     *
     * @return string Path to the file containing the stderr
     */
    public function getErrorFile()
    {
        return $this->stderr;
    }
    
    /**
     * Returns the standard error output flow of the task's process
     *
     * @return string Error output (or FALSE when file does not exist)
     */
    public function getError()
    {
        $size = @filesize($this->stderr);
        if (!$size) return FALSE;
        
        return @file_get_contents($this->stderr);
    }
}