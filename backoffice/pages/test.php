<?php

    include('includes/header.php');

    $key = getArrayParameter($_REQUEST, 'key', 'sample');
    $command = getArrayParameter($_REQUEST, 'cmd');
    if ($command == 'start')
    {
        $task = wcmTaskManager::addNewTask($key, 'My task '.$key, $key);
    }
    else
    {
        $task = wcmTaskManager::getTask($key);
        if ($command == 'abort')
        {
            wcmTaskManager::abortTask($key);
        }
    }
    
    echo '<h2> Task ' . $key . '</h2>';
    if ($task)
    {
        echo '<h3>' . $task->name . ' => ' . $task->getStatusAsString() . '</h3>';
        echo '<div style="overflow:scroll; height:300px; border:1px solid black">';
        echo str_replace(PHP_EOL, "\n<br/>", $task->getOutput());
        echo '</div><br/>';
        echo '<div style="overflow:scroll; height:300px; border:1px solid black">';
        echo str_replace(PHP_EOL, "\n<br/>", $task->getError());
        echo '</div><br/>';
    }
    elseif($command == 'start')
    {
        echo '<h3>A task with same key is already running!</h3>';
    }
    else
    {
        echo '<h3>No such task</h3>';
    }
    
    include('includes/footer.php');