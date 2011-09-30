<?php

/**
 * Project:     WCM
 * File:        webservices/service.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

// Initialize the Web services
require_once 'init.php';

// Get the service class name from the request
$className = isset($_GET['class']) ? $_GET['class'] : null;
if (!$className)
{
    //throw new wcmWebServiceException(WCM_WS_EXC_MISSING_SERVICE_CLASS_NAME);
}
elseif (!in_array($className, $wcmWebServiceClasses) && !in_array($className, $wcmWebServiceStructureClasses))
{
    throw new wcmWebServiceException(WCM_WS_EXC_INVALID_SERVICE_CLASS_NAME, $className);
}

// Handle the service request using WSHelper
$wsHelper = new WSHelper('http://wcm.nstein.com', $className);
$wsHelper->actor = 'http://wcm.nstein.com';
$wsHelper->classNameArr = $wcmWebServiceClasses;
$wsHelper->structureMap = $wcmWebServiceStructureClasses;
$wsHelper->use = SOAP_ENCODED;
$wsHelper->setPersistence(SOAP_PERSISTENCE_SESSION);
$wsHelper->setWSDLCacheFolder('wsdl/'); // trailing '/' required

try
{
    if ($className)
        $wsHelper->handle();
    else
        $wsHelper->createDocumentation();
}
catch (wcmWebServiceException $wcmE)
{
    $wsHelper->fault($wcmE->getCode(), $wcmE->getMessage(), '', $wcmE->__toString());
}
catch (exception $e)
{
    $wcmE = new wcmWebServiceException(WCM_WS_EXC_SYSTEM_ERROR, $e->getCode(), $e->getMessage());
    $wsHelper->fault($wcmE->getCode(), $wcmE->getMessage(), '', $wcmE->__toString());
}

?>