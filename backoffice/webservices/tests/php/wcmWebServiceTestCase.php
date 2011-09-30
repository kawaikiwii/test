<?php

/**
 * Project:     WCM
 * File:        wcmWebServiceTestCase.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

require_once dirname(__FILE__) . '/testCase.php';

/**
 * WCM Web Service Test Case
 */
abstract class wcmWebServiceTestCase extends testCase
{
    /**
     * The associated SOAP client.
     *
     * @var SoapClient
     */
    protected $soapClient;
    
    /**
     * Gets a SOAP client for a service given its class name.
     *
     * @param string $service The class name of the service
     * 
     * @return SoapClient
     */
    protected function getSoapClient($serviceName)
    {
        $wsdl = 'http://localhost/wcm30/webservices/service.php?class='.$serviceName.'&wsdl';
        try
        {
            return new SoapClient($wsdl, array());
        }
        catch (Exception $e)
        {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @see parent::setup()
     */
    protected function setup($testName)
    {
        $className = get_class($this);
        $serviceName = substr($className, 0, strlen($className) - strlen('TestCase'));
        $this->soapClient = $this->getSoapClient($serviceName);
    }

    /**
     * @see parent::tearDown()
     */
    protected function tearDown($testName)
    {
        unset($this->soapClient);
    }
}

?>