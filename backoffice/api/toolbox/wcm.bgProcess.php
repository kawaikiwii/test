<?php

/**
 * Project:     WCM
 * File:        api/wcm.toolbox_process.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Background process directory.
 */
DEFINE('WCM_BG_PROCESS_DIR', WCM_DIR . '/bgprocesses');

/**
 * Path invalid-characters regular expression.
 * @see getOutputFilePaths()
 */
DEFINE('WCM_BG_PROCESS_INVALID_PATH_CHARS_RE', '/[^A-Za-z0-9_.-]/');

/**
 * Path invalid-characters replacement character.
 * @see getOutputFilePaths()
 */
DEFINE('WCM_BG_PROCESS_INVALID_PATH_CHARS_REPLACEMENT', '_');

/**
 * Background process-related utility functions.
 */
class wcmBgProcess
{
    /**
     * Executes a given PHP script in the background using the PHP
     * interpreter command defined by constant PHP_INTERPRETER_CMD.
     *
     * A given PHP script named (for example) 'myScript' is assumed to
     * reside in a file named 'myScript.php' in the directory
     * specified by constant BG_PROCESS_DIR.
     *
     * @param string $scriptName Name of the PHP script to execute
     * @param string $stdout     File to use to capture standard output
     * @param string $stderr     File to use to capture standard error
     * @param mixed  ...         Arguments to pass to the script on the command line
     */
    public static function execScript($scriptName, $stdout, $stderr /*, ... */)
    {
        $scriptPath = WCM_BG_PROCESS_DIR . '/' . $scriptName . '.php';
        $scriptArgs = array_slice(func_get_args(), 3);

        $project = wcmProject::getInstance();
        $config = wcmConfig::getInstance();

        //add the path to php.exe and php.ini to use
        $command = '"' . $config['php.exe'] . '" -q -c "'.$config['php.ini'].'" -f ' . escapeshellarg($scriptPath);
        if ($scriptArgs)
        {
            foreach ($scriptArgs as $scriptArg)
            {
                $command .= ' ' . escapeshellarg($scriptArg);
            }
        }

        if ($stdout)
        {
            $command .= ' >' . escapeshellarg($stdout);
        }

        if ($stderr)
        {
            if ($stderr == $stdout)
            {
                $command .= ' 2>&1';
            }
            else
            {
                $command .= ' 2>' . escapeshellarg($stderr);
            }
        }

        self::execCommand($command);
    }

    /**
     * Executes a given command in the background.
     *
     * @param string $command The command to execute
     */
    public static function execCommand($command)
    {
        // Do the right thing depending on the OS type
        if (strtoupper(substr(php_uname(), 0, 7)) == 'WINDOWS')
        {
            $resource = @popen('start /b "" ' . $command, 'r');
            if ($resource)
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
     * Kills a given process.
     *
     * @param int $pid The PID of the process to kill
     */
    public static function kill($pid)
    {
        if ($pid)
        {
            // Do the right thing depending on the OS type
            if (strtoupper(substr(php_uname(), 0, 7)) == 'WINDOWS')
            {
                exec('taskkill /f /pid ' . $pid);
            }
            else
            {
                exec('kill -9 ' . $pid);
            }
        }
    }

    /**
     * Gets the absolute paths of the background process output files
     * for a given process group and process name:
     *
     * array(
     *     'log' => <log-file>
     *     'out' => <standard-output>
     *     'err' => <standard-error>
     * )
     *
     * Replaces any character in the process group and name that is *not*
     * in the set defined by PATH_INVALID_CHARS_RE with the value of
     * PATH_INVALID_CHARS_REPLACEMENT.
     *
     * @param string $group The process group
     * @param string $name  The process name
     *
     * @return array The output file paths
     */
    public static function getOutputFilePaths($group, $name)
    {
        $config = wcmConfig::getInstance();

        $group = preg_replace(WCM_BG_PROCESS_INVALID_PATH_CHARS_RE,
                              WCM_BG_PROCESS_INVALID_PATH_CHARS_REPLACEMENT, $group);
        $name  = preg_replace(WCM_BG_PROCESS_INVALID_PATH_CHARS_RE,
                              WCM_BG_PROCESS_INVALID_PATH_CHARS_REPLACEMENT, $name);

        $folder = $config['wcm.logging.path'] . 'tasks/';
        $prefix = $folder . $group . '_' . $name;

        //create directory?
        if(!is_dir($folder))
            mkdir($folder, 0777);

        return array(
            'log' => $prefix . '.log',
            'out' => $prefix . '.out',
            'err' => $prefix . '.err',
            );
    }

    /**
     * Gets the session key for a given process group.
     *
     * @param string $group The process group
     *
     * @return string The session key
     */
    public static function getGroupSessionKey($group)
    {
        return 'bgprocess:' . $group;
    }
}

?>