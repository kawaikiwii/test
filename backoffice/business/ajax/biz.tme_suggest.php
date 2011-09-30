<?php
/**
 * Project:     WCM
 * File:        biz.tme_suggest.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * This page is called by an Ajax call, It returns the suggested results from tme
 *
 */

    // Initialize system
    require_once dirname(__FILE__).'/../../initWebApp.php';

    $method = getArrayParameter($_REQUEST, "method", null);
    $type = getArrayParameter($_REQUEST, "type", null);
    $targetid = getArrayParameter($_REQUEST, "targetid", null);
    $score = getArrayParameter($_REQUEST, "score", null);

    $config = wcmConfig::getInstance();
    $tme = wcmSemanticServer::getInstance();
    $methods = array($method);

    $context = $_SESSION['wcm']['footprint']['context'];
    $sdata = $tme->mineObject($context, $methods);

    if($sdata == null)
    {
        echo '<ul><li>';
        echo _BIZ_TME_PROCESSING_FAILED;
        echo '</li></ul>';
        echo '<ul class="toolbar">';
        echo '<li><a href="#" onclick="closemodal(); return false;" class="ok">'._BIZ_OK.'</a></li>';
        echo '</ul>';
    }
    else
    {
        echo '<ul class="selection"><li><a href="#" onclick="javascript:_wcmselectall();return false;">'._SELECT_ALL.'</a></li></ul>';
        echo '<ul id="_tmeSuggestions">';
        
        $count = 0;
        foreach($sdata->$type as $key =>$value)
        {
            //print '<label><input type="checkbox" value="'.$key.'"> '.$key .'</label>';
            //$count++;
            // limit to the 10 first
            //if ($count == 10) break;
			if($type == "similars")
        		print '<li><label><input type="checkbox" value="'.$key.'"> '.$value['title'] .' ('.$key.')' .'</label></li>';
        	else
            	print '<li><label><input type="checkbox" value="'.$key.'"> '.$key .'</label></li>';
            
            $count++;
            
            // limit to the 10 first
            if ($count == 10) break;
        	
        
        }
        echo '</ul>';
    
        if($count == 0)
        {
            echo '<ul><li>';
            echo _BIZ_NO_SUGGESTION;
            echo '</li></ul>';
            echo '<ul class="toolbar">';
            echo '<li><a href="#" onclick="closemodal(); return false;" class="ok">'._BIZ_OK.'</a></li>';
            echo '</ul>';
        }
        else
        {
?>

<ul class="toolbar" style="margin-top:10px;">
    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
    <li><a href= "#" onclick="elem = $('_tmeSuggestions').down(); if(elem.down().down().checked) _wcmAddElementText($('<?php echo $targetid;?>'),elem.down().down().value, '<?php echo $targetid;?>', null); while(elem = elem.next()){if(elem.down().down().checked) _wcmAddElementText($('<?php echo $targetid;?>'),elem.down().down().value, '<?php echo $targetid;?>', null);}; closemodal(); return false;" class="replace"><?php echo _BIZ_ADD;?></a></li>
    <li><a href= "#" onclick="_wcmDeleteAllElement($('<?php echo $targetid;?>'), '<?php echo $targetid;?>'); elem = $('_tmeSuggestions').down(); if(elem.down().down().checked) _wcmAddElementText($('<?php echo $targetid;?>'),elem.down().down().value, '<?php echo $targetid;?>', null); while(elem = elem.next()){ if(elem.down().down().checked) _wcmAddElementText($('<?php echo $targetid;?>'),elem.down().down().value, '<?php echo $targetid;?>', null);}; closemodal(); return false;" class="save"><?php echo _BIZ_REPLACE;?></a></li>
</ul>
<?php
        }
    }