<?php
/**
 * Project:     WCM
 * File:        modules/log/trace.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    $log = readEndOfFile(wcmProject::getInstance()->logger->getFile(), 8192);
    $lines = array_reverse(explode("\n", $log));

    echo '<ul id="log">';
    foreach($lines as $line)
    {
        $kind = substr($line, 0, 3);
        $line = substr($line, 5);
        if ($line)
        {
            switch($kind)
            {
                case 'ERR':
                case 'WRN':
                case 'MSG':
                case 'VRB':
                    echo '<li class="'.$kind.'">'.htmlentities($line).'</li>';
                    break;
                default:
                    echo htmlentities($line).'<br/>';
            }
        }
    }
    echo '</ul>';

    unset($lines);
    unset($log);