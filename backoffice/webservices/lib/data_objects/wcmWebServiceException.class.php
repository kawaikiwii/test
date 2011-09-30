<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceException.class.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * WCM Web Service Exception
 *
 * Thrown by service methods when an error or otherwise exceptional
 * condition occurs.
 */
class wcmWebServiceException extends SoapFault
{
    /**
     * Constructs a new instance given an exception code and an optional
     * list of associated parameters used to format a human-readable
     * message.
     * 
     * Formats the message according to the global message list
     * $wcmWebServiceMessages and the current session language
     * 
     * If there is no current session language - eg. when a service
     * method is called without having called the login service method
     * first - or the current session language is not supported, uses
     * the project configured language.
     *
     * @param string       $code The exception code
     * @param string[] ... The optional list of associated parameters
     */
    public function __construct($code /* ... */)
    {
        global $wcmWebServiceMessages;

        $format = null;
        if (isset($wcmWebServiceMessages[$code]))
        {
            $formats = $wcmWebServiceMessages[$code];

            $language = wcmSession::getInstance()->getLanguage();
            if (!$language || !isset($formats[$language]))
            {
                $config = wcmConfig::getInstance();
                $language = $config['wcm.default.language'];
            }

            if (isset($formats[$language]))
            {
                $format = $formats[$language];
            }
        }

        $parameters = array_slice(func_get_args(), 1);
        if ($format)
        {
            $message = vsprintf($format, $parameters);
        }
        else
        {
            $message = $code . ': ' . implode(', ', $parameters);
        }

        parent::__construct($code, $message);
    }
}

?>