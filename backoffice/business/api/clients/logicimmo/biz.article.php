<?php

/**
 * Project:     WCM
 * File:        biz.article.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Definition of an article
 */
class article extends bizobject
{
    // last chapter rank used
    private $lastChapterRank = 0;

   /**
    * (int) site id
    */
    public $siteId;

   /**
    * (string) Title
    */
    public $title;

   /**
    * (date) Publication date
    */
    public $publicationDate;

   /**
    * (string) Author
    */
    public $author;
    
    /**
     * (string) titleh2
     */
	public $titleh2;
	
	/**
	 * (string) titleh3
     */
	public $titleh3;
	
	/**
	 * (string) text
     */
   	public $description;
   	
    public $channelId;
    public $kind;
    public $location;

    public $image_url;
    public $image_caption;
    public $image_credits;

    public $publication;
    public $publicationYear;

    public $subtitle;
    /**
     * Set all initial values of an object
     * This method is invoked by the constructor
     */
    protected function setDefaultValues()
    {
        parent::setDefaultValues();

        $this->publicationDate   = date('Y-m-d');
        $this->permalinks = null; 

        $this->siteId = 0;
    }

    /**
     * CheckIn object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     * @param int   $userId id of user who create ou update the document
     *
     * @return true on success, false otherwise
     *
     */
    public function checkin($source = null, $userId = null)
    {
        // Insert or update the article object
        if (!parent::checkin($source, $userId))
            return false;

        return true;
    }

    /**
     * Exposes 'chapters' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of chapters getAssocArray
     */
    public function getAssoc_chapters($toXML = false)
    {
        $chapters = array();
        foreach($this->getChapters() as $chapter)
        {
            $chapterAssoc = $chapter->getAssocArray($toXML);
            if ($toXML)
                $chapterAssoc = $chapterAssoc->toArray();

            $chapters[] = $chapterAssoc;
        }

        return $chapters;
    }

    /**
     * Expose 'fulltext' to getAssocArray by computing a default full-text
     * string used for indexing
     *
     * Remark: this method is exposed by getAssocArray and may be
     * overloaded by inherited classes
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return string A full-text representation of the object
     */
    public function getAssoc_fulltext($toXML = false)
    {
        // => Retrieve all string keys
        // => Remove all tags (html, xml)
        // => Parse result text and discard stop words
        $fulltext = null;
        foreach(getPublicProperties($this) as $value)
        {
            if (is_string($value))
            {
                $fulltext .= ' ' . strip_tags($value);
            }
        }

        // => Add chapter's content
        foreach($this->getChapters() as $chapter)
        {
            $fulltext .= ' ' . $chapter->title;
            $fulltext .= ' ' . strip_tags($chapter->text);
        }

        $fulltext = getRawText($fulltext);

        // Parse text (no stop word, retrieve a single occurence for each word)
        return trim(implode(" ", parseText($fulltext, null, true)));
    }
    
    /**
    * Returns the chapters of the article
    *
    * @return  array An array containing all the bizchapters of the article
    */
    public function getChapters()
    {
        return bizobject::getBizobjects('chapter', 'articleId='.$this->id, 'rank');
    }
    
    /**
	* Returns the inserts of the article
	*
	* @return  array An array containing all the bizinserts of the article
	*/
	public function getInserts()
	{
		return bizobject::getBizobjects('inserts', 'articleId='.$this->id, 'rank');
    }

    /**
     * Returns a new rank to sur for a new chapter
     */
    public function getNewChapterRank()
    {
        if ($this->lastChapterRank == 0)
        {
            $sql = 'SELECT MAX(rank)+1 FROM #__chapter WHERE articleId='.$this->id;
            $this->lastChapterRank = $this->database->executeScalar($sql);
        }
        else
        {
            $this->lastChapterRank++;
        }

        return $this->lastChapterRank;
    }

    /**
     * Gets the 'semantic' text that will be passed to the Text-Mining Engine
     *
     * @return string The semantic text to mine
     */
    public function getSemanticText()
    {
        $content = '';
        return $content;
    }

    /**
     * Computes the sql where clause matching foreign constraints
     * => This method must be overloaded by child class
     *
     * @param string $of Assoc Array with foreign constrains (key=className, value=id)
     *
     * @return string Sql where clause matching "of" constraints or null
     */
    function ofClause($of)
    {
        if ($of == null || !is_array($of))
            return;

        $sql = null;

        foreach($of as $key => $value)
        {
            switch($key)
            {
                case "site":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "siteId=".$value;
                    break;

                case "channel":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "channelId=".$value;
                    break;
            }
        }
        return $sql;
    }

    /**
     * Deletes object from database
     *
     * @return true on success or an error message (string)
     *
     */
    public function delete()
    {
        // Delete all children chapters of this article
        $this->deleteChapters();
        $this->deleteInserts();
        return parent::delete();
    }

    /**
     * Delete all the chapters from the database
     */
    public function deleteChapters()
    {
        // Delete all children chapters of this article
        $chapters = $this->getChapters();
        foreach($chapters as $chapter)
        {
            $chapter->delete(false);
        }
    }

	/**
     * Delete all the chapters from the database
     */
    public function deleteInserts()
    {
        // Delete all children chapters of this article
        $inserts = $this->getInserts();
        foreach($inserts as $insert)
        {
            $insert->delete(false);
        }
    }
    
    /**
     * Update all the chapters by the chapters given in the array
     *
     * @param Array $array  Array of new chapters
     */
    public function updateChapters($newChapters)
    {

        $this->serialStorage['chapters'] = $newChapters;
    }

	/**
     * Update all the inserts by the inserts given in the array
     *
     * @param Array $array  Array of new inserts
     */
    public function updateInserts($newInserts)
    {

        $this->serialStorage['inserts'] = $newInserts;
    }


    /**
     * Gets object ready to store by getting modified date, creation date etc
     * Will execute transition.
     *
     */
    protected function store()
    {
        if(!parent::store()) return false;

        $newChapters = getArrayParameter($this->serialStorage, 'chapters');

        if($newChapters)
        {
            $this->deleteChapters();

            $chapter = new chapter();
            $chapter->articleId = $this->id;
            $chapter->rank = 0;

            foreach($newChapters as $newChapter)
            {
                $chapter->id = 0;
                $chapter->rank++;

                $newChapter['articleId'] = $chapter->articleId;
                $newChapter['id'] = $chapter->id;
                $newChapter['rank'] = $chapter->rank;

                if (!$chapter->save($newChapter))
                {
                    $this->lastErrorMsg = $chapter->lastErrorMsg;
                    return false;
                }
            }
        }
        
		$newInserts = getArrayParameter($this->serialStorage, 'inserts');

		if($newInserts)
		{
			$this->deleteInserts();

			$insert = new inserts();
			$insert->articleId = $this->id;
			$insert->rank = 0;

			foreach($newInserts as $newInsert)
			{
				if ($newInsert['kind'] == '')
					$newInsert['text'] = '';
					
				$insert->id = 0;
				$insert->rank++;

				$newInsert['articleId'] = $insert->articleId;
				$newInsert['id'] = $insert->id;
				$newInsert['rank'] = $insert->rank;

				if (!$insert->save($newInsert))
				{
					$this->lastErrorMsg = $insert->lastErrorMsg;
					return false;
				}
			}
		}

        return true;
    }

    /**
     * Check validity of object
     *
     * A generic method which can (should ?) be overloaded by the child class
     *
     * @return boolean true when object is valid
     *
     */
    public function checkValidity()
    {
        if (!parent::checkValidity())
            return false;

        return true;
    }

    /**
     * Fill-in public properties of the sysobject from a valid XML DOMDocument
     * If a XSL is defined, apply the XSL transformation
     *
     * TODO This override of wcmObject::initFromXMLDocument() is only
     * temporary - see the end of the function definition for more
     * details.
     *
     * @param DOMDocument $domXml   XML DOMDocument representing the object
     * @param string      $xslFile  XSL xsl file (if defined, apply the XSL transformation)
     *
     * @return boolean True on success, false otherwise
     */
    public function initFromXMLDocument($domXml, $xslFile = null)
    {
        // If XSL, Transform XML document
        if ($xslFile)
        {
            // Create XSL document
            $xslDoc = new DOMDocument();
            $xslDoc->load($xslFile);

            // Create XSLT processor
            $xsltProc = new XSLTProcessor;
            $xsltProc->importStyleSheet($xslDoc);

            // Transform XML document
            $xml = $xsltProc->transformToXML($domXml);
            
            // Create XML document
            $domXml = new DOMDocument();
            if (!$domXml->loadXML($xml))
            {
                throw new Exception(_BIZ_INVALID_XML);
            }
        }

        $className = $this->getClass();
        $domXPath = new DOMXPath($domXml);

        // Set object default values
        $this->setDefaultValues();

        // Expected format is <$className> <$propertyName> $propertyValue </$propertyName> ... </$className>
        foreach(getPublicProperties($this) as $property => $value)
        {
            $xpath = '/' .$className . '/' . $property;
            $node = wcmXML::getXPathFirstNode($domXPath, null, $xpath);
            if ($node)
            {
                // Use custom initialization for this property
                $this->initPropertyFromXMLNode($property, $node);
            }
        }

        // Handle chapters
        //
        // TODO: When $chapters is implemented (holding the serialized
        // contents of the chapters), remove this function!
        //
        $chapterNodes = $domXPath->query('/' . $className . '/chapters/item');
        if ($chapterNodes && $chapterNodes->length > 0)
        {
            $chapters = array();
            foreach ($chapterNodes as $chapterNode)
            {
                $xml = wcmXML::getOuterXml($chapterNode);
                $className = 'item'; // that's what it is in the XML

                $chapter = new chapter;
                if (!$chapter->initFromXML($xml, null, $className))
                    return false;

                $chapters[] = $chapter->getAssocArray()->toArray();
            }

            $this->updateChapters($chapters);
        }

        return true;
    }
    
    public function getLogicImmoEditions()
    {
        $editions = array();
        $sql  = "SELECT DISTINCT channelId FROM ".$this->tableName;

	$rs = $this->database->executeQuery($sql);
	if ($rs != null)
	{
	    while ($rs->next())
	    {
		$editions[$rs->get('channelId')] = 'Edition numero '.$rs->get('channelId');
	    }
	}

	return $editions;
    }

    /**
     * Exposes 'inserts' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return An array of inserts getAssocArray
     */
    public function getAssoc_inserts($toXML = false)
    {
        $inserts = array();
        foreach($this->getInserts() as $insert)
        {
            $insertAssoc = $insert->getAssocArray($toXML);
            if ($toXML)
                $insertAssoc = $insertAssoc->toArray();

            $inserts[] = $insertAssoc;
        }
        return $inserts;
    }

    public static function getPublications()
    {
    	$publications = array();
    	for ($i = 1; $i < 19; $i++)
    	{
    		$publications['p'.$i] = 'P '.$i;
    	}
    	return $publications;
    }
    
    public static function getPublicationsYear()
    {
	$publicationsYear = array();
	for ($i = 2010; $i < 2016; $i++)
	{
		$publicationsYear[$i] = $i;
	}
    	return $publicationsYear;
    }

    public static function getKind()
    {
    	return array( 'DO' => 'Dossier',
    			'GUA' => 'Guide Achat',
    			'GUV' => 'Guide Vente',
    			'IN' => 'Invite',
    			'AN' => 'Actu Neuf');
    }
    
    public static function getLocations()
    {
       return array(	'NAT' => 'NAT',
						'001' => '001',
						'006' => '006',
						'011' => '011',
						'030' => '030',
						'033' => '033',
						'036' => '036',
						'039' => '039',
						'040' => '040',
						'044' => '044',
						'045' => '045',
						'048' => '048',
						'053' => '053',
						'068' => '068',
						'075' => '075',
						'088' => '088',
						'092' => '092',
						'094' => '094',
						'101' => '101',
						'102' => '102',
						'106' => '106',
						'110' => '110',
						'118' => '118',
						'123' => '123',
						'152' => '152',
						'156' => '156',
						'160' => '160',
						'173' => '173',
						'177' => '177',
						'210' => '210',
						'226' => '226',
						'260' => '260',
						'261' => '261',
						'277' => '277',
						'280' => '280');
    }

}
