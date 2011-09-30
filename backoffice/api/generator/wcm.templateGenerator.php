<?php

/**
 * Project:     WCM
 * File:        wcm.smarty.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * set SMARTY_DIR to absolute path to Smarty library files.
 * if not defined, include_path will be used. Sets SMARTY_DIR only if user
 * application has not already defined it.
 */
if (!defined('SMARTY_DIR'))
{
    define('SMARTY_DIR', dirname(__FILE__) . '/../includes/Smarty/');
}
require_once(SMARTY_DIR . 'Smarty.class.php');
require_once(SMARTY_DIR . 'Smarty_Compiler.class.php');

/**
 * The wcmTemplateGenerator class represents the generator used
 * in smarty to extend native tags (loop, exitloop, load, ..)
 *
 */
class wcmTemplateGenerator extends Smarty
{
    /**
     * The current generation set
     * @var wcmGenerationSet
     */
    public $generationSet;

    /**
     * The current generation
     * @var wcmGeneration
     */
    public $generation;

    /**
     * The current generation content
     * @var wcmGenerationContent
     */
    public $generationContent;

    /**
     * The current template
     * @var wcmTemplate
     */
    public $template;

    /**
     * The logger
     * @var wcmLogger
     */
    public $logger;

    /**
     * The computer output file name for current template
     * @var string
     */
    public $outputFilename;

    /**
     * The folder corresponding to the computed output file name for current template
     * @var string
     */
    public $outputFolder;

    /**
     * Default widget mode
     * @var int (bit field)
     */
    public $widgetMode;

    /**
     * Global variables (associative array)
     * @var array
     */
    public $globals = array();

    // Privates properties
    private $loopInfo;
    private $currentContext;
    private $contextStack;
    private $templateContent;
    private $outputFilenamePattern;
    private $outputRootPath;
    private $loopParameters;

    // matches double quoted strings: "foobar", "foo\"bar"
    private $wcm_db_qstr_regexp = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';

    // matches single quoted strings: 'foobar', 'foo\'bar'
    private $wcm_si_qstr_regexp = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

    // matches single or double quoted strings
    private $wcm_qstr_regexp = '(?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')';

    // matches numerical constants 30, -12, 13.22
    private $wcm_num_const_regexp = '(?:\-?\d+(?:\.\d+)?)';

    /**
     * Parse attribute string
     *
     * @param string $tag_args
     * @return array
     */
    private function wcm_parse_attrs($tag_args)
    {
        /* Tokenize tag attributes. */
        preg_match_all('~(?:' . $this->wcm_qstr_regexp . ' | (?>[^"\'=\s]+)
                         )+ |
                         [=]
                        ~x', $tag_args, $match);
        $tokens       = $match[0];

        $attrs = array();
        /* Parse state:
         *   0 - expecting attribute name
         *   1 - expecting '='
         *   2 - expecting attribute value (not '=')
        */
        $state = 0;

        foreach ($tokens as $token) {
            switch ($state) {
                case 0:
                    /* If the token is a valid identifier, we set attribute name
                       and go to state 1. */
                    if (preg_match('~^\w+$~', $token)) {
                        $attr_name = $token;
                        $state = 1;
                    } else
                        die("invalid attribute name: '".$token."' " . __FILE__ . "line " . __LINE__ );
                    break;

                case 1:
                    /* If the token is '=', then we go to state 2. */
                    if ($token == '=') {
                        $state = 2;
                    } else
                        die("expecting '=' after attribute name '$last_token'" . __FILE__ . "line " . __LINE__ );
                    break;

                case 2:
                    /* If token is not '=', we set the attribute value and go to
                       state 0. */
                    if ($token != '=')
                    {
                        /* We booleanize the token if it's a non-quoted possible
                           boolean value. */
                        if (preg_match('~^(on|yes|true)$~', $token)) {
                            $token = 'true';
                        } else if (preg_match('~^(off|no|false)$~', $token)) {
                            $token = 'false';
                        } else if ($token == 'null') {
                            $token = 'null';
                        } else if (preg_match('~^' . $this->wcm_num_const_regexp . '|0[xX][0-9a-fA-F]+$~', $token)) {
                            /* treat integer literally */
                        }

                        // Remove extra double quotes
                        if ($token[0]=='"' && $token[strlen($token)-1]=='"')
                            $token = substr($token, 1, strlen($token)-2);

                        $attrs[$attr_name] = $token;

                        $state = 0;
                    } else
                        die("'=' cannot be an attribute value " . __FILE__ . " line: " . __LINE__);
                    break;
            }
            $last_token = $token;
        }

        if($state != 0) {
            if($state == 1) {
                die("expecting '=' after attribute name '$last_token'" . __FILE__ . "line " . __LINE__ );
            } else {
                die("missing attribute value '$last_token'" . __FILE__ . "line " . __LINE__ );
            }
        }

        $this->wcm_parse_vars_props($attrs);

        return $attrs;
    }

    /**
     * compile multiple variables and section properties tokens into
     * PHP code
     *
     * @param array $tokens
     */
    private function wcm_parse_vars_props(&$tokens)
    {
        foreach($tokens as $key => $val) {
            $tokens[$key] = $this->wcm_parse_var_props($val);
        }
    }

    /**
     * compile single variable and section properties token into
     * PHP code
     *
     * @param string $val
     * @param string $tag_attrs
     * @return string
     */
    private function wcm_parse_var_props($val)
    {
        $val = trim($val);

        if (preg_match('~^' . $this->wcm_db_qstr_regexp . '$~', $val)) {
                // double quoted text
                preg_match('~^(' . $this->wcm_db_qstr_regexp . ')$~', $val, $match);
                // replace double quoted literal string with no quotes
                return preg_replace('~^"([\s\w]+)"$~',"\\1", $match[1]);
            }
        elseif(preg_match('~^' . $this->wcm_num_const_regexp . '$~', $val)) {
                // numerical constant
                preg_match('~^(' . $this->wcm_num_const_regexp . ')$~', $val, $match);
            }
        elseif(preg_match('~^' . $this->wcm_si_qstr_regexp . '$~', $val)) {
                // single quoted text
                preg_match('~^(' . $this->wcm_si_qstr_regexp . ')$~', $val, $match);
            }
        elseif(!is_numeric($val)) {
                // literal
                return $val;
        }
        return $val;
    }


    /**
     * Constructor
     *
     * @param wcmLogger  $logger      Specific logger (or null to create a new one)
     * @param bool       $stripFilter Enable WrappedStripFilter (optimize code par removing extra white spaces and carriage returns)
     * @param bool       $widgetMode  Default widget mode (default is wcmWidget::VIEW_CONTENT)
     */
    public function __construct($logger = null, $stripFilter = true, $widgetMode = wcmWidget::VIEW_CONTENT)
    {
        $config  = wcmConfig::getInstance();

        $this->widgetMode = $widgetMode;

        // Initialize logger
        if ($logger)
            $this->logger = $logger;
        else
            $this->logger = new wcmLogger($config['wcm.logging.verbose'], $config['wcm.logging.debug'], null, false);

        // Base constructor
        $this->Smarty();

        // Initialize template directories and caching
        $this->temp_dir     = sys_get_temp_dir() . '/wcm/Smarty/';
        $this->compile_dir  = $this->temp_dir . 'templates_c/';
        $this->cache_dir    = $this->temp_dir . 'cache/';
        $this->config_dir   = $config['smarty.path'].'configs/';
        $this->template_dir = $config['wcm.templates.path'];

        // Create template directories if necessary
        makeDirectory($this->template_dir);
        makeDirectory($this->compile_dir);
        makeDirectory($this->config_dir);
        makeDirectory($this->cache_dir);

        // Add special variables
        $this->assign_by_ref('this', $this);
        $assocConfig = wcmConfig::getAssocInstance();
        $this->assign('config', $assocConfig);
        $this->assign('session', wcmSession::getInstance()->getAssocArray(false));

        //
        // Register blocks and special functions
        //

        // globals
        $this->register_function('global', array($this, 'func_global'));
        $this->globals = array();
        $this->assign_by_ref('globals', $this->globals);

        // context
        $this->register_block('context', array($this, 'func_context'));
        $this->contextStack = array();
        $this->currentContext = array();
        $this->assign_by_ref('contextStack', $this->contextStack);
        $this->assign_by_ref('context', $this->currentContext);

        // dump
        $this->register_function('dump', array($this, 'func_dump'));

        // load...
        $this->register_function('load', array($this, 'func_load'));
        $this->register_function('unload', array($this, 'func_unload'));
        $this->register_function('loadArray', array($this, 'func_loadArray'));
        $this->register_function('count', array($this, 'func_count'));

        // loop
        $this->register_compiler_function('loop', array($this, 'func_loop'));
        $this->register_compiler_function('/loop', array($this, 'func_endloop'));
        $this->register_compiler_function('exitloop', array($this, 'func_exitloop'));
        $this->loopInfo = array();
        $this->assign_by_ref('loop', $this->loopInfo);

        // search
        $this->register_compiler_function('search', array($this, 'func_search'));
        $this->register_compiler_function('/search', array($this, 'func_endsearch'));
        $this->register_compiler_function('exitsearch', array($this, 'func_exitsearch'));

        // zone
        $this->register_compiler_function('zone', array($this, 'func_zone'));

        // Widget
        $this->register_function('widget', array($this, 'func_widget'));
    }

    /**
     * Destructor
     */
    function __destruct()
    {
        // Free resources
        unset($this->logger);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    // INTERNAL OBJECT MANAGEMENT (generation set, generation, generationContent, template //
    /////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Open a generation set
     *
     * @param int $generationSetId Id of generation set object
     *
     */
    private function openGenerationSet($generationSetId)
    {
        // Reset context stack
        $this->contextStack = array();
        $this->currentContext = array();

        // Load generation object
        $this->generationSet = new wcmGenerationSet();
        $this->generationSet->refresh($generationSetId);

        // Adjust output root path to ensure it ends with a '/'
        $this->outputRootPath = $this->generationSet->location;
        $lastchar = substr($this->outputRootPath, -1);
        if ($lastchar != '/' && $lastchar != '\\')
            $this->outputRootPath .= '/';

        // Add generation context
        $context = $this->createContext($this->generationSet->context);
        $this->pushContext($context);

        $this->logger->logVerbose("Executing generation set ".$this->generationSet->name." at ".$this->outputRootPath." with context ".$this->generationSet->context);
    }

    /**
     * Open a generation
     *
     * @param int $generationId Id of generation object
     *
     */
    private function openGeneration($generationId)
    {
        // Load generation object
        $this->generation = new wcmGeneration();
        $this->generation->refresh($generationId);

        if (!$this->generation->id)
        {
            $this->logger->logError($generationId.' is not a valid generation ID');
        }

        // open generationSet?
        if (!$this->generationSet || $this->generationSet->id != $this->generation->generationSetId)
        {
            $this->openGenerationSet($this->generation->generationSetId);
        }

        // if generation location is defined, override generationSet location
        if ($this->generation->location)
        {
            $this->outputRootPath = $this->generation->location;
            $lastchar = substr($this->outputRootPath, -1);
            if ($lastchar != '/' && $lastchar != '\\')
                $this->outputRootPath .= '/';
        }

        // Add generation context
        $context = $this->createContext($this->generation->context);
        $this->pushContext($context);

        $this->logger->logVerbose("Executing generation ".$this->generation->name." at ".$this->outputRootPath." with context ".$this->generation->context);
    }

    /**
     * Close generation set
     *
     */
    private function closeGenerationSet()
    {
        $this->logger->logVerbose("Generation set ".$this->generationSet->name." ended");

        $this->generationSet = null;

        // Remove generation context
        $this->popContext();
    }

    /**
     * Close generation
     *
     */
    private function closeGeneration()
    {
        $this->logger->logVerbose("Generation ".$this->generation->name." ended");

        $this->generationContent = null;
        $this->generation = null;

        // Remove generation context
        $this->popContext();
    }

    /**
     * Loads parameters from current generation content
     *
     * @param int $generationContentId Id of generationContent to load (or null to use current)
     */
    private function openGenerationContent($generationContentId = null)
    {
        // Load template
        $this->openTemplate($this->generationContent->templateId);

        $this->outputFilenamePattern = $this->generationContent->namingRule;
        $this->loopParameters = trim($this->generationContent->loop);

        // Add generationContent context
        $context = $this->createContext($this->generationContent->context);
        $this->pushContext($context);

        $this->logger->logVerbose("Executing generationContent ". $this->generationContent->name . " with context " . $this->generationContent->context);
    }

    /**
     * Close generation content
     */
    private function closeGenerationContent()
    {
        // Unload template
        $this->closeTemplate();

        $this->generationContent = null;

        // Remove generation context
        $this->popContext();
    }

    /**
     * Open a template
     *
     * @param int $generationContentId Id of template to load (or null to use current)
     */
    private function openTemplate($templateId)
    {
        // Load template
        $this->template = new wcmTemplate();
        $this->template->refresh($templateId);
        $this->templateContent = $this->template->content;
    }

    /**
     * Close current template
     */
    private function closeTemplate()
    {
        $this->template = null;
        $this->templateContent = null;
    }

    //////////////////////////////////////////////////////////////////////////////////
    // CONTEXT MANAGEMENT
    //////////////////////////////////////////////////////////////////////////////////

    /**
     * Push a context array in the context stack
     *
     * @param array $context Associative array representing context to push
     */
    private function pushContext($context)
    {
        // Ensure we have an array
        if ($context==null || !is_array($context)) $context = array();

        // Note: when a context is pushed, it overload the existing one
        // we have to retrieve the current one and overload with new one
        $newContext = $this->getContext();
        foreach($context as $key => $val)
        {
            $newContext[$key] = $val;
        }

        // Push context and update current context variable
        $this->contextStack[] = $newContext;
        $this->currentContext = $this->getContext();

        $this->logger->logVerbose('Pushed context: ' . print_r($this->currentContext, true));
    }

    /**
     * Pop last context out of context stack
     */
    private function popContext()
    {
        if (count($this->contextStack) > 0)
        {
            // Pop context and update current context variable
            array_pop($this->contextStack);
            $this->currentContext = $this->getContext();

            $this->logger->logVerbose('Popped context: ' . print_r($this->currentContext, true));
        }
        else
        {
            $this->logger->logError('Unexpected POP context as contextStack is empty!');
        }
    }

    /**
     * Returns the current context (on top of context stack)
     *
     * @return array Associative array representing current context
     */
    private function getContext()
    {
        $n = count($this->contextStack);
        if ($n > 0)
        {
            // Return last context (top of stack)
            return $this->contextStack[$n-1];
        }

        // Return an empty array
        return array();
    }

    /**
     * Create an assoc array containing the variable extracted from a context string
     *
     * @param string $values A context string (e.g. "class1[.id]=value1, class2[.id]=value2, ...")
     * @return array Assoc array
     */
    private function createContext($values)
    {
		print_r($values);
		
        $context = array();
        $elements = explode(',', $values);
        foreach($elements as $element)
        {
            // Expect '='
            if (strpos($element, '='))
            {
                $parts = explode('=', $element, 2);

                $key = $parts[0];
                $val = $parts[1];
                // only supress one of the surrounding value...
                if ($val[0] == '"' || $val[0] == "'" ) $val[0] = ' ';
                $lastIdx = mb_strlen($val) - 1;
                if ($val[$lastIdx] == '"' || $val[$lastIdx] == "'") $val[$lastIdx] = ' ';

                $key = trim($parts[0]);
                $val = trim($parts[1]);

                // Remove extra '.id' in key
                if (substr($key, -3) == '.id')
                {
                    $key = substr($key, 0, strlen($key)-3);
                }

                // Add key/value pair if the key is not empty after trimming
                if ($key != '' && $val != '')
                {
                    $context[$key] = $val;
                }
            }
        }

        $this->logger->logVerbose('Create context from ' . $values . ': ' . print_r($context, true));
        return $context;
    }

    /**
     * Extra the associative array needed to computre an ofClause from current context
     *
     * @param string $of The of clause (from a {loop} tag or multipleGeneration)
     *
     * @return array Associative array used to compute ofClause()
     */
    private function extractContext($of)
    {
        // Prepare result
        $ofContext = array();

        // Retrieve current context
        $context = $this->getContext();


        // Extract the list of classes to retrieve
        $classes = explode(',', $of);
        foreach($classes as $key)
        {
            $key = trim($key);
            if (isset($context[$key]))
            {
                $ofContext[$key] = $context[$key];
            }
        }

        return $ofContext;
    }

    //////////////////////////////////////////////////////////////////////////////////
    // EXECUTION (generate template, generationContent, generation)
    //////////////////////////////////////////////////////////////////////////////////

    /**
     * Compute output filename from pattern
     *
     * @param string $sPattern The filename pattern
     */
    private function computeOutputFileName($sPattern)
    {
        $tempFilename = tempnam($this->temp_dir, 'tpl');
		//print_r($tempFilename);
		//echo "\n---\n\n";exit();

        saveToFile($tempFilename, $sPattern);

        $outputFilename = $this->fetch($tempFilename);
		
		removeFile($tempFilename);

        // Append root path ?
        if ($this->outputRootPath != '')
        {
            // Do not append root path if output file name is absolute (linux/windows/mac)
            if ($outputFilename[0]!='/' && $outputFilename[1]!=':' && $outputFilename[0]!=':')
            {
                // Parse root path through smarty engine
                $tempFilename = tempnam($this->temp_dir, 'tpl/');
                saveToFile($tempFilename, $this->outputRootPath);
                $outputPath = $this->fetch($tempFilename);
                removeFile($tempFilename);

                $outputFilename = $outputPath . $outputFilename;
            }
        }
        return $outputFilename;
    }

    /**
     * Compute output from template content
     *
     * @param string $sPattern The template source code
     */
    private function computeFileContent($sPattern)
    {
        $tempFilename = tempnam($this->temp_dir, 'tpl');

        saveToFile($tempFilename, $sPattern);
        $result = $this->fetch($tempFilename);
        removeFile($tempFilename);

        return $result;
    }

    /**
     * Execute a single template
     *
     * @param int $templateId Id of template to execute
     * @param array $parameters Extra parameters (assoc array) used to create smarty variables
     *
     * @return string Template execution result
     */
    public function executeTemplate($templateId, $parameters = array())
    {
        // Reset context
        $this->generation = null;
        $this->generationContent = null;

        // Add variables
        if (is_array($parameters))
        {
            foreach($parameters as $name => $value)
            {
                $this->assign($name, $value);
            }
        }
        // Load and execute template
        $this->openTemplate($templateId);
        return $this->computeFileContent($this->templateContent);
    }

    /**
     * Execute a specific generation set
     *
     * @param int $generationSetId   Id of generation to execute
     * @param array $extraContext An optional assoc array to extend generation context
     * @param boolean   $generateFile         Indicates if the output must be a file or echoed
     *
     */
    public function executeGenerationSet($generationSetId, $extraContext = null, $generateFile = TRUE)
    {
        $this->openGenerationSet($generationSetId);
        foreach ($this->generationSet->getGenerations() as $generation)
        {
            $this->executeGeneration($generation->id, $extraContext, $generateFile);
        }
        $this->closeGenerationSet();
    }

    /**
     * Execute a specific generation
     *
     * @param int $generationId   Id of generation to execute
     * @param array $extraContext An optional assoc array to extend generation context
     * @param boolean   $generateFile   Indicates if the output must be a file or echoed
     *
     * @return array Empty array when $generateFile is set to TRUE or an array of associative arrays
     *         (structured as "name" => output file name, "content => generation result)
     *         or null on error
     */
    public function executeGeneration($generationId, $extraContext = null, $generateFile = TRUE)
    {
        $results = array();

        // Open generation
        $this->openGeneration($generationId);

        // Execute each generation content
        $contents = $this->generation->getContents();
        foreach($contents as $gcId => $generationContent)
        {
            // Execute generation content (and avoid re-opening of current generation
            $result = $this->executeGenerationContent($gcId, $extraContext, $generateFile, TRUE);
            $results[] = $result;
        }

        // Close generation
        $this->closeGeneration();

        return $results;
    }

    /**
     * Execute a specific generation content
     *
     * @param int       $generationContentId  Generation content ID
     * @param array     $extraContext         An optional Assoc array to extend generation context
     * @param boolean   $generateFile         Indicates if the output must be a file or echoed
     * @param boolean   $doNotOpenGeneration  Do not open generation (WARNING: for internal use only. Set to FALSE)
     *
     * @return array Empty array when $generateFile is set to TRUE or an array of associative arrays
     *         (structured as "name" => output file name, "content => generation result)
     *         or null on error
     */
    public function executeGenerationContent($generationContentId, $extraContext = null, $generateFile = TRUE, $doNotOpenGeneration = FALSE)
    {
        $result = null;

        $gc = new wcmGenerationContent();
        if (!$gc->refresh($generationContentId))
        {
            return null;
        }

        // Open generation is needed
        if (!$doNotOpenGeneration) $this->openGeneration($gc->generationId);

        // Open generation content
        $this->generationContent = $gc;
        $this->openGenerationContent();

        // Single or multiple generation (loop?)
        if ($this->loopParameters == '')
        {
            $result = $this->singleGeneration($extraContext, $generateFile);
        }
        else
        {
            $result = $this->multipleGeneration($extraContext, $generateFile);
        }

        // Close generation content
        $this->closeGenerationContent();

        // Close generation is needed
        if (!$doNotOpenGeneration) $this->closeGeneration();

        return $result;
    }

    /**
     * Performs a single generation
     *
     * @param array $extraContext Assoc array containing extra information for context
     * @param boolean $generateFile Indicates if the output must be a file or echoed
     *
     * @return array Empty array when $generateFile is set to TRUE or associative array
     *         (structured as "name" => output file name, "content => generation result)
     *         or null on error
     */
    private function singleGeneration($extraContext = null, $generateFile = true)
    {
        // Push extra context
        $this->pushContext($extraContext);

        // Compute output filename and execute template
        $this->outputFilename = $this->computeOutputFileName($this->outputFilenamePattern);
        $this->outputFolder = dirname($this->outputFilename) . '/';

        // Generate into array
        $this->logger->logMessage('Generation of: ' . $this->outputFilename);
        $result = array('name' => $this->outputFilename, 'content' => $this->computeFileContent($this->templateContent));

        // Generate file?
        if ($generateFile)
        {
            saveToFile($this->outputFilename, $result['content']);
            $result = array();
        }

        // Pop extra context
        $this->popContext();

        return $result;
    }

    /**
     * Performs a multiple generation (act as a loop in generationContent)
     *
     * @param array $extraContext Assoc array containing extra information for context
     * @param boolean $generateFile Indicates if the output must be a file or echoed
     *
     * @return array Empty array when $generateFile is set to TRUE or an array of associative arrays
     *         (structured as "name" => output file name, "content => generation result)
     *         or null on error
     */
    private function multipleGeneration($extraContext = null, $generateFile = true)
    {
        // Parse loop parameters
        $tParams = $this->wcm_parse_attrs($this->loopParameters);
        $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
        $assocname = getArrayParameter($tParams, 'name',   $tablename);
        $objname   = getArrayParameter($tParams, 'object', 'o'.$tablename);
        $of        = getArrayParameter($tParams, 'of', null);
        $where     = getArrayParameter($tParams, 'where', null);
        $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
        $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
        $limit     = getArrayParameter($tParams, 'limit', -1);
        $to        = getArrayParameter($tParams, 'to', -1);
        if ($to != -1) $limit = ($to - $from + 1);
        $this->pushContext($extraContext);
        $ofContext = $this->extractContext($of);

        //
        // Parse where clause through smarty engine!
        //
        if ($where && strpos($where, '{') !== null)
        {
            // Parse root path through smarty engine
            $tempFilename = tempnam($this->temp_dir, 'tpl');
            saveToFile($tempFilename, $where);
            $where = $this->fetch($tempFilename);
            removeFile($tempFilename);
        }

        if ($tablename == null)
        {
            $this->logger->logError('Multiple generation fatal error: [class] parameter in loop is mandatory');
            $this->popContext();
            return null;
        }

        // Prepare result
        $globalResult = array();

        // Push extra context

        // Iterate bizobjects (loop)
        $bizobject  = new $tablename();
        $assocarray = array();
        $this->assign_by_ref($objname, $bizobject);
        if ($bizobject->beginEnum($where, $order, $from, $limit, $ofContext))
        {
            while($bizobject->nextEnum())
            {
                // Push context for current loop
                $this->pushContext(array($tablename => $bizobject->id));

                // Update assoc array
                $assocarray = $bizobject->getAssocArray(false);
                $this->assign_by_ref($assocname, $assocarray);

                // Compute output filename and execute template
                $this->outputFilename = $this->computeOutputFileName($this->outputFilenamePattern);
                $this->outputFolder = dirname($this->outputFilename) . '/';

                // Generate into array
                $this->logger->logMessage("Generation of: " . $this->outputFilename);
                $result = array('name' => $this->outputFilename, 'content' => $this->computeFileContent($this->templateContent));

                // Generate file?
                if ($generateFile)
                {
                    saveToFile($this->outputFilename, $result['content']);
                    $result = array();
                }
                else
                {
                    // Push result into global result
                    $globalResult[] = $result;
                }
		/* OPTIMISATION NSTEIN 29/06/2009
                sleep(1);
		*/

                // Pop context for current loop
                $this->popContext();
            }
            $bizobject->endEnum();
        }
        else
        {
            $this->logger->logError("error enumerating $tablename when generating content {$this->generationContent->id} file " . __FILE__ . " line " . __LINE__);
            $this->logger->logError($bizobject->getErrorMsg());
            $this->popContext();
            return null;
        }
        $this->clear_assign($objname);
        $this->clear_assign($assocname);

        // Pop extra context
        $this->popContext();

        return $globalResult;
    }

    //////////////////////////////////////////////////////////////////////////////////
    // RESOURCE MANAGEMENT (db)
    //////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles "db" resource
     *
     * @param string    $code       Template code (or template id if numeric)
     * @param string   &$tpl_source Template source
     * @param string   &$smarty_obj Smarty object
     *
     */
    public function db_get_template($id, &$tpl_source, &$smarty_obj)
    {
        // Load template content by its code or by its id ?
        $id = (is_numeric($code)) ? intval($code) : 0;

        $this->template = new wcmTemplate();
        $this->template->refresh($templateId);
        // Retrieve source
        if ($template != null)
        {
            $tpl_source = $template->content;
            return true;
        }
        else
        {
            $tpl_source = null;
            return false;
        }
    }

    /**
     * Handles "db" resource
     *
     */
    public function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
    {
        $tpl_timestamp = time();
        return true;
    }

    /**
     * Handles "db" resource
     *
     */
    public function db_get_secure($tpl_name, &$smarty_obj)
    {
        // always safe
        return true;
    }

    /**
     * Handles "db" resource
     *
     */
    public function db_get_trusted($tpl_name, &$smarty_obj)
    {
        // unused
        return true;
    }

    //////////////////////////////////////////////////////////////////////////////////
    // EXTRA TAGS AND BLOCKS
    //////////////////////////////////////////////////////////////////////////////////

    /**
     * Handles {global} tag
     *
     */
    public function func_global($tParams, &$oSmarty)
    {
        $varname = getArrayParameter($tParams, "var", null);
        $value = getArrayParameter($tParams, "value", null);

        if ($varname)
        {
            $this->globals[$varname] = $value;
            $this->logger->logVerbose("gobal assign " . $varname . " to " . $value);
        }
    }

    /**
     * Handles {dump} tag
     *
     */
    public function func_dump($tParams, &$oSmarty)
    {
        // dump output
        $filename = getArrayParameter($tParams, "file", null);
        $content  = getArrayParameter($tParams, "content", null);
        $utf8 = (null != getArrayParameter($tParams, "utf8", null));

        if ($filename)
        {
            saveToFile($filename, $content, false, $utf8);
            $this->logger->logMessage('Generation of: ' . $filename);
        }
    }

    /**
     * Handles {loadArray} tag
     *
     */
    public function func_loadArray($tParams, &$oSmarty)
    {
        // Parse parameters
        $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
        $assocname = getArrayParameter($tParams, 'name',   $tablename);
        $objname   = getArrayParameter($tParams, 'object', 'o'.$tablename);
        $of        = getArrayParameter($tParams, 'of', null);
        $where     = getArrayParameter($tParams, 'where', null);
        $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
        $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
        $limit     = getArrayParameter($tParams, 'limit', -1);
        $to        = getArrayParameter($tParams, 'to', -1);
        if ($to != -1) $limit = ($to - $from + 1);
        $ofContext = $this->extractContext($of);

        if ($tablename == null)
        {
            $this->logger->logError('loadArray fatal error: [class] parameter is mandatory');
            return null;
        }

        // Prepare array and enumerate bizobject
        $bigarray = array();
        $bizobject = new $tablename();
        if ($bizobject->beginEnum($where, $order, $from, $limit, $ofContext))
        {
            while($bizobject->nextEnum())
            {
                $bigarray[] = $bizobject->getAssocArray(false);
            }
            $bizobject->endEnum();

        }
        else
        {
            $this->logger->logError('loadArray fatal error: enumerating ' . $tablename . ' failed: ' . $bizobject->getErrorMsg());
        }

        // Expose array
        $this->assign($assocname, $bigarray);
    }

    /**
     * Handles {load} tag
     *
     */
    public function func_load($tParams, &$oSmarty)
    {
        // Parse parameters
        $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
        $assocname = getArrayParameter($tParams, 'name',   $tablename);
        $objname   = getArrayParameter($tParams, 'object', 'o'.$tablename);
        $of        = getArrayParameter($tParams, 'of', null);
        $where     = getArrayParameter($tParams, 'where', null);
        $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
        $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
        $ofContext = $this->extractContext($of);

        if ($tablename == null)
        {
            $this->logger->logError('load fatal error: [class] parameter is mandatory');
            return null;
        }

        // Enumerate bizobject
        $bizobject = new $tablename();
        $assocarray = array();
        if ($bizobject->beginEnum($where, $order, $from, 1, $ofContext))
        {
            if ($bizobject->nextEnum())
            {
                // Expose object and assocarray
                $this->logger->logVerbose("load class=$tablename where=$where of=$of offset=$from");
                $this->assign_by_ref($objname, $bizobject);
                $this->assign($assocname, $bizobject->getAssocArray(false));
            }
            else
            {
                $this->logger->logError('load failed: enumerating ' . $tablename . ' returns no result');
            }
            $bizobject->endEnum();
        }
        else
        {
            $this->logger->logError('load fatal error: enumerating ' . $tablename . ' failed: ' . $bizobject->getErrorMsg());
        }
    }

    /**
     * Handles {unload} tag
     *
     */
    public function func_unload($tParams, &$oSmarty)
    {
        // Parse parameters
        $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
        $assocname = getArrayParameter($tParams, 'name',   $tablename);
        $objname   = getArrayParameter($tParams, 'object', 'o'.$tablename);

        // Unload assoc array and object
        $this->clear_assign($assocname);
        $this->clear_assign($objname);
    }

    /**
     * Handles {context} tag
     */
    public function func_context($tParams, $content, &$oSmarty, &$repeat)
    {
        if (!isset($content))
        {
            // handle open tag
            $values = getArrayParameter($tParams, 'values', null);
            $this->logger->logVerbose('Overload context with values ' . $values);
            $context = $this->createContext($values);
            $this->pushContext($context);
        }
        else
        {
            // handle closing tag
            $this->logger->logVerbose('Restore previous context');
            $this->popContext();
            return $content;
        }
    }

    /**
     * Handles {count} tag
     *
     */
    public function func_count($tParams, &$oSmarty)
    {
        // Parse parameters
        $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
        $varname   = getArrayParameter($tParams, 'var',   $tablename.'Count');
        $of        = getArrayParameter($tParams, 'of', null);
        $where     = getArrayParameter($tParams, 'where', null);
        $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
        $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
        $limit     = getArrayParameter($tParams, 'limit', -1);
        $to        = getArrayParameter($tParams, 'to', -1);
        if ($to != -1) $limit = ($to - $from + 1);
        $ofContext = $this->extractContext($of);

        if ($tablename == null)
        {
            $this->logger->logError('count fatal error: [class] parameter is mandatory');
            return null;
        }

        // Enumerate bizobject
        $bizobject = new $tablename();
        if ($bizobject->beginEnum($where, $order, $from, $limit, $ofContext))
        {
            $this->assign($varname, $bizobject->enumCount());
            $bizobject->endEnum();
        }
        else
        {
            $this->logger->logError('count fatal error: enumerating ' . $tablename . ' failed: ' . $bizobject->getErrorMsg());
        }
        unset($bizobject);
    }

    /**
     * Handles {zone} tag
     *
     */

    public function func_zone($tParams, &$oSmarty)
    {
        $params = $this->wcm_parse_attrs($tParams);

        $bizobject = getArrayParameter($params, "bizobject", null);
        if ($bizobject == null)
        {
            $this->logger->logError("error missing [bizobject] parameter in zone tag; file ".__FILE__." line ".__LINE__);
            return;
        }

        $name = getArrayParameter($params, "name", null);
        if ($name == null)
        {
            $this->logger->logError("error missing [name] parameter in zone tag; file ".__FILE__." line ".__LINE__);
            return;
        }

        $output = '';

        $output .= '$_bizobject = $this->_tpl_vars["' . $bizobject . '"];';
        $output .= '$_content = new wcmDesignZone(get_class($_bizobject), $_bizobject->id, "'.$name.'");';
        $output .= '$_content->refresh();';
        $output .= '$_contentArray = $_content->getZoneContent();';
        $output .= 'if (is_array($_contentArray) && count($_contentArray) > 0) {';
        $output .= 'foreach($_contentArray as $_blockGuid => $_block) {';
        $output .= '$_blockId = $_block["name"] . "-" . $_blockGuid;';
        $output .= '$_blockTitle = $_blockClass = $_blockIndex = $_block["name"];';
        $output .= '$_smarty_tpl_vars = $this->_tpl_vars;';
        $output .= '$_widget = new $_block["name"]($this->widgetMode, $_block["settings"], $_bizobject, $_blockGuid);';
        $output .= 'echo $_widget->display();';
        $output .= '$this->_tpl_vars = $_smarty_tpl_vars; unset($_smarty_tpl_vars); }';
        $output .= '}';

        return $output;
    }

    /**
     * Handles {loop} tag
     *
     * => This code is inspired from smarty code (foreach)
     */
    function func_loop($tag_args, &$smarty)
    {
        $arg_list = $smarty->_compile_arg_list('block', 'loop', $smarty->_parse_attrs($tag_args), $cache);

        $output  = '';
        $output .= '$_loop_repeat = true;'."\n";
        $output .= '$this->_tag_stack[] = array("loop", array(' . implode(',', $arg_list) . '));'."\n";
        $output .= '$this->handle_loop($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_loop_repeat, false);'."\n";
        $output .= 'while ($_loop_repeat) {'."\n";
        $output .= '    ob_start();'."\n";

        return $output;
    }

    /**
     * Handles {exitloop} tag
     *
     * => This code is inspired from smarty code (foreach)
     */
    function func_exitloop($tag_args, &$smarty)
    {
        $output  = '';
        $output .= '    $_loop_repeat = false; $_block_content = ob_get_contents(); ob_end_clean();'."\n";
        $output .= '    echo $this->handle_loop($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_loop_repeat, true);'."\n";
        $output .= '    break;'."\n";

        return $output;
    }

    /**
     * Handles {/loop} tag
     *
     * => keep comment for memory as this code is inspired from smarty code
     *    and smarty code is subject to evolve. will be helpful to locate
     *    into smarty code.
     *
     */
    function func_endloop($tag_args, &$smarty)
    {
        $output  = '';
        $output .= '    $_loop_repeat = false; $_block_content = ob_get_contents(); ob_end_clean();'."\n";
        $output .= '    echo $this->handle_loop($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_loop_repeat, false);'."\n";
        $output .= '}'."\n";
        $output .= 'array_pop($this->_tag_stack);'."\n";

        return $output;
    }

    /**
     * This function is called during template execution ("func_loop", "func_exitloop" and
     * "func_endloop" methods write PHP code to call this function when appropriate)
     *
     * @param array   $tParams  Loop parameters
     * @param string  $content  Smarty content (unset when {loop} start)
     * @param Smarty  &$oSmarty Smarty object
     * @param boolean &$repeat  TRUE to repeat block
     * @param boolean $bExit    TRUE to exit loop
     *
     * @access private
     */
    function handle_loop($tParams, $content, &$oSmarty, &$repeat, $bExit)
    {
        if (!isset($content))
        {
            // Handle opening tag {loop}


            //Parse parameters
            $tablename = getArrayParameter($tParams, 'class', getArrayParameter($tParams, 'table', null));
            $assocname = getArrayParameter($tParams, 'name',   $tablename);
            $objname   = getArrayParameter($tParams, 'object', 'o'.$tablename);
            $of        = getArrayParameter($tParams, 'of', null);
            $where     = getArrayParameter($tParams, 'where', null);
            $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
            $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
            $limit     = getArrayParameter($tParams, 'limit', -1);
            $to        = getArrayParameter($tParams, 'to', -1);
            if ($to != -1) $limit = ($to - $from + 1);
            $ofContext = $this->extractContext($of);

            if ($tablename == null)
            {
                $this->logger->logError('loop fatal error: [class] parameter is mandatory');

                // Exit loop!
                $repeat = false;
                return null;
            }

            // Check if there is another loop with same assocname
            if (isset($this->loopInfo[$assocname]) && $this->loopInfo[$assocname]['iteration'] != -1)
            {
                $this->logger->logError('loop fatal error: loop conflit: a loop with same [name] parameter has already started');

                // Exit loop!
                $repeat = false;
                return null;
            }

            // Enumerate bizobject and store it in current tag
            $this->logger->logVerbose("loop class=$tablename offset=$from limit=$limit");
            $bizobject = new $tablename();
            $assocarray = array();

            // Points to current tag values in the tag stack
            $i = count($this->_tag_stack) - 1;
            $this->_tag_stack[$i][1]['o'] = $bizobject;
            $this->_tag_stack[$i][1]['objname'] = $objname;
            $this->_tag_stack[$i][1]['assocname'] = $assocname;

            // Enumeration failed?
            if (!$bizobject->beginEnum($where, $order, $from, $limit, $ofContext))
            {
                $this->logger->logError('loop fatal error: enumerating ' . $tablename . ' failed: ' . $bizobject->getErrorMsg());
                $repeat = false;
                return null;
            }

            // Create loop info (even for empty loop)
            $this->createLoopInfo($assocname, $from, $bizobject->enumCount());

            // Empty loop?
            if (!$bizobject->nextEnum())
            {
                $repeat = false;
                return null;
            }

            // Expose bizobject and assocarray
            $this->assign_by_ref($objname, $bizobject);
            $this->assign($assocname, $bizobject->getAssocArray(false));

            // Update context
            $this->pushContext(array($tablename => $bizobject->id));
        }
        else
        {
            // Handle closing tag {/loop} or exiting tag {exitloop}

            // Retrieve enumerated bizobject
            $i = count($this->_tag_stack) - 1;
            $objname   = $this->_tag_stack[$i][1]['objname'];
            $assocname = $this->_tag_stack[$i][1]['assocname'];
            $bizobject = $this->_tag_stack[$i][1]['o'];

            if ($bExit || !$bizobject->nextEnum())
            {
                // Restore context
                $this->popContext();

                // Exitloop (or end of loop)
                $this->logger->logVerbose(($bExit) ? "exitloop" : "endloop");
                $bizobject->endEnum();
                $this->clear_assign($objname);
                $this->clear_assign($assocname);
                $this->clearLoopInfo($assocname);
                $repeat = false;
            }
            else
            {
                // Update context
                $this->popContext();
                $this->pushContext(array($bizobject->getClass() => $bizobject->id));

                // Refresh assocarray
                $this->assign($assocname, $bizobject->getAssocArray(false));
                $this->updateLoopInfo($assocname);
                $repeat = true;
            }

            return $content;
        }
    }

    /**
     * Handles {search} tag
     *
     * => This code is inspired from smarty code (foreach)
     */
    function func_search($tag_args, &$smarty)
    {
        $arg_list = $smarty->_compile_arg_list('block', 'search', $smarty->_parse_attrs($tag_args), $cache);

        $output  = '';
        $output .= '$_search_repeat = true;'."\n";
        $output .= '$this->_tag_stack[] = array("search", array(' . implode(',', $arg_list) . '));'."\n";
        $output .= '$this->handle_search($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_search_repeat, false);'."\n";
        $output .= 'while ($_search_repeat) {'."\n";
        $output .= '    ob_start();'."\n";

        return $output;
    }

    /**
     * Handles {exitsearch} tag
     *
     * => This code is inspired from smarty code (foreach)
     */
    function func_exitsearch($tag_args, &$smarty)
    {
        $output  = '';
        $output .= '    $_search_repeat = false; $_block_content = ob_get_contents(); ob_end_clean();'."\n";
        $output .= '    echo $this->handle_search($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_search_repeat, true);'."\n";
        $output .= '    break;'."\n";

        return $output;
    }

    /**
     * Handles {/search} tag
     *
     * => keep comment for memory as this code is inspired from smarty code
     *    and smarty code is subject to evolve. will be helpful to locate
     *    into smarty code.
     *
     */
    function func_endsearch($tag_args, &$smarty)
    {
        $output  = '';
        $output .= '    $_search_repeat = false; $_block_content = ob_get_contents(); ob_end_clean();'."\n";
        $output .= '    echo $this->handle_search($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_search_repeat, false);'."\n";
        $output .= '}'."\n";
        $output .= 'array_pop($this->_tag_stack);'."\n";

        return $output;
    }


    /**
     * This function is called during template execution ("func_search", "func_exitsearch" and
     * "func_endsearch" methods write PHP code to call this function when appropriate)
     *
     * @param array   $tParams  search parameters
     * @param string  $content  Smarty content (unset when {search} start)
     * @param Smarty  &$oSmarty Smarty object
     * @param boolean &$repeat  TRUE to repeat block
     * @param boolean $bExit    TRUE to exit search
     *
     * @access private
     */
    function handle_search($tParams, $content, &$oSmarty, &$repeat, $bExit)
    {
        if (!isset($content))
        {
            // Handle opening tag {search}
            $config = wcmConfig::getInstance();

            //Parse parameters
            $query     = getArrayParameter($tParams, 'where', getArrayParameter($tParams, 'where', null));
            $assocname = getArrayParameter($tParams, 'name',   getArrayParameter($tParams, 'name', 'bizobject'));
            $order     = getArrayParameter($tParams, 'orderby', getArrayParameter($tParams, 'order', null));
            $from      = getArrayParameter($tParams, 'offset', getArrayParameter($tParams, 'from', 0));
            $limit     = getArrayParameter($tParams, 'limit', -1);
            $to        = getArrayParameter($tParams, 'to', -1);
            $uid       = getArrayParameter($tParams, 'uid', 'search_'.uniqid());
            $engine    = getArrayParameter($tParams, 'engine', $config['wcm.search.engine']);
            if ($to != -1) $limit = ($to - $from + 1);

            if ($query == null)
            {
                $this->logger->logError('search fatal error: [query] parameter is mandatory');

                // Exit search!
                $repeat = false;
                return null;
            }

            // Check if there is another search with same assocname
            if (isset($this->loopInfo[$assocname]) && $this->loopInfo[$assocname]['iteration'] != -1)
            {
                $this->logger->logError('search fatal error: search conflit: a search or loop with same [name] parameter has already started');

                // Exit search!
                $repeat = false;
                return null;
            }

            // Search bizobjects and store results in current tag
            $this->logger->logVerbose("search name=$name query=$query order=$order offset=$from limit=$limit");
            $search = wcmBizsearch::getInstance($engine);

            // Total is equals to the min value between total and limit
            $total = min($search->initSearch($uid, $query, $order), $limit);
            $this->logger->logVerbose("search : total iterations will be $total");

            // Points to current tag values in the tag stack
            $i = count($this->_tag_stack) - 1;
            $this->_tag_stack[$i][1]['o'] = $search;
            $this->_tag_stack[$i][1]['uid'] = $uid;
            $this->_tag_stack[$i][1]['assocname'] = $assocname;

            // Get firstresult
            $rs = $search->getDocumentRange($from, $from, $uid, false);
            if (!$rs)
            {
                $this->logger->logError('search fatal error: retrieve search item #' . $from . ' failed');
                $repeat = false;
                return null;
            }

            // Create search info (even for empty search)
            $this->createloopInfo($assocname, $from, $total);

            // Empty search?
            if ($total == 0)
            {
                $repeat = false;
                return null;
            }

            // Expose bizobject assocArray
            $this->assign($assocname, $rs[0]->getAssocArray(false));
        }
        else
        {
            // Handle closing tag {/search} or exiting tag {exitsearch}

            // Retrieve enumerated bizobject
            $i = count($this->_tag_stack) - 1;
            $assocname   = $this->_tag_stack[$i][1]['assocname'];
            $uid = $this->_tag_stack[$i][1]['uid'];
            $search = $this->_tag_stack[$i][1]['o'];

            // Forced end of search {/search}?
            if (!$bExit)
            {
                // End of search loop?
                $bExit = $this->loopInfo[$assocname]['last'];
                if (!$bExit)
                {
                    // Retrieve next item
                    $this->updateloopInfo($assocname);
                    $offset = $this->loopInfo[$assocname]['current'];
                    $total = $this->loopInfo[$assocname]['total'];
                    $this->logger->logVerbose("search : retrieving item $offset of $total");
                    $rs = $search->getDocumentRange($offset, $offset, $uid, false);
                    // Error in search retrieval?
                    $bExit = (!$rs || count($rs) == 0);
                    if (!$bExit)
                    {
                        // Refresh assoc array and repeat loop
                        $this->assign($assocname, $rs[0]->getAssocArray(false));
                        $repeat = true;
                    }
                }
            }

            // Check if we have reach last item
            if ($bExit)
            {
                // Exitsearch (or end of search)
                $this->logger->logVerbose(($bExit) ? "exitsearch" : "endsearch");
                $this->clear_assign($assocname);
                $this->clearloopInfo($assocname);
                $repeat = false;

                // Clear memory
                //@todo: add method in Ibizsearch to unset a previous search
                unset($_SESSION['wcmBizsearch_'.$uid]);
                unset($search);
            }

            return $content;
        }
    }

    /**
     * Create a loop info structure
     *
     * @param string $loopname  Name of loop
     * @param int    $from      First index in loop
     * @param int    $total     Total number of rows to enumerate
     */
    private function createLoopInfo($loopname, $from, $total)
    {
        $this->loopInfo[$loopname] = array(
            'index' => 0,
            'iteration' => 1,
            'total' => $total,
            'first' => 1,
            'last' => ($total == 1) ? 1 : 0,
            'current' => $from );
    }

    /**
     * Update a loop info structure
     *
     * @param string $loopname  Name of loop
     */
    private function updateLoopInfo($loopname)
    {
        // Increate index, iteration and current
        $index = 1 + $this->loopInfo[$loopname]['index'];
        $iteration = 1 + $index;
        $current = 1 + $this->loopInfo[$loopname]['current'];

        $this->loopInfo[$loopname]['index'] = $index;
        $this->loopInfo[$loopname]['iteration'] = $iteration;
        $this->loopInfo[$loopname]['current'] = $current;
        $this->loopInfo[$loopname]['first'] = 0;
        $this->loopInfo[$loopname]['last'] = ($this->loopInfo[$loopname]['total'] == $iteration) ? 1 : 0;
    }

    /**
     * Clear a loop info structure
     *
     * @param string $loopname  Name of loop
     */
    private function clearLoopInfo($loopname)
    {
        // Mark loop as done (keep info for global $loop variable)
        $this->loopInfo[$loopname]['iteration'] = -1;
    }


    /**
     *  Handles {widget} tag
     */
    public function func_widget($params, &$smarty)
    {

        $required_params = array('type', 'guid', 'context');
        foreach($required_params as $key)
        {
            if(empty($params[$key])) {
                $smarty->trigger_error("widget: missing '".$key."' parameter");
                return;
            } else {
                $$key = $params[$key];
            }
        }

        $blockid = $type . '-' . $guid;
        $designZone = new wcmDesignZone(get_class($context), $context->id, 'null');
        $designZone->refresh();
        $content = $designZone->getZoneContent();

        if(isset($content[$guid]))
                $settings = $content[$guid]['settings'];
        else
                $settings = array();

        $widget = new $type($smarty->widgetMode | wcmWidget::IS_FIXED, $settings, $context, $guid, $smarty);

        $html = $widget->display();

        if($smarty->widgetMode & wcmWidget::VIEW_SETTINGS)
            $html .= '<script type="text/javascript">Event.observe(window, \'load\', function () { portal.widgets["'.$blockid.'"] = new Widget("'.$blockid.'", portal, "null"); });</script>';

        return $html;
    }

}
?>
