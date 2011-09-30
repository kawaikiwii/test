<?php

/**
 * Project:     WCM
 * File:        wcm.textml.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class helps querying the textml webservice
 *
 */
class wcmTextmlServer
{
    private $logger;
    private $webserviceURL;

    /**
     * Textml server name
     */
    public $serverName = null;

    /**
     * Textml docbase name
     */
    public $docbaseName = null;

    /**
     * Number of documents within the docbase
     */
    public $documentCount = 0;

    /**
     * Index list (an associative array "name" => indexName, "kind" => kind, "property" => true|false)
     */
    public $indexList = null;

    /**
     * XLS list (simple string array)
     */
    public $xslList = null;

    /**
     * Last error message
     */
    public $lastError = null;

    /**
     * Constructor
     *
     * @param string $webserviceURL URL of Textml WebService
     * @param bool $refresh TRUE to invoke 'refresh' method (default value is TRUE)
     * @param wcmLogger $logger Logger (or null to create one)
     */
    public function __construct($webserviceURL, $refresh = true, $logger = null)
    {
        $this->webserviceURL = $webserviceURL;

        if ($logger)
        {
            $this->logger = $logger;
        }
        else
        {
            $this->logger = new wcmLogger(true, true);
        }

        if ($refresh)
        {
            $this->refresh();
        }
    }

    /**
     * Returns the text corresponding to an URL request
     *
     * @param string  $url          URL to be queried
     * @param mixed   $parameters   Either an associative array or a query string (e.g. "foo1=val1&foo2=val2...")
     *
     * @return string Content of the url invoked
     *
     */
    private function invokeUrl($url, $parameters = null)
    {
        $ch = curl_init();

        // Prepare URL (method POST, no ECHO)
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Assign post variables
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

        // Invoke URL
        $result = curl_exec($ch);
        curl_close ($ch);

        return $result;
    }
    /**
     * Returns logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Invoke a method of the webservice
     *
     * @param string $method     Method name (e.g. "GetProperties", "GetDocument", "InitQuery", ...)
     * @param array  $parameters Method parameters (an associative array or NULL)
     *
     * @return DOMDocument The corresponding DOMDocument or null on failure (will update 'lastError' property)
     */
    public function executeMethod($method, $parameters = null)
    {
        // Compute URL from method
        $url = $this->webserviceURL . $method . '.aspx';

        // Serialize parameters
        if (is_array($parameters))
        {
            $urlParams = null;
            foreach ($parameters as $key => $value)
            {
                $urlParams .= ($key . '=' . urlencode($value) . '&');
            }

            // Remove extra '&'
            if ($urlParams) $urlParams = substr($urlParams, 0, -1);
        }
        else
        {
            $urlParams = $parameters;
        }

        // Log
        $this->logger->logVerbose('wcmTextmlServer::executeMethod ' . $url . '?' . $urlParams);

        // Check result existence
        $result = $this->invokeUrl($url, $parameters);
        if ($result == null)
        {
            $this->lastError = 'Method ' . $method . ' does not return any result. Connection maybe broken?';
            $this->logger->logError('wcmTextmlServer::executeMethod ' . $method . ' failed: ' . $this->lastError);
            return null;
        }

        // Check result validity
        $domXML = new DOMDocument();
        if (!$domXML->loadXML($result))
        {
	
            $this->lastError = 'Method ' . $method . ' returned an invalid XML: ' . $result;
	    $this->logger->logError('wcmTextmlServer::executeMethod ' . $method . ' failed: ' . $this->lastError);
            return null;
        }

        // Check if an <error> tag exists
        $rootNode = $domXML->documentElement;
        foreach($rootNode->childNodes as $child)
        {
            if ($child->nodeName == "error")
            {
                $this->lastError = $child->nodeValue;
                $this->logger->logError('wcmTextmlServer::executeMethod ' . $method . ' failed: ' . $this->lastError);
                return null;
            }
        }

        // Result DOM
        return $domXML;
    }

    /**
     * Refresh the properties from the server (docbase name, server name, document count, index list, xsl list)
     *
     * @return TRUE on success, FALSE otherwise (check 'lastError' property for more information)
     */
    public function refresh()
    {

        if (!$domXML = $this->executeMethod('GetProperties')) return false;

        // Retrieve general properties
        $rootNode = $domXML->documentElement;
        $this->serverName = $rootNode->getAttribute("server");
        $this->docbaseName = $rootNode->getAttribute("docbase");
        $this->documentCount = $rootNode->getAttribute("documents");

        // Retrieve other properties
        foreach($rootNode->childNodes as $child)
        {
            switch($child->nodeName)
            {
                // Retrieve index list
                case 'indexList':
                    $this->indexList = array();
                    foreach($child->childNodes as $indexNode)
                    {
                        $name = $indexNode->getAttribute("name");
                        $kind = $indexNode->getAttribute("kind");
                        $property = ($indexNode->getAttribute("property") == "True");
                        $this->indexList[$name] = array("name" => $name, "kind" => $kind, "property" => $property);
                    }

                    // To allow date time support a DateIndex index can be associated to a TimeIndex index
                    foreach($this->indexList as $name => &$infos)
                    {
                        if('DateIndex' === $infos['kind'] && array_key_exists($name . '_time', $this->indexList))
                        {
                            $infos['kind'] = 'DateTimeIndex';
                        }
                    }
                    break;

                // Retrieve xsl list
                case 'xslList':
                    $this->xslList = array();
                    foreach($child->childNodes as $xslNode)
                    {
                        $this->xslList[] = $xslNode->nodeValue;
                    }
                    break;
            }
        }
        return true;
    }

    /**
     * Retrieve a document content from the docbase (and optionally transform it on server-side before returning content)
     *
     * @param string $docId     Full document id (e.g. '/x/y/z/foo.xml')
     * @param string $xsl       XSL to use (server-side) before returning result or NULL (will use default)
     * @param string $xslParams XSL parameters (an associative array) or NULL
     *
     * @return string NULL on error, or the result of the xsl-t on the document (or the document content itself)
     */
    public function getDocument($docId, $xsl = null, $xslParams = null)
    {
        // Prepare parameters
        $parameters = array();
        $parameters['id'] = $docId;
        if ($xsl)
        {
            $parameters['xsl'] = $xsl;
        }
        if (is_array($xslParams))
        {
            $params = null;
            foreach($xslParams as $key => $value)
            {
                if ($params) $params .= '|';
                $params .= ($key . '=' . $value);
            }
            if ($params != null)
            {
                $parameters['parameters'] = $params;
            }
        }

        // Invoke method
        if (!$domXML = $this->executeMethod('GetDocument', $parameters)) return null;

        // The first node contains the document content
        return $domXml->documentElement->nodeValue;
    }

    /**
     * Set (create or update) an XML document in the docbase
     *
     * @param string $docId     Full path of document (e.g. '/x/y/z/foo.xml')
     * @param string $xml       Xml document content (with XML declaration)
     *
     * @return boolean TRUE on success, FALSE otherwise (check 'lastError' property for textual information)
     */
    public function setDocument($docId, $xml)
    {
        // Prepare parameters
        $parameters = array();
        $parameters['id'] = $docId;
        $parameters['xml'] = $xml;

        // Invoke method
        if (!$domXML = $this->executeMethod('SetDocument', $parameters)) return false;

        return true;
    }

    /**
     * Remove an existing document from the docbase
     *
     * @param string $docId     Full path of document (e.g. '/x/y/z/foo.xml')
     *
     * @return TRUE on success, FALSE otherwise (check 'lastError' property for more information)
     */
    public function removeDocument($docId)
    {
        // Prepare parameters
        $parameters = array();
        $parameters['id'] = $docId;

        // Invoke method
        if (!$domXML = $this->executeMethod('RemoveDocument', $parameters)) return false;

        return true;
    }

    /**
     * Remove an existing collection from the docbase (and all its content)
     *
     * @param string $collectionId Full path of collection (e.g. '/x/y/z')
     *
     * @return TRUE on success, FALSE otherwise (check 'lastError' property for more information)
     */
    public function removeCollection($collectionId)
    {
        // Prepare parameters
        $parameters = array();
        $parameters['id'] = $collectionId;

        // Invoke method
        if (!$domXML = $this->executeMethod('RemoveCollection', $parameters)) return false;

        return true;
    }

    /**
     * Retrieve the list of distinct index values
     *
     * @param string $indexName Name of the index to scan
     * @param int    $maxValues Maximum number of returned values (default is 512)
     * @param string $query     Optional native Textml query to reduce the scope of index values (default is NULL)
     *
     * @return array An associative array where key is index value and value is document count (or NULL on failure)
     */
    public function getIndexValues($indexName, $maxValues = 512, $query = null, $sortByOccurence = false)
    {
        // Prepare parameters
        $parameters = array();
        $parameters['name'] = $indexName;
        $parameters['max'] = $maxValues;
        $parameters['sortByOccurence'] = ($sortByOccurence ? 1 : 0);
        if ($query)
        {
            $parameters['query'] = $indexName;
        }

        // Invoke method
        if (!$domXML = $this->executeMethod('GetIndexValues', $parameters)) return null;

        // Search for index value index
        $rootNode = $domXML->documentElement;
        foreach($rootNode->childNodes as $child)
        {
            switch($child->nodeName)
            {
                // Retrieve index value list:
                // <valueList>
                //    <value documentCount='43'> some value </value>
                //    ...
                // <valueList>
                case 'valueList':
                    $indexList =  array();
                    foreach($child->childNodes as $indexNode)
                    {
                        $value = $indexNode->nodeValue;
                        $indexList[$value] = $indexNode->getAttribute('documentCount');;
                    }
                    return $indexList;
            }
        }

        // ValueList was not found!!!!
        $this->lastError = 'GetIndexValues failed: cannot find [valueList] tag!';
        return null;
    }
}

?>
