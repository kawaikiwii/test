<?php
class wcmImportDAM extends wcmGenericImport
{
    
    private $activeQuery;
    private $activeQueryId;
    
    private $searchService;
    private $managementService;
    private $mediaService;
    
    private $sessionToken;
    private $managementAuth;
    
    private $binaryClasses;
    
    private $xslCache;
    private $classCache;
    
    
    /**
     * Stores relationships before they are built
     *
     * @var array
     */
    private $relStore = array();
    
    /**
     * ID Maps
     * 
     * This contains a list of old to new IDs to build relationships with
     *
     * @var array $idMaps
     */
    private $idMaps = array();
    
    /**
     * Relationship Type mapping
     * 
     * Maps DAM relationship types to WCM relationship types
     *
     * @var array $relMaps
     */
    private $relMaps = array('BELONGS_TO' => wcmBizrelation::IS_RELATED_TO,
                             'SIMILAR_TO' => wcmBizrelation::IS_RELATED_TO,
                             'RELATED_TO' => wcmBizrelation::IS_RELATED_TO);
    
    public function __construct(array $parameters)
    {
        
        parent::__construct($parameters);
        
        $conf = wcmConfig::getInstance();
        $this->binaryClasses = explode(',',$conf['dam.importRules.binaryClasses']);
        
        $query = '';
        
        // create query
        if (isset($parameters['classes']) && is_array($parameters['classes']))
        {
            $query .= 'Class:(';
            foreach ($parameters['classes'] as $class => $flag)
            {
                if ($flag)
                {
                    $query .= $class.' ';
                }
            }
            $query = trim($query).')';
        }
        
        
        if (!empty($parameters['publicationDate']))
        {
            $query .= '&PublicationDate:['.date('Ymd',$parameters['publicationDate']).','.date('Ymd').']';
        }
        
        $parameters['query'] = $query;
        
        // First parameter: query string
        $this->activeQuery = $parameters['query'];
        $this->logger->logMessage('Active Query: '.$this->activeQuery);
        $this->activeQueryId = uniqid();
        
        $options['soap_version'] = SOAP_1_2;
        
        $parameters['searchWsdl'] = $parameters['webServiceURL'].'SearchService.asmx?WSDL';
        $parameters['managementWsdl'] = $parameters['webServiceURL'].'BizObjectManagementService.asmx?WSDL';
        $parameters['mediaWsdl'] = $parameters['mrWebServiceURL'].'MediaRepositoryService.asmx?WSDL';
        
        
        $this->searchService = new SoapClient($parameters['searchWsdl'],$options);
        $this->managementService = new SoapClient($parameters['managementWsdl'],$options);
        $this->mediaService = new SoapClient($parameters['mediaWsdl'],$options);
        
        
        $obj = new stdClass;
        $obj->login = $parameters['login'];
        $obj->password = $parameters['password'];
        $obj->language = 'english';
        
        $this->sessionToken = $this->searchService->Authenticate($obj)->AuthenticateResult;
        
        $this->parameters = $parameters;
        $this->parameters['sessionToken'] = $this->sessionToken;
        
        $this->getTotal();
    }
    
    public function process()
    {
            if (!$this->total)
            {
                $this->logger->logWarning(_BIZ_IMPORT_NOTHING);
                return;
            }        
            
            $config = wcmConfig::getInstance();
            
            $params = new stdClass;
            $params->queryName = $this->activeQueryId;
            $params->offset = 0;
            $params->limit = $this->total;
            
            $result = $this->searchService->StateFullRetrieveBizObjectSearchResults($params);
    
    
            
            $results = $result->StateFullRetrieveBizObjectSearchResultsResult->string;
            
            // Load up the config into DOM and prep it for xpath.
            $confDom = new DOMDocument;
            if (!$confDom->load((defined('WCM_CONFIG_FILE'))? WCM_CONFIG_FILE : WCM_DIR.'/xml/configuration.xml'))
            {
                $this->logger->logError(_BIZ_CANNOT_LOAD_CONFIGURARTION_FILE);
                return;
            }
            
            $confXpath = new DOMXPath($confDom);
            
            foreach ($results as $xml)
            {
                try
                {
                    $this->totalProcessed++;
                    $this->writePollData(floor(($this->totalProcessed / $this->total) * 100));            
                    
                    /**
                     * DAM it. DAM XML is spit out full of HTML entities that are not part
                     * of the XML standard and it all breaks unless they get pretranslated.
                     * 
                     * Of course, the entities themselves are encoded as well, so you end up with
                     * scenarios like this:
                     * &amp;egrave;
                     */
                    $table1 = get_html_translation_table(HTML_ENTITIES);
                    $table2 = get_html_translation_table(HTML_SPECIALCHARS);
                    $table3 = array_flip(array_diff_assoc($table1,$table2));
                    $table3['&nbsp;'] = ' ';
                    
                    // Stranger entities
                    $table3['&hellip;'] = '&#8230';
                    $table3['&OElig;'] = '&#338;';
                    $table3['&oelig;'] = '&#338;';
                    $table3['&bull;'] = '&#8226;';        
                    
                    $xml = html_entity_decode($xml, ENT_COMPAT,'UTF-8');
                    
                    
                    foreach ($table3 as $entity => $char)
                    {
                        $xml = str_replace($entity,utf8_encode($char),$xml);
                    }
                    
                    $xml = str_replace('&ndash;','&#8211;',$xml);
                    $xml = str_replace('&rsquo;','&#2019;', $xml);
                    $xml = str_replace('&hellip;','&#8230;',$xml);
                    
                    $dom = new DOMDocument('1.0','UTF-8');
                    if (!$dom->loadXML($xml))
                    {
                        $this->logger->logError(_BIZ_XML_INVALID);
                        continue;
                    }
                    
                    $xpath = new DOMXPath($dom);
                    
                    $uniqueCode = wcmXML::getXPathNodeValue($xpath,null,'//Id',uniqid());
                    $source     = wcmXML::getXPathNodeValue($xpath,null,'//Content/Metadata/Source','DAM');
                    $sourceId   = wcmXML::getXPathNodeValue($xpath,null,'//Content/Metadata/SourceID',$uniqueCode);
                    

                    
                    
                    
                    $BizObject = $dom->getElementsByTagName('BizObject');
                    $Class = $BizObject->item(0)->getAttributeNode('Class')->value;
                    
                    $tmp = '//dam/importRules/allowedClasses/class[@id=\''.$Class.'\']/wcmObject';
                    $className = wcmXML::getXPathNodeValue($confXpath, null, $tmp, null);
                    unset($tmp);
                    
                    if (!$className)
                    {
                        $this->logger->logError(_BIZ_IMPORT_DAM_NO_MAPPING.': '.$Class);
                        continue;
                    }
                    
                   
                                    
                    if (!class_exists($className))
                    {
                        $this->logger->logError($className.' '._BIZ_DOESNT_EXIST);
                        continue;
                    }
                    
                    $this->parameters['damClassname'] = $Class;
                    
                    $bizObj = new $className;
                    $bizObj->refreshFromSource($source,$sourceId);
                    
                    if ($bizObj->id)
                    {
                        $this->logger->logMessage(sprintf(_BIZ_UPDATE_BIZOBJECT, $className, $bizObj->id, $bizObj->sourceId));
                    } else {
                        $this->logger->logMessage(sprintf(_BIZ_CREATE_BIZOBJECT, $className, '', $sourceId));
                    }
        
                    if (isset($this->xslCache[$className]) && $this->xslCache[$className])
                    {
                        $bizObj->initFromXMLDocument($dom, $this->parameters['xslFolder'].'/'.$className.'.xsl');
                    } elseif (!isset($this->xslCache[$className])) {
                        if (is_file($this->parameters['xslFolder'].'/'.$className.'.xsl'))
                        {
                            $bizObj->initFromXMLDocument($dom, $this->parameters['xslFolder'].'/'.$className.'.xsl');
                            $this->xslCache[$className] = true;    
                        } else {
                            $this->logger->logError(_BIZ_XSL_NOT_FOUND.': '.$this->parameters['xslFolder'].'/'.$className.'.xsl');
                            $this->xslCache[$className] = false;
                            continue;
                        }
                    } else {
                        $this->logger->logError(_BIZ_XSL_NOT_FOUND.': '.$this->parameters['xslFolder'].'/'.$className.'.xsl');
                        continue;
                    }
                    $bizObj->initFromXMLDocument($dom, $this->parameters['xslFolder'].'/'.$className.'.xsl');
                    $bizObj->revisionNumber = 1;
                    $bizObj->siteId = $this->parameters['siteId'];
                    $bizObj->source = $source;
                    $bizObj->sourceId = $sourceId;
                    
                    
                    
                    
                    
                    /**
                     * This code is stupid, but autoload DIES when a class name is invalid
                     * so I can't just use class_exists()
                     * 
                     * Just bare with me here ok? Thanks, here we go!
                     */
                    if (!isset($this->classCache[$className]))
                    {
                        $processorClassFile = WCM_DIR.'/business/import/plugins/DAM/wcm.importDAM_'.$className.'.php';
                        if (is_file($processorClassFile))
                        {
                            $this->classCache[$className] = true;
                        } else {
                            $this->classCache[$className] = false;
                        }
                    }
                    
                    
                    if ($this->classCache[$className])
                    {
                        $processorClass = 'wcmImportDAM_'.$className;
                        $this->logger->logMessage(_BIZ_IMPORT_DAM_NEW_PROCESSOR.': '.$processorClass);
                        $obj = new $processorClass($this->parameters, &$bizObj, $dom, $this->logger);
                        if ($obj->process())
                        {
                            
                            // Make sure object has a title
                            if (empty($bizObj->title))
                            {
                                $bizObj->title = wcmXML::getXPathNodeValue($xpath,null,'//Name',$className.'-'.$bizObj->sourceId);
                            }
                            
                            if ($bizObj->save())
                            {
                                $this->logger->logMessage(_BIZ_SAVED_BIZOBJECT.': '.$className.':'.$bizObj->title);
                            } else {
                                $this->logger->logError(_BIZ_ERROR_SAVE.': '.$className.':'.$bizObj->title.' - '.$bizObj->getErrorMsg());
                            }
                        } else {
                            $this->logger->logError(sprintf(_BIZ_IMPORT_DAM_PROCESSOR_FAILED, $className));
                            continue;
                        }
                    } else {
                        
                        // Make sure object has a title
                        if (empty($bizObj->title))
                        {
                            $bizObj->title = wcmXML::getXPathNodeValue($xpath,null,'//Name',$className.'-'.$bizObj->sourceId);
                        }                
                        
                        if ($bizObj->save())
                        {
                            $this->logger->logMessage(_BIZ_SAVED_BIZOBJECT.': '.$className.':'.$bizObj->title);
                        } else {
                            $this->logger->logError(_BIZ_ERROR_SAVE.': '.$className.':'.$bizObj->title.' - '.$bizObj->getErrorMsg());
                        }
                    }
                    
                    $wcmObjects[] = $bizObj;
                    
                    $relationships = wcmXML::getXPathFirstNode($xpath,null,'//Relations');
                    
                    if (!empty($relationships))
                    {
                        $rels = $relationships->getElementsByTagName('BizRelation');
                        foreach ($rels as $rel)
                        {
                            $damBizClass = $rel->getAttribute('ClassName');
                            $relXpath = '//dam/importRules/allowedClasses/class[@id=\''.$damBizClass.'\']/wcmObject';
                            $className = wcmXML::getXPathNodeValue($confXpath, null, $relXpath,null);
                            if (!$className || !class_exists($className))
                            {
                                $this->logger->logWarning('Could not build bizRelation: destination class for '.$damBizClass.' is invalid');
                                continue;
                            }
                            
                            $newRelationship['damId'] = trim($rel->getAttribute('Id'));
                            $newRelationship['rank'] = trim($rel->getAttribute('Rank'));
                            $newRelationship['desc'] = trim($rel->getAttribute('Description'));
                            $newRelationship['title'] = trim($rel->getAttribute('Title'));
                            $newRelationship['sourceClass'] = trim($bizObj->getClass());
                            $newRelationship['destClass'] = $className;
                            $newRelationship['sourceId'] = $bizObj->id;
                            $newRelationship['kind'] = trim($rel->getAttribute('Kind'));
                            
                            $this->relStore[] = $newRelationship;
                            
                        }
                        
                    }                     
                    
                    $this->idMaps[$bizObj->getClass()][$uniqueCode] = $bizObj->id;
                    
                } catch (Exception $e) {
                    $this->logger->logError($e->getMessage());
                }
            }
            
            // build relationships
            foreach ($this->relStore as $relationship)
            {
                if (!isset($this->idMaps[$relationship['destClass']][$relationship['damId']]))
                {
                    $this->logger->logError('Could not build relation because destination object was never imported: damID: '.$relationship['damId'].' - destClass: '.$relationship['destClass']);
                    continue;
                }
                $rel = new wcmBizrelation();
                $rel->sourceClass = $relationship['sourceClass'];
                $rel->sourceId = $relationship['sourceId'];
                $rel->destinationClass = $relationship['destClass'];
                $rel->destinationId = $this->idMaps[$relationship['destClass']][$relationship['damId']];
                $rel->title = $relationship['title'];
                $rel->kind = $this->relMaps[$newRelationship['kind']];
                
                if (!$rel->save())
                {
                    $this->logger->logError('Could not build relationship between '.$rel->sourceClass.':'.$rel->sourceId.' and '.$rel->destinationClass.':'.$rel->destinationId.' - Cause: '.$rel->getErrorMsg());
                }
                
            }
            
            if (isset($wcmObjects)) return $wcmObjects;
            return;
        
    }
    
    public function getTotal()
    {
        $params = new stdClass;
        $params->queryName = $this->activeQueryId;
        $params->searchQuery = $this->activeQuery;
        
        $results = $this->searchService->StateFullBizObjectSearch($params);
        
        $xml = simplexml_load_string($results->StateFullBizObjectSearchResult);
        $this->total = (int) $xml->attributes()->Count;
        
        return $this->total;
    }

}

?>