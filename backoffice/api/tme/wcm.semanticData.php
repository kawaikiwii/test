<?php
/**
 * Project:     WCM
 * File:        wcm.semanticData.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Provides a bridge to the Text-Mining Engine Server
 */
class wcmSemanticData implements ArrayAccess
{
    /**
     * Text language (for auto-detection)
     */
    public $language;

    /**
     * Array of concepts
     * 'concept' => array('relevancy' => , 'frequency' =>)
     */
    public $concepts;

    /**
     * Array of organization names
     *
     * 'entity' => array('relevancy' => , 'frequency' =>, 'weight' =>, 'confidence' =>)
     */
    public $ON;

    /**
     * Array of geographic locations
     *
     * 'entity' => array('relevancy' => , 'frequency' =>, 'weight' =>, 'confidence' =>)
     */
    public $GL;

    /**
     * Array of people names
     *
     * 'entity' => array('relevancy' => , 'frequency' =>, 'weight' =>, 'confidence' =>)
     */
    public $PN;

    /**
     * Text summary
     */
    public $summary;

    /**
     * Text tone (between -100 and 100)
     */
    public $tone;

    /**
     * Text positiveTone (between 0 and 100)
     */
    public $positiveTone;

    /**
     * Text negativeTone (between 0 and 100)
     */
    public $negativeTone;

    /**
     * Text subjectivity
     */
    public $subjectivity;

    /**
     * Array of categories
     *
     * 'category' => ('weight' =>, 'confidence' =>)
     */
    public $categories;

    /**
     * Array of  similar items
     *
     * 'className_id' => ('className' =>, 'id' => )
     */
    public $similars;

    /**
     * Constructor
     *
     * @param string $xml An optional XML represeting result from TME
     */
    public function __construct($xml = null)
    {
        $this->cleanup();
        if ($xml)
        {
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $xpath = new DOMXpath($dom);

            foreach($xpath->query('//Category') as $el)
            {
                $key = ucfirst(array_pop(explode(' - ', $el->nodeValue)));
                $this->categories[$key] = array('weight' => $el->getAttribute('Weight'),
                                                'confidence' => $el->getAttribute('ConfidenceScore'));
            }

            foreach($xpath->query('//Concept') as $el)
            {
                $key = ucfirst($el->nodeValue);
                $this->concepts[$key] = array(  'frequency' => $el->getAttribute('Frequency'),
                                                'relevancy' => $el->getAttribute('RelevancyScore'));
            }

            foreach($xpath->query('//Entity[@Kind="ON"]') as $el)
            {
                $key = ucfirst($el->nodeValue);
                $this->ON[$key] = array(    'frequency' => $el->getAttribute('Frequency'),
                                            'relevancy' => $el->getAttribute('RelevancyScore'),
                                            'weight' => $el->getAttribute('Weight'),
                                            'confidence' => $el->getAttribute('ConfidenceScore'));
            }

            foreach($xpath->query('//Entity[@Kind="PN"]') as $el)
            {
                $key = ucfirst($el->nodeValue);
                $this->PN[$key] = array(    'frequency' => $el->getAttribute('Frequency'),
                                            'relevancy' => $el->getAttribute('RelevancyScore'),
                                            'weight' => $el->getAttribute('Weight'),
                                            'confidence' => $el->getAttribute('ConfidenceScore'));
            }

            foreach($xpath->query('//Entity[@Kind="GL"]') as $el)
            {
                $key = ucfirst($el->nodeValue);
                $this->GL[$key] = array(    'frequency' => $el->getAttribute('Frequency'),
                                            'relevancy' => $el->getAttribute('RelevancyScore'),
                                            'weight' => $el->getAttribute('Weight'),
                                            'confidence' => $el->getAttribute('ConfidenceScore'));
            }

            foreach($xpath->query('//Summary') as $el)
            {
                $this->summary = $el->nodeValue;
            }

            foreach($xpath->query('//Sentiment') as $el)
            {
                $this->positiveTone = intval(floatval($el->getAttribute('PositiveTone')) * 100);
                $this->negativeTone = intval(floatval($el->getAttribute('NegativeTone')) * 100);
                $this->subjectivity = intval(floatval($el->getAttribute('Subjectivity')) * 33);
                if ($this->positiveTone > $this->negativeTone)
                    $this->tone = $this->positiveTone;
                else
                    $this->tone = - $this->negativeTone;
            }

            foreach($xpath->query('//SimilarText') as $el)
            {
                list($className, $id) = explode('_', $el->getAttribute('Id'));

                // Ensure similar object still exists
                $bizobject = new $className(null, $id);
                if ($bizobject->id)
                {
                    $this->similars[$el->getAttribute('Id')] = array('className' => $className,
                                                                     'id' => $id,
                    												 'score' => intval(floatval($el->getAttribute('Weight')) * 100),
                                                                     'title' => $bizobject->title);
                }
            }
        }
    }

    /**
     * Initialize semantic data from an XML node
     *
     * *@param DOMElement An XML node representing semantic data
     */
    public function fromXML($node)
    {
        $this->cleanup();
        if (!$node) return;

        if ($node->childNodes)
        {
            foreach($node->childNodes as $child)
            {
                $name = $child->nodeName;
                switch($name)
                {
                case 'language':
                case 'tone':
                case 'positiveTone':
                case 'negativeTone':
                case 'subjectivity':
                case 'summary':
                    $this->$name = $child->nodeValue;
                    break;
                case 'concepts':
                    foreach($child->childNodes as $el)
                    {
                        if ($el->nodeName == '#text') continue;

                        $keys[] = $el->nodeValue;
                        $vals[] = array('frequency' => $el->getAttribute('frequency'),
                                        'relevancy' => $el->getAttribute('relevancy'));

                        $this->$name = array_combine($keys, $vals);
                    }
                    break;
                case 'categories':
                    foreach($child->childNodes as $el)
                    {
                        if ($el->nodeName == '#text') continue;

                        $keys[] = $el->nodeValue;
                        $vals[] = array('weight' => $el->getAttribute('weight'),
                                        'confidence' => $el->getAttribute('confidence'));
                        $this->$name = array_combine($keys, $vals);
                    }
                    break;
                case 'entitiesON':
                case 'entitiesPN':
                case 'entitiesGL':
                    $name = substr($name, -2);
                    foreach($child->childNodes as $el)
                    {
                        if ($el->nodeName == '#text') continue;

                        $keys[] = $el->nodeValue;
                        $vals[] = array('frequency' => $el->getAttribute('frequency'),
                                        'relevancy' => $el->getAttribute('relevancy'),
                                        'weight' => $el->getAttribute('weight'),
                                        'confidence' => $el->getAttribute('confidence'));
                        $this->$name = array_combine($keys, $vals);
                    }
                    break;
                case 'similars':
                    foreach($child->childNodes as $el)
                    {
                        if ($el->nodeName == '#text') continue;

                        $keys[] = $el->nodeValue;
                        $vals[] = array('className' => $el->getAttribute('className'),
                                        'id' => $el->getAttribute('id'),
                                        'title' => $el->getAttribute('title'));
                        $this->$name = array_combine($keys, $vals);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Returns an XML representing the semantic data
     *
     * @return string XML representing the data
     */
    public function toXML()
    {
        $xml = '<semanticData>';
        $xml .= '<language>' . wcmXML::xmlEncode($this->language) . '</language>';
        $xml .= '<tone>' . wcmXML::xmlEncode($this->tone) . '</tone>';
        $xml .= '<positiveTone>' . wcmXML::xmlEncode($this->positiveTone) . '</positiveTone>';
        $xml .= '<negativeTone>' . wcmXML::xmlEncode($this->negativeTone) . '</negativeTone>';
        $xml .= '<subjectivity>' . wcmXML::xmlEncode($this->subjectivity) . '</subjectivity>';
        $xml .= '<summary>' . wcmXML::xmlEncode($this->summary) . '</summary>';
        $xml .= $this->assocToXML($this->concepts, 'concepts', 'concept');
        $xml .= $this->assocToXML($this->categories, 'categories', 'category');
        $xml .= $this->assocToXML($this->ON, 'entitiesON', 'ON');
        $xml .= $this->assocToXML($this->PN, 'entitiesPN', 'PN');
        $xml .= $this->assocToXML($this->GL, 'entitiesGL', 'GL');
        $xml .= $this->assocToXML($this->similars, 'similars', 'similar');
        $xml .= '</semanticData>';

        return $xml;
    }

    /**
     * Returns an XML representing the semantic assoc array
     *
     * @return string XML representing the array
     */
    private function assocToXML($assoc, $mainTag, $tag)
    {
        $xml = '<' . $mainTag . '>';
        if ($assoc)
        {
            foreach($assoc as $item => $attributes)
            {
                $xml .= '<' . $tag;
                foreach($attributes as $attribute => $value)
                {
                    $xml .= ' ' . $attribute . '="' . wcmXML::xmlEncode($value) . '"';
                }
                $xml .= '>' . wcmXML::xmlEncode($item) . '</' . $tag . '>';
            }
        }
        $xml .= '</' . $mainTag . '>';

        return $xml;
    }

    /**
     * Cleanup the structure
     */
    public function cleanup()
    {
        $this->language = 'ENGLISH';
        $this->concepts = array();
        $this->ON = array();
        $this->PN = array();
        $this->GL = array();
        $this->categories = array();
        $this->similars = array();
        $this->summary = null;
        $this->tone = 0;
        $this->positiveTone = 0;
        $this->negativeTone = 0;
        $this->subjectivity = 0;
    }


    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return bool True is key exists
     */
    public function offsetExists($key)
    {
        return isset($this->$key);
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return mixed Configuration key value
     */
    public function offsetGet($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception Configuration is read only!
     */
    public function offsetSet($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Implements interface ArrayAcccess
     *
     * @param string $key Configuration key
     *
     * @return Exception Configuration is read only!
     */
    public function offsetUnset($key)
    {
        $this->$key = null;
    }
    
    /**
     * Updates semantic data from an associative array
     * (typically used with $_REQUEST['_semanticData'])
     *
     * @param array $data Associative array containing data to update
     *                    (the key must match a public property of the
     *                     semanticData and is case-sensitive)
     */
    public function updateFromAssocArray(array $data)
    {
        if (!$data) return;

        foreach($data as $key => $value)
        {
            if (property_exists($this, $key))
            {
                // are we dealing with an array? (e.g. ON, PN, GL, categories, concepts)
                if (is_array($this->$key))
                {
                    // assume value is a list of string separated by pipe
                    $array = array();
                    $values = explode('|', $value);
                    foreach($values as $val)
                    {
                        $array[$val] = array('frequency' => 0, 'relevancy' => 100,
                                             'weight' => 100, 'confidence' => 100);
                    }
                    $this->$key = $array;
                }
                else
                {
                    // store literal value (e.g. summary, tone, subjectivity, ...)
                    $this->$key = $value;
                }
            }
            else
            {
                // unexpected property... log warning
                wcmProject::getInstance()->logger->logWarning('Invalid semanticData property: "'.$key.'"');
            }
        }
    }
    
    
    /**
     * Merge current semantic data with extra data. If the extra data contains
     * new values (i.e. non-existing in the current semanticData) those values will
     * be added. The original values (for list like ON,PN,GL...) will be kept.
     *
     * @param wcmSemanticData $extraData Extra semantic data
     */
    public function merge(wcmSemanticData $extraData)
    {
        foreach($this as $key => $source)
        {
            // manage array (ON, PN, GL, concepts, categories...)
            $extra = $extraData->$key;
            if (is_array($source) && is_array($extra))
            {
                $source = array_merge($extra, $source);
                $this->$key = $source;
            }
            elseif($extra && !$source)
            {
                $this->$key = $extra;
            }
        }
    }
    
}