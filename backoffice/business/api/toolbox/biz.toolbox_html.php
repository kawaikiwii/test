<?php

/**
 * Project:     WCM
 * File:        biz.toolbox_html.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Fill in an html select with options containing all channels
 * @param project       the project
 * @param selectedId    contains the id of the selected item, if there is any
 * @param where         contains the clause where for the query to the database
 * @param prefix        contains the string used to prefix the children name in the select box
 * @param excludeId     contains the id of an item we dont want to display
 */
function renderHtmlChannelOptions($project, $selectedId = null, $where = null, $prefix = null, $excludeId = null)
{
    if ($where == null)
        $where = 'parentId IS NULL OR parentId = 0';

    // Recover the channels in an array of object
    if (is_null($channels = bizobject::getBizobjects('channel', $where, "rank")))
        return;

    // Loop through the array of channels and display
    foreach ($channels as $channel)
    {
        if ($channel->id != $excludeId)
        {
            echo "<option value='". textH8($channel->id)."'";
            if($selectedId == $channel->id){ echo ' selected="selected" ';}
            echo ">".$prefix.textH8($channel->title)." (id:".textH8($channel->id).")".'</option>';

            // Display subchannels with new prefix
            renderHtmlChannelOptions($project, $selectedId, "parentId = ".$channel->id, ($prefix . _BIZ_CHANNEL_INDENT_SYMBOL), $excludeId);
        }
    }
}

/**
 * Fill in an html select with options containing all sites
 * @param project       the project
 * @param selected      contains the id of the selected item, if there is any
 * @param where         contains the clause where for the query to the database
 * @param orderBy       contains the orderBy for the quere to the database
 * @param indent        contains the string used to indent the children channels in the select box
 * @param exclude       contains the id of an item we dont want to display
 */
function renderHtmlSiteOptions($project, $selected = null, $where = null, $orderBy = 'title', $indent = null, $exclude = null)
{
    // Recover the sites in an array of object
    if(is_null($sites = bizobject::getBizobjects('site', $where, $orderBy))) return;

    // Loop through the array of sites and display
    foreach($sites as $site)
    {
        if (wcmSession::getInstance()->isAllowed($site, wcmPermission::P_READ))
        {
            $siteIds[] = $site->id;
            $option = "<option value='".textH8($site->id)."'";
            if($selected == $site->id) $option .= ' selected="selected" ';
            $option .= ">";
            $option .= textH8($site->title)." (id:".textH8($site->id).")".'</option>';
            $options[] = $option;
        }
    }
    echo '<option value="'.join(',',$siteIds).'">('._BIZ_ALL.')</option>';
    echo join('',$options);
}

/**
 * Fill in an html select with options containing all sites
 * @param project       the project
 * @param selected      contains the id of the selected item, if there is any
 * @param where         contains the clause where for the query to the database
 * @param orderBy       contains the orderBy for the quere to the database
 * @param exclude       contains the id of an item we dont want to display
 */
function renderHtmlBizclassOptions($project, $selected = null, $where = null, $orderBy = 'title', $exclude = null)
{
    $project = wcmProject::getInstance();
    $bizlogic = new wcmBizlogic($project);
    // Recover the sites in an array of object
    if(is_null($bizClasses = $bizlogic->getBizclasses())) return;

    // Loop through the array of sites and display
    foreach($bizClasses as $bizClass)
    {
        //On enlève le webuser car il existe un onglet.
        if (($bizClass->className != 'webuser') && ($bizClass->className != 'newsletter') && ($bizClass->className != 'channel'))
        {
            if (wcmSession::getInstance()->isAllowed($bizClass, wcmPermission::P_READ))
            {
                $bizClassNames[] = textH8(getConst($bizClass->className));
                $option = "<option value='".textH8(getConst($bizClass->className))."'";
                if($selected == $bizClass->className) $option .= ' selected="selected" ';
                $option .= ">";
                $option .= textH8(getConst($bizClass->title))." (id:".textH8($bizClass->id).")".'</option>';
                $options[] = $option;
            }
        }
    }
    echo '<option value="'.join(',',$bizClassNames).'">('._BIZ_ALL.')</option>';
    echo join('',$options);
}

/**
 * Renders HTML createdBy.
 *
 * @param object $bizobject bizobject
 *
 * @return string $html contains result
 */
function renderHtmlCreatedBy($bizobject)
{
    $html = '';
    $user  = $bizobject->getProject()->membership->getUserById($bizobject->createdBy);
    $html .= _BIZ_CREATEDAT  . ' ' . $bizobject->createdAt . ' ';
    if ($user)
    {
        $html .= mb_strtolower(_BIZ_BY);
        $html .= $user->id < 1 ? ' ' . _BIZ_IMPORTATION . '.' :  ' <a href="mailto:' . $user->email . '">' . getConst($user->name) . '</a>.';
    }
    return $html;
}

/**
 * Renders HTML modifiedBy.
 *
 * @param object $bizobject bizobject
 *
 * @return string $html contains result
 */
function renderHtmlModifiedBy($bizobject)
{
    $html = '';
    $user  = $bizobject->getProject()->membership->getUserById($bizobject->modifiedBy);
    if ($user)
    {
        $html .= _BIZ_MODIFIEDAT . ' ' . $bizobject->modifiedAt . ' ';
        $html .= mb_strtolower(_BIZ_BY) . ' <a href="mailto:' . $user->email . '">' . getConst($user->name) . '</a>.';
    }
    return $html;
}

/**
 * Render HTML <optgroups>
 *
 * @param $xmlNodes     nodes from xml document
 * @param exclude       contains $_SESSION['tags'] so we do not print already selected tags
 *
 */
function renderHtmlOptgroups($xmlNodes, $exclude)
{
    echo '<optgroup label="' . textH8($xmlNodes->getAttribute('name')) . '">';
    $tagsArray = array();
    foreach($xmlNodes->getElementsByTagName('tag') as $tag)
    {
        $tagArray = array();
        $tagArray[$xmlNodes->getAttribute('name')] = $tag->nodeValue;

        if (!in_array($tagArray, $exclude))
            $tagsArray[$xmlNodes->getAttribute('name') . '|' . $tag->nodeValue] = ($tag->nodeValue);

    }
    echo renderHtmlOptions($tagsArray, null, false);
    echo '</optgroup>\n\n';
}

/**
 * Renders a business object as HTML by executing a given template.
 *
 * @param bizobject  $bizobject    the business object to render
 * @param string     $templateCode the code of the template to execute
 * @param array      $parameters   the parameters for the template execution
 *
 * @return string the rendered business object as HTML
 */
function renderBizobject($bizobject, $templateCode = null, $parameters = null)
{
    $project   = wcmProject::getInstance();
    $generator = $project->generator;

    $templateId = "renderBizobject.tpl";
    if (!is_array($parameters))
    {
        $parameters = array();
    }

    $templateParameters = array();
    $templateParameters['obizobject'] = $bizobject;
    $templateParameters['parameters'] = $parameters;

    $templateGenerator = new wcmTemplateGenerator();
    return $templateGenerator->executeTemplate($templateId, $templateParameters);
}

/**
 * Renders a business object as HTML by executing a given template.
 *
 * @param string $bizclass     the class name of the business object to render
 * @param int    $bizid        the ID of the business object to render
 * @param string $templateCode the code of the template to execute
 * @param array  $parameters   the parameters for the template execution
 *
 * @return string the rendered business object as HTML
 */
function renderBizobjectById($bizclass, $bizid, $templateCode = null, $parameters = null)
{
    if($bizclass != '')
    {
        $project =  wcmProject::getInstance();
        $bizobject = new $bizclass($project, $bizid);
        
        if (is_string($parameters))
            $parameters = wcmXML::xmlToArray($parameters);
    
        return renderBizobject($bizobject, $templateCode, $parameters);
    }
}

/**
 * Renders a business object as HTML by transforming its XML
 * representation using a given XSL. If the latter is not given, uses
 * the XSL corresponding to the object's class name.
 *
 * @param bizobject  $bizobject  the business object to render
 * @param string     $xsl        the XSL to use, or null to use the default
 * @param array      $parameters the parameters for the transformation
 *
 * @return string the business object rendered as HTML
 */
function renderBizobjectFromXSL($bizobject, $xsl = null, $parameters = null)
{
    $bizclass = $bizobject->getClass();
    $bizid    = $bizobject->id;

    // Select XSL to use
    if (!$xsl)
    {
        $xslPrefix = WCM_DIR . '/business/xsl/';
        $xslSuffix = '.xsl';

        if(!isset($parameters['view']))
        {
            $xsl = $xslPrefix.$bizclass.$xslSuffix;
            $parameters['view'] = $xsl;
        }
        else 
        {
            //if an xsl template definition is sent, set the xsl file accordingly - new search UI

            // Load the search_configuration into the session if it's not aleady done
            if (!isset($_SESSION['search_configuration']))
                loadSearchConfiguration();
            
            // Get the xsl from the xml config file using the $parameters['view'] and the bizobject class $bizclass
            $domDoc = new DOMDocument();
            $xsl_not_found = FALSE;   
            if (!$domDoc->load($_SESSION['search_configuration']))
                throw new Exception(_BIZ_INVALID_XML);

            // The corresponding XPath object
            // Get the XSL representation of the current bizobject.
            $domXPath = new DOMXPath($domDoc);
            $nodeList = $domXPath->query("//searches/search[@id='1']/resultPage/resultSet/view[@name='".$parameters["view"]."']/bizObjects/bizObject[@name='".$bizclass."']");

            if($nodeList->length > 0)
            {
                $nd = $nodeList->item(0);
                $xsl = $xslPrefix.trim($nd->nodeValue);
            }
            else
            {
                $xsl_not_found = TRUE;
            }

            // If we don't find the XSL file related to the bizobject we get the default's bizobject XSL
            if ((!file_exists($xsl)) || ($xsl_not_found))
            {

                $nodeList = $domXPath->query("//searches/search[@id='1']/resultPage/resultSet/view[@name='".$parameters["view"]."']/bizObjects/bizObject[@name='default']");

                if($nodeList->length > 0)
                {
                    $nd = $nodeList->item(0);
                    $xsl = $xslPrefix.trim($nd->nodeValue);
                }
                else
                {
                    echo "Error in search configuration file XPATH query: //searches/search[@id='1']/resultSet/views/view[@name='".$parameters["view"]."']/bizObjects/bizObject[@name='default']";
                }
            }
            // If the default XSL doesn't exist, we search for the XSL named bizobject.xsl 
            if (!file_exists($xsl))
            {
                $xsl = $xslPrefix.'bizobject'.$xslSuffix;
            }
       }
    }

    // Create XSL document
    $xslDoc = new DOMDocument();
    $xslDoc->load($xsl);

    // Create XSLT processor
    $xsltProc = new XSLTProcessor;
    $xsltProc->importStyleSheet($xslDoc);

    $xsltProc->registerPHPFunctions();

    $xsltProc->setParameter("", 'bizclass', $bizclass."_".$parameters["view"]);
    $xsltProc->setParameter("", 'bizid', $bizid);
    if (method_exists($bizobject, 'isLocked') && $bizobject->isLocked())
        $locked = 'TRUE';
    elseif (method_exists($bizobject, 'getLockInfo'))
    {
        $lock = $bizobject->getLockInfo();
        if ($lock->userId == wcmSession::getInstance()->userId)
            $locked = 'ME';
        else
            $locked = 'FALSE';
    }
    else
    {
        $locked = 'FALSE';
    }
    $xsltProc->setParameter("",'locked', $locked);

    if ($parameters)
    {
        foreach ($parameters as $name => $value)
        {
            $xsltProc->setParameter("", $name, $value);
        }
    }

    // Create XML document
    $xml = $bizobject->toXML();

    // Load XML into DOM document
    $xmlDoc = new DOMDocument();
    if (!$xmlDoc->loadXML($xml))
    {
        throw new Exception(_BIZ_INVALID_XML);
    }

    // Transform XML document into HTML
    $html = $xsltProc->transformToXML($xmlDoc);

    return $html;
}

/**
 * Renders DAM object as HTML using a XSL if the given xsl does not exist,
 * a default xsl is used
 *
 * @param string  $xml      xml to parse
 * @param string  $xslname  xsl to use, or null to use the default
 * @return string   the DAM object rendered as HTML
 *
 */
function renderDAMObject($xml, $xslname = null)
{
    // Create XSL document
    $xslDoc = new DOMDocument();

    if (!$xslname)
        $xslname = "renderDamObject";

    $xsl = WCM_DIR . '/business/xsl/DAM_'.$xslname.'.xsl';
    if (!file_exists($xsl))
        $xsl = WCM_DIR . '/business/xsl/DAM_renderDamObject.xsl';
    $xslDoc->load($xsl);

    // Create XSLT processor
    $xsltProc = new XSLTProcessor;
    $xsltProc->importStyleSheet($xslDoc);

    $xsltProc->registerPHPFunctions();

    // Load XML into DOM document
    $xmlDoc = new DOMDocument();
    if (!$xmlDoc->loadXML($xml))
    {
        throw new Exception(_BIZ_INVALID_XML);
    }

    // Transform XML document into HTML
    $html = $xsltProc->transformToXML($xmlDoc);

    return $html;
}

/**
 * 
 * This function will help to find the name of a user from his/her id
 * It is used in xsl rendering of bizobjects
 * @param int $id userId
 * @return string userName
 */
function renderUserName($id)
{
    $user = new wcmUser(wcmProject::getInstance(), $id);
    return $user->name;
}

/**
 * gets workflow transitions ready to insert into a select
 *
 * @param array of transitions $transitions
 */
function renderWorkflowTransitions($transitions)
{
    $trans = array();
    foreach ($transitions as $transition)
    {
        $trans[$transition->id] = $transition->name;
    }
    renderHtmlOptions($trans);
}