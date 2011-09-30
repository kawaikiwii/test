<?php

/**
 * Project:     WCM
 * File:        webservices/config.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Web service class paths (relative to the 'webservices' directory)
 */
$wcmWebServiceClassPaths = array(
    'lib/data_objects',
    'lib/services',
    'lib/soap',
    'lib',
);

/**
 * Web service classes
 */
$wcmWebServiceClasses = array(
    'wcmUserAuthenticationWebService',
    'wcmObjectManagementWebService',
    'wcmBusinessObjectManagementWebService',
    'wcmContentGenerationWebService',
    'wcmBusinessSearchWebService',
    'wcmNserverWebService'
);

/**
 * Web service structure classes
 */
$wcmWebServiceStructureClasses = array(
    'wcmWebServiceFacetValue'       => 'wcmWebServiceFacetValue',
    'wcmWebServiceGeneratedContent' => 'wcmWebServiceGeneratedContent',
    'wcmWebServiceNameValuePair'    => 'wcmWebServiceNameValuePair',
);

?>