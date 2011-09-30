<?php

/**
 * Project:     WCM
 * File:        biz.nList.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$locked      = getArrayParameter($_REQUEST, "locked", null);
$displayOnly = getArrayParameter($_REQUEST, "displayOnly", null);
$classId     = getArrayParameter($_REQUEST, "classId", null);
$className   = getArrayParameter($_REQUEST, "className", null);
$todo        = getArrayParameter($_REQUEST, "todo", null);
$mode        = getArrayParameter($_REQUEST, "mode", null);
$editedId    = getArrayParameter($_REQUEST, "editedId", null);
$text        = urldecode(getArrayParameter($_REQUEST, "text", null));
$score       = (float) getArrayParameter($_REQUEST, "score", null);

// Div names
$divName        = "div_".$className."_".$todo;
$msgDivName     = "div_msg_".$className."_".$todo;
$inputDivName   = "div_input_".$className."_".$todo;
$refreshDivName = "div_refresh_".$className."_".$todo;

$xsl        = WCM_DIR . '/xsl/tme/methods.xsl';
$xslDisplay = WCM_DIR . '/xsl/tme/display.xsl';
$xslSort    = WCM_DIR . '/xsl/tme/sort.xsl';

if (($mode == 'update' || $mode == 'insert') && !$text)
{
    $ajaxError[] = _BIZ_ERROR_TEXT_NOT_EMPTY;
}
if ($score > 100 || $score < 0)
{
    $ajaxError[] = _BIZ_ERROR_SCORE_NUM;
}
if (isset($ajaxError) && $ajaxError)
{   
    // No browser cache
    header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
    header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
    header( 'Cache-Control: no-store, no-cache, must-revalidate' );
    header( 'Cache-Control: post-check=0, pre-check=0', false );
    header( 'Pragma: no-cache' );
    
    // Xml output
    header( 'Content-Type: text/xml;charset=UTF-8' );
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    
    // Write ajax response
    echo "<ajax-response>\n";

    // Write ajax response
    echo "<response type='item' id='ajaxError'><![CDATA[";
        foreach ($ajaxError as $error)
        {
            echo '<p class="error">' . $error . '</p>';
        }
    echo "]]></response>";
    // TODO : duplication of reset code
    echo "<response type='item' id='".$refreshDivName."'><![CDATA[";
    if ($locked != 1)
    {
        echo '<a href="javascript:confirm_'.$todo.'()">';
        echo "<img src=\"img/refresh.gif\" alt=\""._BIZ_RESET."\"/></a>";
    
        if ($todo == 'NSummarizer')
        {
            echo '<a href="javascript:ajaxCall_'.$todo."('update', 1)\">";
            echo '<img src="img/icons/accept.png" alt="'._BIZ_UPDATE.'" title="'._BIZ_UPDATE.'"/></a>';
        }
    }
    echo "]]></response>";
    echo '</ajax-response>';
    return;
}



$bizobject = new $className($project);
$bizobject->refresh($classId);

if ($todo == "NFinder_GL" || $todo == "NFinder_ON" || $todo == "NFinder_PN")
{
    $kind = "NFinder";
}
else if ($todo == 'NCategorizer_Display')
{
    $kind = 'NCategorizer';
}
else if ($todo == 'NLikeThis_Compare_Display')
{
    $kind = 'NLikeThis_Compare';
}
else
{
    $kind = $todo;
}

$semanticData = null;
switch ($mode)
{
    /**
     * Use the wcmNServerBridge to retrieve & refresh the semantic data
     */
    case "reset" :
        // Fetch the semantic data from NServer
        $semanticData = fetchSemanticData($bizobject, array($kind));
        $xmlSemanticData = $semanticData['xmlSemanticData'];

        // Reset the session values
        setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        break;

    /**
     * Display the content of the div (list all the items)
     */
    case "display":
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);
        break;

    /**
     * Validate the item (Source will be switched to "User" and
     * RelevancyScore or Weight will be put at 100
     */
    case "validate" :
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);

        // Edit the node
        if ($todo != 'NSentiment')
            $score = '100';
        else
            $score = null;

        $xmlSemanticData = updateNode($xmlSemanticData, $todo, $editedId, null, $score);

        // Update the XMLSemantic in the session
        setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        break;

    /**
     * Update values of an item (text or score), it will also update the source to User
     */
    case "update" :
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);

        // Edit the node
        $xmlSemanticData = updateNode($xmlSemanticData, $todo, $editedId, $text, $score);

        // Update the XMLSemantic in the session
        setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        break;

    /**
     * Rertrieve data from the xml fragment and create the input fields, so they can be edited
     */
    case "edit" :
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);

        $msg = _BIZ_MSG_EDIT;
        $editedText = "";
        $editedScore = "";
        editNode($xmlSemanticData, $todo, $editedId, $editedText, $editedScore);
        break;

    /**
     * Delete one item / node from the xml fragment
     */
    case "delete" :
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);
        // Delete the node
        $xmlSemanticData = deleteNode($xmlSemanticData, $todo, $editedId);

        // Update the XMLSemantic in the session
        setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        break;

    /**
     * Insert a new item into the xml fragment
     */
    case "insert" :
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);

        // Insert a new node
        $xmlSemanticData = insertNode($xmlSemanticData, $todo, $text, $score);

        // Update the XMLSemantic in the session
        setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        break;

    /**
     * Reset field (display a msg?)
     */
    case "new":
        if ($todo == 'NSummarizer')
        {
            // Recover the XMLSemantic from the session
            $xmlSemanticData = getSessionSemanticData($bizobject, $kind);

            // Update the node
            $xmlSemanticData = updateNode($xmlSemanticData, $todo, 1, _BIZ_MSG_INSERT_TEXT, 100);

            // Update the XMLSemantic in the session
            setSessionSemanticData($bizobject, $kind, $xmlSemanticData);
        }
        break;

    /**
     * Undo editing
     */
    case "undo":
        // Recover the XMLSemantic from the session
        $xmlSemanticData = getSessionSemanticData($bizobject, $kind);
        break;
}

/**
 * Echo the layout used to display the semantic data for each method available
 *
 * @param string $xml         Xml fragment containing the semantic data that must be displayed
 * @param string $xsl         Xsl to be used with the xml
 * @param string $kind        Semantic data kind
 * @param string $method      Method to be used
 * @param bool   $locked      Whether the business object is locked
 * @param bool   $displayOnly Whether to omit editing buttons
 */
function displayList($xml, $xsl, $kind, $method, $locked, $displayOnly)
{
    if (!$xml)
        return;

    $domXsl = new DOMDocument;
    $domXsl->load($xsl);

    // Create the domXML
    $domXml = new DOMDocument();
    if (!$domXml->loadXML($xml))
    {
        throw new Exception(_BIZ_INVALID_XML);
    }
    
    // Process the XSL
    $proc = new XSLTProcessor;
    $proc->importStyleSheet($domXsl);

    $proc->registerPHPFunctions();

    $proc->setParameter("", "kind",        $kind);
    $proc->setParameter("", "method",      $method);
    $proc->setParameter("", "locked",      $locked);
    $proc->setParameter("", "displayOnly", $displayOnly);

    echo $proc->transformToXML($domXml);
}

/**
 * Parses part of a given XML and returns an associative array of
 * items such as: document, parentNode, childNodes, etc.
 *
 * @param string $xml  the XML to parse
 * @param string $todo what to extract from the XML
 */
function parseXml($xml, $todo)
{
    $document = new DOMDocument();
    $document->loadXML($xml);

    $parentNodeName = null;
    $childNodeName  = null;
    $kind           = null;
    $kindNodeName   = null;
    $scoreAttrName  = 'RelevancyScore';

    switch ($todo)
    {
    case "NCategorizer":
        $parentNodeName = "Categories";
        $childNodeName  = "Category";
        break;

    case "NConceptExtractor":
        $parentNodeName = "Concepts";
        $childNodeName  = "Concept";
        break;

    case "NFinder_ON":
        $parentNodeName = "EntitiesList";
        $kind           = "ON";
        $kindNodeName   = "Entities";
        $childNodeName  = "Entity";
        $scoreAttrName  = "Weight";
        break;

    case "NFinder_PN":
        $parentNodeName = "EntitiesList";
        $kind           = "PN";
        $kindNodeName   = "Entities";
        $childNodeName  = "Entity";
        $scoreAttrName  = "Weight";
        break;

    case "NFinder_GL":
        $parentNodeName = "EntitiesList";
        $kind           = "GL";
        $kindNodeName   = "Entities";
        $childNodeName  = "Entity";
        $scoreAttrName  = "Weight";
        break;

    case "NLikeThis_Compare":
        $parentNodeName = "SimilarTexts";
        $childNodeName  = "SimilarText";
        $scoreAttrName  = "Weight";
        break;

    case "NSummarizer":
        $parentNodeName = "Summary";
        $childNodeName  = "#text";
        break;

    case "NSentiment":
        $parentNodeName = "Sentiment";
        break;
    }

    $xpath = new DOMXPath($document);

    $xpathQuery = "//$parentNodeName";
    $parentNode = $xpath->query($xpathQuery)->item(0);
    $childNodes = null;

    if ($kind && $kindNodeName)
    {
        $xpathQuery .= "/$kindNodeName"."[@Kind='$kind']";
        $parentNode = $xpath->query($xpathQuery)->item(0);
    }

    if ($childNodeName)
    {
        if ($childNodeName == '#text')
            $xpathQuery .= "/text()";
        else
            $xpathQuery .= "/$childNodeName";

        $childNodes = $xpath->query($xpathQuery);
    }

    return array(
        'document'      => $document,
        'parentNode'    => $parentNode,
        'childNodeName' => $childNodeName,
        'childNodes'    => $childNodes,
        'kind'          => $kind,
        'scoreAttrName' => $scoreAttrName
        );
}

/**
 * Delete a specific node
 *
 * @param   $xml    Xml fragment containing the semantic data
 * @param   $todo   The method that was executed (NConceptExtractor, NCategorizer, NSummarizer, NFinder, etc.)
 * @param   $id     Id of the item to be deleted (its position)
 *
 * @return  $result     Returns the new xml semantic data
 */
function deleteNode($xml, $todo, $id)
{
    $info = parseXml($xml, $todo);

    $document   = $info['document'];
    $parentNode = $info['parentNode'];
    $childNodes = $info['childNodes'];

    $childNode = $childNodes->item($id - 1);
    $parentNode->removeChild($childNode);

    return $document->SaveXML();;
}

/**
 * Add an item into the semantic data
 *
 * @param   $xml    Xml fragment containing the semantic data
 * @param   $todo   The method that was executed (NConceptExtractor, NCategorizer, NSummarizer, NFinder, etc.)
 * @param   $text   Text to insert
 * @param   $score  Score to insert
 *
 * @return  $result     Returns the new xml semantic data
 */
function insertNode($xml, $todo, $text, $score)
{
    $info = parseXml($xml, $todo);

    $document      = $info['document'];
    $parentNode    = $info['parentNode'];
    $childNodeName = $info['childNodeName'];
    $scoreAttrName = $info['scoreAttrName'];
    $kind          = $info['kind'];

    if ($childNodeName == '#text')
    {
        $parentNode->setAttribute("Source", "User");
        $parentNode->setAttribute($scoreAttrName, $score);

        $newNode = $document->createTextNode($text);
    }
    else
    {
        $newNode = $document->createElement($childNodeName);
        $newNode->setAttribute("Source", "User");
        $newNode->setAttribute($scoreAttrName, $score);

        if ($kind)
            $newNode->setAttribute("Kind", $kind);

        $textNode = $document->createTextNode($text);
        $newNode->appendChild($textNode);
    }

    $parentNode->appendChild($newNode);

    return sortXML($document->SaveXML(), $todo);
}

/**
 * Update a specific node
 *
 * @param   $xml        Xml fragment containing the semantic data
 * @param   $todo       The method that was executed (NConceptExtractor, NCategorizer, NSummarizer, NFinder, etc.)
 * @param   $editedId   Id of the node to be updated (Its position)
 * @param   $text       Text to update
 * @param   $score      Score to update
 *
 * @return  $result     Returns the new xml semantic data
 */
function updateNode($xml, $todo, $editedId, $text, $score)
{
    $info = parseXml($xml, $todo);

    $document      = $info['document'];
    $parentNode    = $info['parentNode'];
    $childNodeName = $info['childNodeName'];
    $childNodes    = $info['childNodes'];
    $scoreAttrName = $info['scoreAttrName'];

    if ($childNodes->length > 0)
    {
        $editedNode = $childNodes->item($editedId - 1);

        if ($text)
            $editedNode->nodeValue = $text;

        if ($childNodeName == '#text')
        {
            if ($score)
                $parentNode->setAttribute($scoreAttrName, $score);

            $parentNode->setAttribute("Source", "User");
        }
        else
        {
            if ($score)
                $editedNode->setAttribute($scoreAttrName, $score);
    
            $editedNode->setAttribute("Source", "User");
        }
    }
    else
    {
        if ($editedId == 'tone')
        {
            $editedId = 0;
            $editedText = _BIZ_TONE;
            $scoreAttrName = 'Tone';
        }
        else
        {
            $editedId = 1;
            $editedText = _BIZ_SUBJECTIVITY;
            $scoreAttrName = 'Subjectivity';
        }

        if ($score)
            $parentNode->setAttribute($scoreAttrName, $score);

        $parentNode->setAttribute("Source", "User");
    }

    return sortXML($document->SaveXML(), $todo);
}

/**
 * Retrieve data from a specific node to edit it in input fields
 *
 * @param   $xml        Xml fragment containing the semantic data
 * @param   $todo       The method that was executed (NConceptExtractor, NCategorizer, NSummarizer, NFinder, etc.)
 * @param   $editedId   Id of the node to be edited (Its position)
 * @param   $text       Text to edit
 * @param   $score      Score to edit
 */
function editNode($xml, $todo, $editedId, &$editedText, &$editedScore)
{
    $info = parseXml($xml, $todo);

    $parentNode    = $info['parentNode'];
    $childNodeName = $info['childNodeName'];
    $childNodes    = $info['childNodes'];
    $scoreAttrName = $info['scoreAttrName'];

    if ($childNodes->length > 0)
    {
        $editedNode = $childNodes->item($editedId - 1);

        $editedText  = $editedNode->nodeValue;

        if ($childNodeName == '#text')
            $editedScore = $parentNode->getAttribute($scoreAttrName);
        else
            $editedScore = $editedNode->getAttribute($scoreAttrName);
    }
    else
    {
        if ($editedId == 'tone')
        {
            $editedId = 0;
            $editedText = _BIZ_TONE;
            $scoreAttrName = 'Tone';
        }
        else
        {
            $editedId = 1;
            $editedText = _BIZ_SUBJECTIVITY;
            $scoreAttrName = 'Subjectivity';
        }

        $editedScore = $parentNode->getAttribute($scoreAttrName);
    }
}

/**
 * Sort the function by its RelevancyScore or Weight
 *
 * @param   $xml    XML fragment of the semantic data
 * @param   $todo   The method that was executed (NConceptExtractor, NCategorizer, NSummarizer, NFinder, etc.)
 *
 * @return  $xml    XML fragment of the semantic data sorted
 */
function sortXML($xml, $todo)
{
    global $xslSort;

    $domXml = new DOMDocument();
    if (!$domXml->loadXML($xml))
    {
        throw new Exception(_BIZ_INVALID_XML);
    }
    
    $domXsl = new DOMDocument;
    $domXsl->load($xslSort);

    // Process the XSL
    $proc = new XSLTProcessor;
    $proc->importStyleSheet($domXsl);

    $result = $proc->transformToXML($domXml);

    return $result;
}

/**
 * Begin output of the Ajax Response
 */

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header( 'Content-Type: text/xml;charset=UTF-8' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";

    // Write ajax response
    echo "<response type='item' id='ajaxError'><![CDATA[";
    echo '<span class="error"></span>';
    echo "]]></response>";
    
    echo "<response type='item' id='".$refreshDivName."'><![CDATA[";
    if ($locked != 1)
    {
        echo '<a href="javascript:confirm_'.$todo.'()">';
        echo "<img src=\"img/refresh.gif\" alt=\""._BIZ_RESET."\"/></a>";
    
        if ($todo == 'NSummarizer')
        {
            echo '<a href="javascript:ajaxCall_'.$todo."('update', 1)\">";
            echo '<img src="img/icons/accept.png" alt="'._BIZ_UPDATE.'" title="'._BIZ_UPDATE.'"/></a>';
        }
    }
    echo "]]></response>";
    
    /**
     * No update of the listing div if mode == new
     */
    if (($mode != "new" || $todo == 'NSummarizer') && $mode != "edit")
    {
        echo "<response type='item' id='".$divName."'>";
        echo "<![CDATA[";
        displayList($xmlSemanticData, $xslDisplay, $kind, $todo, $locked, $displayOnly);
        echo "]]></response>\n";
    }
    
    /**
     * Display the edit input (inline) for the item
     */
    if ($mode == 'edit')
    {
        // Select the appropriate node name
        switch($todo)
        {
        case "NConceptExtractor":
            $divTag = "concept";
            break;
    
        case "NCategorizer":
            $divTag = "category";
            break;
    
        case "NFinder_ON":
            $divTag = "entity_ON";
            break;
    
        case "NFinder_PN":
            $divTag = "entity_PN";
            break;
    
        case "NFinder_GL":
            $divTag = "entity_GL";
            break;
    
        case "NLikeThis_Compare":
            $divTag = "similar_text";
            break;
        
        case "NSentiment":
            $divTag = "sentiment";
            break;
        }
    
        echo "<response type='item' id='div_".$divTag."_".$editedId."'><![CDATA[";
        echo '<div style="width:305px;height:45px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">';
        if ($locked != 1 && $displayOnly != 1)
        {
            if ($todo != 'NSentiment')
                echo ' <input type="text" style="width:220px;"';
            else
                echo ' <div style="width:220px;"';
            
            if ($editedText == '' && $mode =='new')
            {
                $editedText = _BIZ_MSG_INSERT_TEXT;
                echo 'onClick="javascript:this.value=\'\'"';
            }
            
            if ($todo != 'NSentiment')
                echo ' name="nText_"'.$todo.' id="nText_'.$todo.'_'.$editedId.'" value="'.textH8($editedText).'"/>';
            else
                echo ' name="nText_"'.$todo.' id="nText_'.$todo.'_'.$editedId.'">'.textH8($editedText).'</div>';
        }
    
        if ($todo != 'NSentiment')
        {
            $nbStar = round($editedScore / 20);
            $cpt = 1;
            echo '<div style="float:right">';
            while($cpt <= $nbStar)
            {
                echo '<img border="0" style="float:midle;width:12px;" id="star_'.$todo.'_'.$editedId.'_'.$cpt.'" onClick="javascript:change_star_'.$todo.'('.$editedId.', '.$cpt.')" src="img/icons/full_star.gif" />';
                ++$cpt;
            }
        
            while($cpt <= 5)
            {
                echo '<img border="0" style="float:midle;width:12px;" id="star_'.$todo.'_'.$editedId.'_'.$cpt.'" onClick="javascript:change_star_'.$todo.'('.$editedId.', '.$cpt.')"  src="img/icons/empty_star.gif" />';
                ++$cpt;
            }
    
            echo '<input type="hidden" id="editedScore_'.$todo.'_'.$editedId.'" value=""/>';
        }
        else
        {
            echo '<input type="text" style="float:right;width:220px;" id="editedScore_'.$todo.'_'.$editedId.'" value="'.$editedScore.'"/>';
        }
        
        if($editedId != '')
            echo '<input type="hidden" id="nEditedId_'.$todo.'_'.$editedId.'" value="'.textH8($editedId).'"/>';
        
        echo '</div>';
        echo '</div>';
        echo '<div style="width:44px;height:45px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">';
    
        // Display appropriate buttons
        if ($todo != 'NSentiment')
            echo '<a href="javascript:ajaxCall_'.$todo."('update', ".$editedId.")\"><img src=".'"img/icons/accept.png"s alt="'._BIZ_UPDATE.'"/></a>';
        else
            echo '<a href="javascript:ajaxCall_'.$todo."('update', '".$editedId."')\"><img src=".'"img/icons/accept.png"s alt="'._BIZ_UPDATE.'"/></a>';
        
        echo '&nbsp;<a href="javascript:ajaxCall_'.$todo."('undo', null)\"><img src=".'"img/actions/undocheckout.gif" alt="'._BIZ_CANCEL.'"/></a>';
    
        echo '</div>';
        echo "]]></response>\n";
    }
    
    /**
     * Display the input fields to add / insert a new entry
     */
    if ($locked != 1 && $displayOnly != 1 &&
        ($mode == "new" || $mode == "display" || $mode == 'reset'))
    {
        echo "<response type='item' id='".$inputDivName."'><![CDATA[";
        echo '<div>';
    
        if ($todo != "NSummarizer" && $todo != "NSentiment")
        {
            echo '<div style="width:300px;height:25px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:5px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-left:5px;margin-bottom:3px;">';
            echo '<input type="text" name="nText_'.$todo.'" id="nText_'.$todo.'" value="'._BIZ_MSG_INSERT_TEXT.'" style="width:230px;" onClick="javascript:this.value=\'\'" />';
            echo '<input type="text" name="nScore_'.$todo.'" id="nScore_'.$todo.'" value="" style="width:30px;" maxlength="3" align="right" />';
            echo '</div>';
        }
    
        echo '<div style="width:42px;height:25px;margin-left:310px;padding-top:5px;padding-bottom:2px;margin-top:5px;">';
    
        if ($todo != 'NSummarizer' && $todo != 'NSentiment')
        {
            echo '<a href="javascript:ajaxCall_'.$todo."('insert', null)\"><img src=".'"img/icons/accept.png" alt="'._BIZ_ADD.'" title="'._BIZ_ADD.'"/></a>';
            echo '<a href="javascript:ajaxCall_'.$todo."('new', null)\"><img src=".'"img/icons/pencil.png" alt="'._BIZ_NEW.'" title="'._BIZ_NEW.'"/></a>';
        }
        echo '</div>';
    
        echo '</div>';
        echo "]]></response>\n";
    }
    
    /* USED FOR DEBUGING ONLY 
    echo "<response type='item' id='Debug'>";
    echo "<![CDATA[";
    echo mb_detect_encoding($xmlSemanticData);
    print_r($xmlSemanticData);
    echo "]]>";
    echo "</response>\n";
    */
    
    echo "<response type='item' id='NServerCommand'>";
    echo "<![CDATA[";
    print_r($semanticData['xmlCommand']);
    echo "]]>";
    echo "</response>\n";
    
    echo "<response type='item' id='NServerResult'>";
    echo "<![CDATA[";
    print_r($semanticData['xmlResult']);
    echo "]]>";
    echo "</response>\n";

echo "</ajax-response>\n";

?>
