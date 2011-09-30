<?php
/**
 * Project:     WCM
 * File:        api/task/wcm.taskManager.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * This class helps managing task (background process)
 * and use the session cache
 */
class wcmTaskManager
{
    /**
     * Returns the list of active tasks
     *
     * @return array An assoc array ($key => wcmTask)
     */
    static function getTasks()
    {
        return getArrayParameter($_SESSION, '_wcmTaskManager', array());
    }
    
    /**
     * Returns the current task status
     *
     * @param string $key Unique key representing the task
     *
     * @return int The task status or null if there is no such task
     */
    static function getTaskStatus($key)
    {
        $task = self::getTask($key);
        return ($task == null) ? null : $task->getStatus();
    }

    /**
     * Returns a task by its unique key
     *
     * @param string $key Unique key representing the task
     *
     * @return wcmTask The wcmTask object of null when not found
     */
    static function getTask($key)
    {
        return getArrayParameter(self::getTasks(), $key);
    }

    /**
     * Removes an existing task (and abort it if needed)
     *
     * @param string $key Unique key representing the task
     *
     * @return wcmTask The aborted task
     */
    static function abortTask($key)
    {
        $tasks = self::getTasks();
        
        $oldTask = getArrayParameter($tasks, $key);
        if ($oldTask)
        {
            // abort task is needed
            $status = $oldTask->getStatus();
            if ($status == wcmTask::READY || $status == wcmTask::STARTED)
            {
                $oldTask->abort();
            }
        }
        
        unset($tasks[$key]);
        $_SESSION['_wcmTaskManager'] = $tasks;
        
        return $oldTask;
    }
    
    /**
     * Add a wcmTask to the task manager and start it automatically
     *
     * @param string $key        A unique identifier representing the task
     * @param string $name       A user-friendly name describing the task
     * @param string $process    PHP process to execute (match file "business/tasks/{$process}.php")
     * @param array  $parameters An optional assoc array of parameters
     *
     * @return wcmTask The wcmTask object added to the task manager or null if a
     *                 task with same key exists and is running (i.e. not aborted nor ended)
     */
    static function addTask(wcmTask $task)
    {
        $tasks = self::getTasks();
        $key = $task->getUID();
        
        // check if a task already exists with same key and is started
        $oldTask = getArrayParameter($tasks, $key);
        if ($oldTask)
        {
            $status = $oldTask->getStatus();
            if ($status == wcmTask::STARTING || $status == wcmTask::STARTED)
                return null;
        }

        $tasks[$key] = $task;
        $task->start();

        $_SESSION['_wcmTaskManager'] = $tasks;

        return $task;
    }
    
    /**
     * Add a new task to the task manager and start it automatically
     *
     * @param string $key        A unique identifier representing the task
     * @param string $name       A user-friendly name describing the task
     * @param string $process    PHP process to execute (match file "business/tasks/{$process}.php")
     * @param array  $parameters An optional assoc array of parameters
     *
     * @return wcmTask The wcmTask object added to the task manager or null if a
     *                 task with same key exists and is running (i.e. not aborted nor ended)
     */
    static function addNewTask($key, $name, $process, array $parameters = array())
    {
        $task = new wcmTask($key, $name, $process, $parameters);
        return self::addTask($task);
    }
}
