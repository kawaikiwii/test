<?php
/**
 * Project:     WCM
 * File:        dialogs/generate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Dialogs enabling generation lauching and monitoring
 * Generations are launch in background (using task manager)
 */

    // initialize system
    require_once dirname(__FILE__).'../../initWebApp.php';
    include(WCM_DIR . '/pages/includes/header_popup.php');

    // retrieve parameters
    $rule = getArrayParameter($_REQUEST, 'rule');
    $command = getArrayParameter($_REQUEST, 'command');

    // display list of available generation rules
    echo '<div class="description">';
    echo '<h2>' . _SELECT_GENERATION_RULE . '</h2>';
    echo '<form name="wcmForm" id="wcmForm" action="#bottom" method="get">';
    echo '<input type="hidden" id="command" name="command" value="refresh"/>';

    // build hierarchy of generation rules
    echo '<select name="rule" id="rule" onchange="$(\'wcmForm\').submit();">';
    echo '<option value="">(' . _NONE . ')</option>';
    foreach($project->generator->getGenerationSets() as $generationSet)
    {
        if ($session->isAllowed($generationSet, wcmPermission::P_EXECUTE))
        {
            $key = $generationSet->id;
            echo '<option value="' . $key . '"';
            if ($key == $rule) echo ' selected="selected"';
            echo '>' . getConst($generationSet->name) . '</option>' . PHP_EOL;
            foreach($generationSet->getGenerations() as $generation)
            {
                $key = $generationSet->id . ':' . $generation->id;
                echo '<option value="' . $key . '"';
                if ($key == $rule) echo ' selected="selected"';
                echo '> :: ' . getConst($generation->name) . '</option>' . PHP_EOL;
                foreach($generation->getContents() as $generationContent)
                {
                    $key = $generationSet->id . ':' . $generation->id . ':' . $generationContent->id;
                    echo '<option value="' . $key . '"';
                    if ($key == $rule) echo ' selected="selected"';
                    echo '> :: :: ' . getConst($generationContent->name) . '</option>' . PHP_EOL;
                }
            }
        }
    }
    echo '</select>';
    echo '</form>';
    echo '</div>';



    // check status of existing task and/or start new task
    if ($rule)
    {
        // search for similar task in the task manager
        $key = str_replace(':', '-', 'generationRule:'.$rule);
        $task = wcmTaskManager::getTask($key);
        if ($task || $command == 'start')
        {
            // check for special command ('start' or 'abort')
            switch($command)
            {
                case 'start':
                $task = wcmTaskManager::addNewTask(
                                    $key, 
                                    'Generation rule ' . $rule,
                                    'generate',
                                    array('rule' => $rule));
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
                    $toolbar  = '<li><a class="start" href="#" onclick="$(\'wcmForm\').command.value=\'start\';$(\'wcmForm\').submit();">' . _START . '</a></li>';
                    $toolbar .= '<li><a class="refresh" href="#" onclick="$(\'wcmForm\').command.value=\'refresh\';$(\'wcmForm\').submit();">' . _REFRESH . '</a></li>';
                    break;
            }
            $status = $task->getStatusAsString($status);
        }
        else
        {
            $status = _TASK_STATUS_READY;
            $toolbar  = '<li><a class="start" href="#" onclick="$(\'wcmForm\').command.value=\'start\';$(\'wcmForm\').submit();">' . _START . '</a></li>';
            $toolbar .= '<li><a class="refresh" href="#" onclick="$(\'wcmForm\').command.value=\'refresh\';$(\'wcmForm\').submit();">' . _REFRESH . '</a></li>';
        }
    }
    else
    {
        // no rule selected
        $task = null;
        $status = '(' . _SELECT_GENERATION_RULE . ')';
        $toolbar = '<li><a class="refresh" href="#" onclick="$(\'wcmForm\').command.value=\'refresh\';$(\'wcmForm\').submit();">' . _REFRESH . '</a></li>';
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