<?php
/**
 * Project:     WCM
 * File:        business/tasks/generate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * 
 * This task generate a rule (generationSet, generation or generationContent)
 *
 * When a task is execute the WCM api is automatically loaded
 * and the following global variables are available
 *  $session    The current wcmSession instance
 *  $project    The current wcmProject instance
 *  $config     The current wcmConfig instance
 *  $logger     The default logger used to trace message and error
 *  $parameters An assoc array of extra parameters
 *
 *
 * IMPORTANT: all output should be done through the $logger and fatal error
 * should be raised using the trigger_error() method
 */

    // check rule
    $rule = getArrayParameter($parameters, 'rule');
    if (!$rule)
    {
        $logger->logWarning(_INVALID_GENERATION_RULE);
        exit;
    }

    // instanciate generator with task's logger
    $generator = new wcmTemplateGenerator($logger);
    $startTime = microtime(true);
    //
    // find out what is the rule to generate as the expected format
    // is "generationSetId[:generationId[:generationContentId]]"
    //
    $parts = explode(':', $rule);
    switch(count($parts))
    {
        case 3:
            $id = intval($parts[2]);
            $logger->logMessage(_EXECUTING_GENERATION_CONTENT . ' ' . $id);
            $generator->executeGenerationContent($id);
            break;

        case 2:
            $id = intval($parts[1]);
            $logger->logMessage(_EXECUTING_GENERATION . ' ' . $id);
            $generator->executeGeneration($id);
            break;

        case 1:
            $id = intval($parts[0]);
            $logger->logMessage(_EXECUTING_GENERATION_SET . ' ' . $id);
            $generator->executeGenerationSet($id);
            break;

        default:
            $logger->logWarning(_INVALID_GENERATION_RULE . ' : ' . $rule);
            break;
    }

    // display duration
    $duration = microtime(true) - $startTime;
    $logger->logMessage(sprintf(_GENERATION_COMPLETED_IN_X_SECONDS, $duration));

    unset($generator);