<?php
/**
 * Project:     WCM
 * File:        wcm.generator.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * The generator class contains methods to help
 * management of templates and sites generation
 *
 */
class wcmGenerator
{
    /**
     * Cache of generations
     *
     * @access private
     */
    public $generations;

    /**
     * Cache of generation sets
     *
     * @access private
     */
    public $generationSets;

    /**
     * Cache of generationContents
     *
     * @access private
     */
    public $generationContents;

    /**
     * Cache of templates
     *
     * @access private
     */
    public $templates;

    /**
     * Cache of templateCategories
     *
     * @access private
     */
    public $templateCategories;


    /**
     * Returns an array of categories matching a specific path
     *
     * @param string  $path      Path on disc where to look for categories
     *
     * @return An array of categories/directories
     */
     public function getCategories($path=null)
     {
        $config = wcmConfig::getInstance();

        if(!$path)
        {
            $path = $config['wcm.templates.path'];
        }

        $this->templateCategories = array();
        $dir_handle  =  @opendir($path)  or  die("Unable  to  open  path");
        while (false !== ($resource = readdir($dir_handle)))
        {
            if(is_dir($path.$resource) && $resource != '.' && $resource != '..' && $resource != '.svn')
            {
                $this->templateCategories[] = $resource;
            }
        }

        return $this->templateCategories;
    }

     /**
     * Returns an temples in a given path
     *
     * @param string  $path      Path on disc where to look for templates
     *
     * @return An array of template files key => value where key is template name (w/o .tpl) abd value
     * is the absolute path.template name
     */
     public function getTemplatesByPath($relativePath='')
     {
        $config = wcmConfig::getInstance();
        $path = $config['wcm.templates.path'] . $relativePath;

        if($relativePath && substr($relativePath, -1) != '/') $relativePath .= '/';

        $this->templates = array();

        $dir = new DirectoryIterator($path);

        foreach($dir as $resource) {
            if($resource->isFile() && substr($resource->getFilename(),-4)=='.tpl')
            {
                $code = str_replace(".tpl", "", $resource->getFilename());
                $this->templates[$code] = $relativePath . $resource->getFilename();
            }

        }

        return $this->templates;
    }
    /**
     * Return an template object corresponding to a specific id (or null)
     *
     * @param int $id The template id
     *
     * @return The template having given id (or null if id is invalid)
     */
    public function getTemplateById_deprecated($id)
    {
        // Get list from cache
        return getArrayParameter($this->getTemplates_deprecated(), $id, null);
    }

    /**
     * Return an template object corresponding to a specific code (or null)
     *
     * @param string $code The template code
     *
     * @return The template having given code (or null if code was not found)
     */
    public function getTemplateByCode_deprecated($code)
    {
        // Get list from cache
        foreach($this->getTemplates() as $template)
        {
            if (0 == strcasecmp($template->code, $code))
            {
                return $template;
            }
        }
        return null;
    }

    /**
     * Return the ID for a given template code.
     *
     * @param string $code the template code
     *
     * @return int the corresponding template ID
     */
    public function getTemplateIdForCode_deprecated($code)
    {
        $template = $this->getTemplateByCode($code);
        return ($template != null) ? $template->id : null;
    }

    /**
     * Returns an array of templates matching a specific where clause
     *
     * @param string  $where Optional where clause (default is null)
     * @param string  $orderBy Optional order clause (default is 'id')
     * @param boolean $resetCache Optional whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {link @wcmTemplate} objects (keys are ids)
     *
     * This function should not be used. It is still here to be used by the update script
     * updatev3tov32smartyide
     *
     */
    public function getTemplates_deprecated($where = '', $orderBy = 'id', $resetCache = false)
    {
        // Cache objects
        if ($resetCache || $where != '' || !isset($this->templates))
        {
            $enum = new wcmTemplate();
            if (!$enum->beginEnum($where, $orderBy))
            {
                wcmProject::getInstance()->logger->logError('Templates enum failed: ' . $enum->lastErrorMsg);
                return null;
            }

            $templates = array();
            while ($enum->nextEnum())
            {
                $templates[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Don't use cache is there is a where clause
            if ($where != '') return $templates;

            // Cache objects
            $this->templates = $templates;
        }

        return $this->templates;
    }

    /**
     * Return a generation object corresponding to a specific id (or null)
     *
     * @param int $id The generation id
     *
     * @return The {@link wcmGeneration} having given id (or null if id is invalid)
     */
    public function getGenerationById($id)
    {
        // Get list from cache
        return getArrayParameter($this->getGenerations(), $id, null);
    }

    /**
     * Return a generation object corresponding to a specific code (or null)
     *
     * @param String    $code   The generation Code
     *
     * @return The {@link wcmGeneration} having given code (or null if code is invalid)
     */
    public function getGenerationByCode($code)
    {
        foreach($this->getGenerations() as $generation)
            if($generation->code === $code) return $generation;

        return null;
    }

    /**
     * Returns an array of generations
     *
     * @param boolean $resetCache Optional whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {@link wcmGeneration} objects (keys are ids)
     */
    public function getGenerations($resetCache = false)
    {
        // Cache objects
        $cached = wcmCache::fetch('wcmGeneration');
        if ($resetCache || $cached === FALSE)
        {
            $enum = new wcmGeneration();
            if (!$enum->beginEnum())
            {
                wcmProject::getInstance()->logger->logError('Generations enum failed: ' . $enum->lastErrorMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);

            }
            $enum->endEnum();

            // Cache objects
            wcmCache::store('wcmGeneration', $cached);
            wcmCache::delete('wcmGenerationContent');
        }

        return $cached;
    }

    /**
     * Return a generationSet object corresponding to a specific id (or null)
     *
     * @param int $id The generation id
     *
     * @return The {@link wcmGenerationSet} having given id (or null if id is invalid)
     */
    public function getGenerationSetById($id)
    {
        // Get list from cache
        return getArrayParameter($this->getGenerationSets(), $id, null);
    }


    /**
     * Return a generationSet object corresponding to a specific code (or null)
     *
     * @param String    $code   The generation Code
     *
     * @return The {@link wcmGenerationSet} having given code (or null if code is invalid)
     */
    public function getGenerationSetByCode($code)
    {
        foreach($this->getGenerationSets() as $generationSet)
            if($generationSet->code === $code) return $generationSet;

        return null;
    }


    /**
     * Returns an array of generation sets
     *
     * @param boolean $resetCache Optional whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {@link wcmGeneration} objects (keys are ids)
     */
    public function getGenerationSets($resetCache = false)
    {
        // Cache objects
        $cached = wcmCache::fetch('wcmGenerationSet');
        if ($resetCache || $cached === FALSE)
        {
            $enum = new wcmGenerationSet();
            if (!$enum->beginEnum())
            {
                wcmProject::getInstance()->logger->logError('Generation sets enum failed: ' . $enum->lastErrorMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Cache objects
            wcmCache::store('wcmGenerationSet', $cached);
            wcmCache::delete('wcmGeneration');
            wcmCache::delete('wcmGenerationContent');
        }

        return $cached;
    }

    /**
     * Return a generation content object corresponding to a specific id (or null)
     *
     * @param int $id The content generation id
     *
     * @return The {@link wcmGenerationContent} having given id (or null if id is invalid)
     */
    public function getGenerationContentById($id)
    {
        // Get list from cache
        return getArrayParameter($this->getGenerationContents(), $id, null);
    }

    /**
     * Return a generationContent object corresponding to a specific code (or null)
     *
     * @param String    $code   The generationContent Code
     *
     * @return The {@link wcmGenerationContent} having given code (or null if code is invalid)
     */
    public function getGenerationContentByCode($code)
    {
        foreach($this->getGenerationContents() as $generationContent)
            if($generationContent->code === $code) return $generationContent;

        return null;
    }


    /**
     * Returns an array of generation contents
     *
     * @param boolean $resetCache Optional whether to reset the cache, ie. load from DB (default is false)
     *
     * @return An assoc array of {@link wcmGenerationContent} objects (keys are ids)
     */
    public function getGenerationContents($resetCache = false)
    {
        // Cache objects
        $cached = wcmCache::fetch('wcmGenerationContent');
        if ($resetCache || $cached===FALSE)
        {
            $enum = new wcmGenerationContent();
            if (!$enum->beginEnum())
            {
                wcmProject::getInstance()->logger->logError('GenerationContents enum failed: ' . $enum->lastErrorMsg);
                return null;
            }

            $cached = array();
            while ($enum->nextEnum())
            {
                $cached[$enum->id] = clone($enum);
            }
            $enum->endEnum();

            // Cache objects
            wcmCache::store('wcmGenerationContent', $cached);
        }

        return $cached;
    }
}
?>
