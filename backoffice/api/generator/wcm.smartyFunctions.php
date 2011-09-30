<?php
/**
 * Project:     WCM
 * File:        wcm.smartyFunctions.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This class holds a set of Smarty functions
 * They can be called through a generic wcm
 * function. e.g. {wcm name='funcName' param1='' param2=''...}
 * where 'funcName' is a valid method name of this class
 *
 * Note: this class is extended by smartyFunctions in
 * the business/api/smarty directory to allow overwrite and extension
 * for profesionnal services, partners and customers
 */
abstract class wcmSmartyFunctions
{
    /**
     * Returns a photo assocArray corresponding to the first bizobject's photo
     * or null if the bizobject has no photo.
     *
     * @param array $params Assoc array of parameters
     * @param Smarty $smarty Instance of smarty
     *
     * @return string An assoc array representing first photo or null
     */
    public function firstPhoto($params, $smarty)
    {
        $bizobject = getArrayParameter($params, 'bizobject', null);
        $assign = getArrayParameter($params, 'assign', null);

        if (!$bizobject && !is_array($bizobject))
        {
            $smarty->trigger_error('wcm: firstPhoto => invalid or missing "bizobject" parameter');
            return;
        }

        if (!$assign)
        {
            $smarty->trigger_error('wcm: firstPhoto => missing "assign" parameter');
            return;
        }

        $sql = 'SELECT destinationId FROM #___relation WHERE sourceClass=? AND sourceID=? AND destinationClass=? AND kind=?';
        $db = wcmProject::getInstance()->datalayer->getConnectorByReference('biz')->getBusinessDatabase();
        $id = $db->executeScalar($sql, array($bizobject['className'], $bizobject['id'], 'photo', bizrelation::IS_COMPOSED_OF));

        if (!$id) return null;

        $photo = new photo(null, $id);
        $result = $photo->getAssocArray(false);
        unset($photo);

        $smarty->assign($assign, $result);
    }

    /**
     * Extract facet values from a search (or from entire corpus)
     * The returned is an array of 'facets' having the following properties
     * - name: facet index name
     * - value: facet indexed value
     * - text: facet literal representation
     * - count: number of matching objects
     *
     * Usage: {wcm  name="facets"
     *              facet="index-name" // name of index
     *              assign="var-name"  // name of variable that will be assigned
     *              sid="search-id"    // optional search id (default:null for the entire corpus)
     *              max="10"           // optional number of values to return (default:10)
     *              sort="0|1"}        // 1 to sort by occurence, 0 to sort by value (default:1)
     *
     * @param array $params Assoc array of parameters
     * @param Smarty $smarty Instance of smarty
     */
    public function facets($params, $smarty)
    {
        // retrieve parameters
        $searchId = getArrayParameter($params, 'sid', null);
        $facet = getArrayParameter($params, 'facet', null);
        $assign = getArrayParameter($params, 'assign', null);
        $max = getArrayParameter($params, 'max', 10);
        $sort = getArrayParameter($params, 'sort', 1);
        
        if (!$facet)
        {
            $smarty->trigger_error('wcm: facets => missing "facet" parameter');
            return;
        }

        if (!$assign)
        {
            $smarty->trigger_error('wcm: facets => missing "assign" parameter');
            return;
        }

        // extract facet values
        $result = array();
        $search = wcmBizsearch::getInstance();
        $values = $search->getFacetValues(array($facet => $max), $searchId, 0, $sort);
        if ($values)
        {
            foreach ($values as $value => $facet)
            {
                // display on facets with positive count
                if ($facet->count > 0)
                {
                    $result[] = array(
                                    'name' => $facet->name,
                                    'value' => $facet->value,
                                    'text' => $facet->text,
                                    'count' => $facet->count);
                }
            }
        }

        // Assign result
        $smarty->assign($assign, $result);
    }

    /**
     * Returns an array of bizrelation associated to a bizobject
     *
     * @param array $params Assoc array of parameters
     * @param Smarty $smarty Instance of smarty
     */
    public function relations($params, $smarty)
    {
        $bizobject = getArrayParameter($params, 'bizobject', null);
        $assign = getArrayParameter($params, 'assign', null);
        $kind = intval(getArrayParameter($params, 'kind', bizrelation::IS_RELATED_TO));

        if (!$bizobject && !is_array($bizobject))
        {
            $smarty->trigger_error('wcm: relations => invalid or missing "bizobject" parameter');
            return;
        }

        if (!$assign)
        {
            $smarty->trigger_error('wcm: relations => missing "assign" parameter');
            return;
        }

        if (!$kind)
        {
            $kind = bizrelation::IS_RELATED_TO;
            return;
        }

        $relations = array();
        $bizrelation = new bizrelation();
        $where = "sourceClass='" . $bizobject['className'] . "' AND sourceId=" . $bizobject['id'] . " AND kind=" . $kind;

        if ($bizrelation->beginEnum($where, "rank"))
        {
            while ($bizrelation->nextEnum())
            {
                $relations[] = $bizrelation->getAssocArray(false);
            }
            $bizrelation->endEnum();
        }

        // Assign result
        $smarty->assign($assign, $relations);
    }

    /**
     * Return the URL of a bizPhoto
     *
     * @param array $params Assoc array of parameters
     * @param Smarty $smarty Instance of smarty
     */
    public function photoUrl($params, $smarty)
    {

        $bizobject = getArrayParameter($params, 'bizobject', null);
        $format = getArrayParameter($params, 'format', 'original');
        $assign = getArrayParameter($params, 'assign', null);
        $forceGen = getArrayParameter($params, 'force', false);

        if (!$bizobject && !is_array($bizobject))
        {
            $smarty->trigger_error('wcm: photoUrl => invalid or missing "bizobject" parameter');
            return;
        }

        $config = wcmConfig::getInstance();
        $cmanager = new croppingManager($bizobject['this'], WCM_DIR . '/business/xml/photosratios/default.xml');

        if(!$path = $cmanager->getPhotoUrl($format, $forceGen))
            if($format == 'thumbnail')
                $path = $bizobject['thumbnail'];
            else
                $path = $bizobject['original'];

        if($assign)
            $smarty->assign($assign, $config['wcm.backOffice.url'] . $path);
        else
            return $config['wcm.backOffice.url'] . $path;
    }

    /**
     * Renders a given object with wcmGUI::renderObject().
     *
     * Specific parameters:
     *
     *     $params['object']          => (wcmSysobject) The object to render
     *     $params['template']        => (string) The Smarty template to use
     *     $params['default_template' => (string) The default template (if $template does not exist)
     *
     * Any other parameters are passed as-is to wcmGUI::renderObject().
     *
     * @param array  $params Assoc array of parameters
     * @param Smarty $smarty The Smarty instance
     *
     * @return string The rendered object
     */
    function render_object($params, $smarty)
    {
        $config = wcmConfig::getInstance();
        // Validate specific parameters
        $msg = 'wcm: render_object => missing or invalid "%s" parameter';

        if (!isset($params['object']) || !is_object($object = $params['object']))
        {
            $smarty->trigger_error(sprintf($msg, 'object'));
            return null;
        }
        unset($params['object']);

        if (!isset($params['template']) || !($template = $params['template']))
        {
            $smarty->trigger_error(sprintf($msg, 'template'));
            return null;
        }
        unset($params['template']);

        if (!isset($params['default_template']) || !($default_template = $params['default_template']))
        {
            $smarty->trigger_error(sprintf($msg, 'default_template'));
            return null;
        }
        unset($params['default_template']);

        // Use default template if $template does not exist
        if (!file_exists($config['wcm.templates.path'].$template))
        {
            $template = $default_template;
        }

        // Render the object
        return wcmGUI::renderObject($object, $template, $params);
    }

    /**
     * Includes a template with a fallback option
     *
     * 
     * Specific parameters:
     *     $params['file']     => (string) Template to include
     *     $params['fallback'] => (string) Fallback template is file does not exists
     *
     * @param array  $params Assoc array of parameters
     * @param Smarty $smarty The Smarty instance
     *
     * @return string The rendered object
     */
    function include_template($params, $smarty)
    {
        // Is this a relative or absolute path?
        if ($params['file']{0} == '/')
        {
            $tpl = $params['file'];
        } else {
            $tpl = $smarty->template_dir.'/'.$params['file'];
        }

        if (file_exists($tpl))
        {
            return $smarty->fetch($tpl);
        } else {

            if ($params['fallback']{0} == '/')
            {
                $tpl = $params['fallback'];
            } else {
                $tpl = $smarty->template_dir.'/'.$params['fallback'];
            }
            return $smarty->fetch($tpl);
        }
    }
}