<?php

/**
 * Project:     WCM
 * File:        webservices/messages.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Localized Web service messages
 */
$wcmWebServiceMessages = array(

    // Exceptions

    'EXC_CHECK_IN_OBJECT_FAILED' => array(
        'en' => "Check-in failed for '%s' object %s: %s",
    ),
    'EXC_CHECK_OUT_OBJECT_FAILED' => array(
        'en' => "Check-out failed for '%s' object %s: %s",
    ),
    'EXC_DEINDEX_OBJECTS_FAILED' => array(
        'en' => "De-indexing '%s' objects failed: %s",
    ),
    'EXC_DELETE_OBJECT_FAILED' => array(
        'en' => "Delete failed for '%s' object %s: %s",
    ),
    'EXC_DELETE_OBJECTS_FAILED' => array(
        'en' => "Delete objects failed for '%s' objects (where '%s'): %s",
    ),
    'EXC_GENERATE_FAILED' => array(
        'en' => "Generation failed for generation %s: %s",
    ),
    'EXC_GENERATE_CONTENT_FAILED' => array(
        'en' => "Content generation failed for generation content %s: %s",
    ),
    'EXC_GENERATE_OBJECT_CONTENT_FAILED' => array(
        'en' => "Content generation failed for '%s' object %s: %s",
    ),
    'EXC_GET_OBJECTS_FAILED' => array(
        'en' => "Get objects failed for '%s' objects (where '%s', order by '%s'): %s",
    ),
    'EXC_GET_FACET_VALUES_FAILED' => array(
        'en' => "Get facet values failed for search '%s': %s",
    ),
    'EXC_GET_SEARCH_RESULTS_FAILED' => array(
        'en' => "Get search results failed for search '%s': %s",
    ),
    'EXC_INDEX_OBJECT_FAILED' => array(
        'en' => "Indexing '%s' object %s failed: %s",
    ),
    'EXC_INIT_SEARCH_FAILED' => array(
        'en' => "Search '%s' failed: %s",
    ),
    'EXC_INITIAL_CHECK_IN_OBJECT_FAILED' => array(
        'en' => "Initial check-in failed for '%s' object: %s",
    ),
    'EXC_INVALID_OBJECT_CLASS' => array(
        'en' => "Invalid object class name '%s'.",
    ),
    'EXC_INVALID_OBJECT_ID' => array(
        'en' => "Invalid object identifier %s.",
    ),
    'EXC_INVALID_OBJECT_ID_OBJECT' => array(
        'en' => "Invalid object identifier %s (refers to invalid object): %s",
    ),
    'EXC_INVaLID_OBJECT_XML' => array(
        'en' => "Invalid object XML '%s': %s",
    ),
    'EXC_INVaLID_OBJECT_XML_OBJECT' => array(
        'en' => "Invalid object XML '%s' (refers to invalid object): %s",
    ),
    'EXC_INVALID_SERVICE_CLASS_NAME' => array(
        'en' => "Invalid service class name '%s' in request.",
    ),
    'EXC_INVALID_SESSION_LANGUAGE' => array(
        'en' => "Invalid session language '%s'.",
    ),
    'EXC_INVALID_SESSION_TOKEN' => array(
        'en' => "Invalid session token '%s'.",
    ),
    'EXC_INVALID_SESSION_TOKEN_ID' => array(
        'en' => "Invalid session token '%s' (refers to invalid session).",
    ),
    'EXC_INVALID_USER_ID_OR_PASSWORD' => array(
        'en' => "Invalid user identifier or password for user '%s'.",
    ),
    'EXC_MISSING_SERVICE_CLASS_NAME' => array(
        'en' => "Missing service class name in request.",
    ),
    'EXC_REINDEX_OBJECTS_FAILED' => array(
        'en' => "Re-indexing '%s' objects failed: %s",
    ),
    'EXC_SESSION_EXISTS_DIFFERENT_CREDENTIALS' => array(
        'en' => "Invalid user identifier '%s' (session already exists with different credentials).",
    ),
    'EXC_SYSTEM_ERROR' => array(
        'en' => "Internal system error: [%s] %s",
    ),
    'EXC_UNDO_CHECK_OUT_OBJECT_FAILED' => array(
        'en' => "Undo check out object failed for '%s' object %s: %s",
    ),
);

// Define a WCM_WS_ constant for each localized message
if ($wcmWebServiceMessages)
{
    foreach ($wcmWebServiceMessages as $messageCode => $message)
    {
        $wcmWsMessageCode = 'WCM_WS_' . $messageCode;
        define($wcmWsMessageCode, $messageCode);
    }
}

?>