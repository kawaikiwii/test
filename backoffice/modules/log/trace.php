<?php
/**
 * Project:     WCM
 * File:        modules/log/trace.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $log = readEndOfFile(wcmGetTraceFile(), 8192);
    $lines = array_reverse(explode("\n", $log));

    echo '<ul id="log">';
    foreach($lines as $line)
    {
        if (trim($line) != '')
            echo '<li class="MSG">'.htmlentities($line).'</li>';
    }
    echo '</ul>';

    unset($lines);
    unset($log);