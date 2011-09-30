<?php
/**
 * Project:     WCM
 * File:        dialogs/import.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * This dialog display an import process status by
 * lauching and monitoring a background task
 */

    // initialize system
    require_once dirname(__FILE__).'../../initWebApp.php';
    include(WCM_DIR . '/pages/includes/header_popup.php');

    // retrieve parameters
    $plugin = getArrayParameter($_REQUEST, 'plugin');
    $command = getArrayParameter($_REQUEST, 'command', 'start');

    // display list of available generation rules
    echo '<div class="description">';
    echo '<h2>' . _BIZ_IMPORT . '</h2>';
    echo '<form name="wcmForm" id="wcmForm" action="#bottom" method="get">';
    echo '<input type="hidden" name="plugin" value="'. $plugin . '"/>';
    echo '<input type="hidden" name="command" value="refresh"/>';
    echo '</form>';
    echo '</div>';


    // search for similar task in the task manager
    $key = 'import-'.$plugin;
    $task = wcmTaskManager::getTask($key);
    if ($task || $command == 'start')
    {
        // check for special command ('start' or 'abort')
        switch($command)
        {
            case 'start':
               $task = wcmTaskManager::addNewTask(
                                $key, 
                                'Import rule ' . $plugin,
                                'import',
                                $_POST);
                if (!$task)
                {
                    // cannot start task because a similar task is running!
                    $task = wcmTaskManager::getTask($key);
                }
                else
                {
                    // wait a second, so the task start output some message
                    sleep(1);
                }
                break;

            case 'abort':
                $task = wcmTaskManager::abortTask($key);
                break;
        }

        // display task status and available command
        $status = $task->getStatus();
        switch($status)
        {
            case wcmTask::STARTED:
                $toolbar  = '<li><a class="abort" href="#" onclick="$(\'wcmForm\').command.value=\'abort\';$(\'wcmForm\').submit();">' . _ABORT . '</a></li>';
                $toolbar .= '<li><a class="refresh" href="#" onclick="$(\'wcmForm\').command.value=\'refresh\';$(\'wcmForm\').submit();">' . _REFRESH . '</a></li>';
                break;

            default:
                $toolbar = '<li><a class="refresh" href="#" onclick="$(\'wcmForm\').command.value=\'refresh\';$(\'wcmForm\').submit();">' . _REFRESH . '</a></li>';
                break;
        }
        $status = $task->getStatusAsString($status);
    }
    else
    {
        // invalid plugin?
        $status = '';
        $toolbar = '';
    }

    // display available commands and current status
    echo '<div class="status">';
    echo '<ul>' . $toolbar . '</ul>';
    echo _TASK_CURRENT_STATUS . '<b>' . $status . '</b>';
    echo '</div>';
    
    // display task log
    echo '<div class="log">';
    if ($task)
    {
        echo '<a name="top"></a><ul>';
        echo wcmLogger::formatTextLog($task->getOutput());
        echo '</ul><a class="top" name="bottom" href="#top">' . _TOP . '</a>';
    }
    echo '</div>';


    include(WCM_DIR . '/pages/includes/footer.php');