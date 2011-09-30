<?php
/**
 * Project:     WCM
 * File:        biz.chapter.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Definition of a chapter
 */
class chapter extends wcmObject
{
    /**
     * (int) Article Id
     */
    public $articleId;

    /**
     * (int) Chapter rank (in siblings)
     */
    public $rank = 1;

    /**
     * Chapter title
     */
    public $title;

    /**
     * Chapter text (HTML)
     */
    public $text;

    public $author;
    public $link;
    
    public $image_url;
    public $image_caption;
    public $image_credits;
	public $subtitle;
	public $company;
    /**
     * Returns the database used to store object
     *
     * @return wcmDatabase Database used for storage
     */
    protected function getDatabase()
    {
        if (!$this->database)
        {
            // use same DB as article
            $this->database = wcmProject::getInstance()->bizlogic->getBizClassByClassName('article')->getConnector()->getBusinessDatabase();
            $this->tableName = '#__chapter';
        }
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
                case "article":
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "articleId=".$value;
                    break;

                default:
                    // Invalid relation!!
                    if ($sql != null) $sql .= " AND ";
                    $sql .= "0=1";
                    break;
            }
        }
        return $sql;
    }

    /**
     * Exposes 'article' to the getAssocArray
     *
     * @param bool $toXML TRUE if method is called in the context of toXML()
     *
     * @return array The chapter's article getAssocArray (or null if orphan)
     */
    function getAssoc_article($toXML = false)
    {
        if ($toXML) return null;

        $article = $this->getArticle();
        return ($article) ? $article->getAssocArray($toXML) : null;
    }

    /**
     * Returns the article associated to this chapter
     *
     * @return article The chapter's article (or null if orphan)
     */
    function getArticle()
    {
        $article = new article(null, $this->articleId);
        return ($article->id) ? $article : null;
    }

    /**
     * Save object in database and update search table
     *
     * @param array $source array for binding to class vars (or null)
     * @param int   $userId id of user who create ou update the document
     *
     * @return true on success, false otherwise
     *
     */
    public function save($source = null)
    {
        // Save
        if (!parent::save($source))
            return false;

        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getArticle());
    }

    /**
     * Deletes object from database
     *
     * @param bool $indexParent TRUE to re-index parent (true by default)
     *
     * @return true on success or an error message (string)
     */
    public function delete($indexParent = true)
    {
        if(!$indexParent)
            return parent::delete();

        // Delete bizobject
        if (!parent::delete())
            return false;

        // Reindex the parent bizobject (article)
        return wcmBizsearch::getInstance()->indexBizobject($this->getArticle());
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

        if ($this->title  && strlen($this->title) > 255)
        {
            $this->lastErrorMsg = _BIZ_ERROR_CHAPTER_TITLE_TOO_LONG;
            return false;
        }
        
        return true;
    }

    /**
     * Preview bizobject
     * => Execute template 'preview/{className|'default'}.tpl with following parameters
     *          widgetMode  The widget mode (see params)
     *          bizobject   The bizobject
     *          context     The bizobject assoc array
     *
     * @param int $widgetMode Widget display mode (bit field, default is wcmWidget::VIEW_CONTENT)
     * @param string $templateId Optional template to use (default is bizobject->templateId)
     * @param wcmLogger $logger Optional logger (if null, the generator will create its own)
     *
     * @return string Result of template execution
     */
    public function preview($widgetMode = wcmWidget::VIEW_CONTENT, $templateId = null, $logger = null)
    {
        // Find template
        if ($templateId === null)
        {
            $templateId = 'preview/chapter.tpl';
        }
        
        $config = wcmConfig::getInstance();
        if (!file_exists($config['wcm.templates.path'] . $templateId))
        {
            $templateId = 'preview/chapter.tpl';
            if (!file_exists($config['wcm.templates.path'] . $templateId))
            {
                $templateId = 'preview/default.tpl';
            }
        }

        // Execute template
        $generator = new wcmTemplateGenerator($logger, false, $widgetMode);
        return $generator->executeTemplate($templateId, array(
                                                            'widgetMode' => $widgetMode,
                                                            'obizobject' => $this,
                                                            'bizobject' => $this->getAssocArray(false)));
    }
}