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
 * This class helps managing queries from the Textml WebServices
 */
class wcmTextmlQuery
{
    /**
     * Instance of wcmTextmlServer
     */
    protected $textmlServer;

    /**
     * Unique query identifier
     */
    public $id;

    /**
     * Current page number
     */
    public $pageNumber = 1;

    /**
     * Page size
     */
    public $pageSize = 10;

    /**
     * Page count
     */
    public $pageCount = 0;

    /**
     * Result count
     */
    public $resultCount = 0;

    /**
     * Default XSL used of WebService side (or NULL)
     */
    public $xsl = null;

    /**
     * Last error message
     */
    public $lastError = null;

    /**
     * Native textQuery used for this instance
     */
    public $nativeQuery = null;

    /**
     * Constructor
     *
     * @param wcmTextmlServer  A valid textmlServer instance
     * @param string $id    Unique query identifier (used for query cache) or NULL for random id
     * @param string $xsl   Default XSL to use (or WebService server side) or NULL
     *
     */
    function __construct($textmlServer, $id = null, $xsl = null)
    {
        $this->textmlServer = $textmlServer;
        $this->id = ($id) ? $id : ('query_' . mt_rand());
        if ($xsl) $this->xsl = $xsl;
    }

    /**
     * Initialize current object from a native query
     *
     * @param  string $query      Native Textml query
     * @param  array  $xslParams  Extra parameters (used for XSL transformation) or NULL
     *
     * @return int Nomber of documents found or NULL on failure
     *
     */
    public function initQuery($query, $xslParams = null)
    {
        // Remember query
        $this->nativeQuery = $query;

        // Prepare method parameters
        $parameters = array();
        $parameters['id'] = $this->id;
        $parameters['query'] = $query;
        if ($this->xsl)
        {
            $parameters['xsl'] = $this->xsl;
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

        // Execute query
        if (!$domXML = $this->textmlServer->executeMethod('InitQuery', $parameters)) return null;

        // Retrieve resultCount and compute page numbers
        $rootNode = $domXML->documentElement;
        $this->resultCount = intval($rootNode->getAttribute("count"));
        $this->pageCount = intval($this->resultCount / $this->pageSize);
        if ($this->resultCount % $this->pageSize != 0)
        {
            $this->pageCount++;
        }

        return $this->resultCount;
    }

    /**
     * Retrieve a document content from the query
     *
     * @param  int        $index     Index (zero based) of document to retrieve
     * @param  bool|true  $xml       An XML is returned if true otherwise a bizobject
     * @param  string     $xsl       Specific XSL to use (or null to use default)
     * @param  array      $xslParams Extra parameters for XSL (or null)
     *
     * @return mixed xml (eventually transformed) or bizobject
     */
    public function getDocument($index, $xml = true, $xsl = null, $xslParams = null)
    {
        $result = $this->getDocumentRange($index, $index, $xml, $xsl, $xslParams);

        if ($xml)
            return $result;

        if (!is_array($result) || count($result) == 0)
            return null;

        return $result[0];
    }

    /**
     * Retrieve a range of documents from the query
     *
     * @param  int          $from      Index (zero based) of first document to retrieve
     * @param  int          $to        Index (zero based) of last document to retrieve
     * @param  bool|true    $xml       Whether to return raw XML or an array of XML fragments
     * @param  string|null  $xsl       Specific XSL to use (or null to use default)
     * @param  array|null   $xslParams Extra parameters for XSL (or null)
     *
     * @return mixed An array of bizobject of a xml (eventually transformed using xsl) or NULL on failure
     */
    public function getDocumentRange($from, $to, $xml = true, $xsl = null, $xslParams = null)
    {
        // Prepare method parameters
        $parameters = array();
        $parameters['id'] = $this->id;
        $parameters['from'] = $from;
        $parameters['to'] = $to;
        // If an xsl is passed as a parameter, it will be used, otherwise we use the constructor's one
        if ($xsl)
        {
            $parameters['xsl'] = $xsl;
        }
        else
        {
            if ($this->xsl)
                $parameters['xsl'] = $this->xsl;
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

        // Execute query
        if (!$domXML = $this->textmlServer->executeMethod('GetDocuments', $parameters)) return null;
         
        // Retrieve document content
        if (!$xml)
            $result = array();
        else
            $result = "<resultSet>";
            
        $rootNode = $domXML->documentElement;
        foreach($rootNode->childNodes as $child)
        {
            if ($child->nodeName == "document")
            {
                if (!$xml)
                {
                	$simpleXml = simplexml_load_string($child->nodeValue);
                    if ($simpleXml === false)
                        continue;
					
                    $className = strval($simpleXml->className);
                    
                    $id = intval($simpleXml->id);
                    // ############################### patch CC
                    if (empty($id) && isset($simpleXml->id->hit)) 
                    	$id = intval($simpleXml->id->hit);
					
                    $bizobject = null;
                    
				    if ($className && $id)
						$bizobject = @new $className($this->project, $id);
					
					if ($bizobject)
                        $result[] = $bizobject;
                }
                else
                {
                    // TODO temporary fix for weird TextML response:
                    // each child has an XML declaration!
                    $nodeValue = $child->nodeValue;
                    $nodeValue = preg_replace('/<\?xml .*?\?>/', '', $nodeValue);

                    $result .= "<result>";
                    $result .= $nodeValue;
                    $result .= "</result>";
                }
            }
        }
        if ($xml)
            $result .= "</resultSet>";

        return $result;
    }

    /**
     * Gets a range of individual documents data.
     *
     * NOTE: The index fields specified in the $fieldList parameter
     *       must be of type 'string' and their "keep extracted"
     *       attribute must be set to "true'.
     *
     * @param string      $id         The ID of a previous query
     * @param string      $indexList  The list of index fields to search
     * @param int|null    $maxResults The maximum number of results per index field (default is null)
     * @param bool|true   $xml        Whether to return raw XML or an array
     * @param string|null $xsl        An XSL to transform the results (default is null)
     * @param array|null  $xslParams  XSL parameters (default is null)
     *                                    or an array of assocs of field value => occurence count
     *
     * @return array|string|null Raw XML or an array containing the requested range of documents data,
     *                               or null on error
     */
    public function getDocumentsData($id, $fieldList, $maxResult = null,
                                     $xml = true, $xsl = null, $xslParams = null)
    {
        // Prepare method parameters
        $parameters = array();
        if (!isset($id) || is_null($id))
            $parameters['id'] = $this->id;
        else
            $parameters['id'] = $id;
        $parameters['fieldList'] = $fieldList;
        if (!is_null($maxResult))
            $parameters['maxResult'] = $maxResult;
        // If an xsl is passed as a parameter, it will be used, otherwise we use the constructor's one
        if ($xsl)
        {
            $parameters['xsl'] = $xsl;
        }
        else
        {
            if ($this->xsl)
                $parameters['xsl'] = $this->xsl;
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

        // Execute query
        if (!$domXML = $this->textmlServer->executeMethod('GetDocumentsData', $parameters))
            return null;

        // Retrieve document content
        if ($xml)
            $result = "<resultSet>";
        else
            $result = array();

        $rootNode = $domXML->documentElement;
        if ($rootNode->childNodes)
        {
            foreach ($rootNode->childNodes as $child)
            {
                if ($child->nodeName == "index")
                {
                    if ($xml)
                    {
                        $result .= "<result>";
                        $result .= $child->nodeValue;
                        $result .= "</result>";
                    }
                    elseif ($child->childNodes)
                    {
                        $elems = array();
                        foreach ($child->childNodes as $elemNode)
                        {
                            if ($elemNode->nodeName == "elem")
                            {
                                $elems[$elemNode->nodeValue] = $elemNode->getAttribute('occurence');
                            }
                        }
                        $result[$child->getAttribute('name')] = $elems;
                    }
                }
            }
        }
        if ($xml)
            $result .= "</resultSet>";

        return $result;
    }

    /**
     * Returns the content of current 'page' (a range computed form pageSize and pageNumber properties)
     *
     * @param bool|true  $xml        Whether to return raw XML or an array of bizobjects
     * @param string     $xsl       Specific XSL to use (or null to use default)
     * @param array      $xslParams Extra parameters for XSL (or null)
     *
     * @return mixed XML (possibly transformed through XSL) or array of bizobjects, or NULL on failure
     */
    public function getPageContent($xml = true, $xsl = null, $xslParams = null)
    {
        // Compute range from current page number
        $from = ($this->pageNumber - 1) * $this->pageSize;
        $to = $from + $this->pageSize - 1;
        if ($to >= $this->resultCount)
        {
            $to = $this->resultCount - 1;
        }

        return $this->getDocumentRange($from, $to, $xml, $xsl, $xslParams);
    }
}

?>