<?php
/**
 * Project:     WCM
 * File:        ajax/biz.permissions.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize the system
require_once dirname(__FILE__).'/../../initWebApp.php';
$config    = wcmConfig::getInstance();
$prefix    = getArrayParameter($_REQUEST, "prefix", null);

$divId		  = getArrayParameter($_REQUEST, "divId", null);
$command	  = getArrayParameter($_REQUEST, "command", null);
$universId	  = getArrayParameter($_REQUEST, "univers", null);
$service	  = getArrayParameter($_REQUEST, "service", null);
$accountId	  = getArrayParameter($_REQUEST, "accountId", null);
$permissionString = getArrayParameter($_REQUEST, "permissionString", null);
$userName         = getArrayParameter($_REQUEST, "userName", null);
$userId         = getArrayParameter($_REQUEST, "userId", null);
$overWrite        = getArrayParameter($_REQUEST, "overWrite", 0);

$account = new account();
$account->refreshByWcmUser(wcmSession::getInstance()->userId);
$adminUser = (!$account->id);

switch ($command)
{
	case "populeAlertServices":
		header("Content-Type: text/xml");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<ajax-response>\n";
		echo '<response type="item" id="' . $divId . '"><![CDATA[' ;
		if (!$universId)
		{
			echo '<select name="services" id="services" style="width:150px;" disabled=disabled>';
				echo '<option value="">&nbsp;</option>';
			echo '</select>';
		}
		else
		{
			if ($adminUser)
			{
				$univers = new site();
				$univers->refresh($universId);
				$services = explode('|', $univers->services);
			}
			else
			{
				$services = $account->getServices($universId);

			}
			echo '<select name="services" id="services" style="width:150px;" onChange="javascript:setRubriqueController();">';

			$optionsArray = array();
			foreach($services as $service)
			{
				if ($service != '')
					$optionsArray[] = '<option value="'.$service.'">'.getConst($service).'</option>';
			}

			$nbOptions = count($optionsArray);
			if ($nbOptions == 0)
				$optionsArray[] = '<option value="" selected="selected"></option>';
			elseif  ($nbOptions > 1)
				array_unshift($optionsArray,'<option value="*" selected="selected">*</option>');
			foreach ($optionsArray as $option)
			{
				echo $option;
			}
			echo '</select>';
		}
		echo ']]></response>';
		echo "</ajax-response>\n";
		break;
	
	case "populeServices":
		header("Content-Type: text/xml");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<ajax-response>\n";
		echo '<response type="item" id="' . $divId . '"><![CDATA[' ;
		if (!$universId)
		{
			echo '<select name="services" id="services" style="width:150px;" disabled=disabled>';
				echo '<option value="">&nbsp;</option>';
			echo '</select>';
		}
		else
		{
			if ($adminUser)
			{
				$univers = new site();
				$univers->refresh($universId);
				$services = explode('|', $univers->services);
			}
			else
			{
				$services = $account->getServices($universId);
				$nbServices = count($services);
				if ($nbServices == 0) {
					$univers = new site();
				$univers->refresh($universId);
				$services = explode('|', $univers->services);
				}
			}
			
			echo '<select name="services" id="services" style="width:150px;" onChange="checkBeforeChangingService();">';
			
			$optionsArray = array();
			$universlang = new site();
			$universlang->refresh($universId);
			require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');
        	
			foreach($services as $service)
			{
				if ($service != '')
				{
					$serv = getServiceTrad($universlang->language, $service);
					/*
					if ($service == "event") $serv = getConst(_BIZ_EVENT);
					//else if ($service == "news") $serv = getConst(_BIZ_NEWS);
					else if ($service == "news") $serv = "News";
					else if ($service == "video") $serv = getConst(_BIZ_VIDEO);
					else if ($service == "slideshow") $serv = getConst(_BIZ_SLIDESHOW);
					else if ($service == "forecast") $serv = getConst(_BIZ_FORECAST);
					else $serv = getConst($service);
					*/
								
					$optionsArray[] = '<option value="'.$service.'">'.$serv.'</option>';
				}
			}

			$nbOptions = count($optionsArray);
			if ($nbOptions == 0)
				$optionsArray[] = '<option value="" selected="selected"></option>';
			elseif  ($nbOptions > 1)
				array_unshift($optionsArray,'<option value="*" selected="selected">'._BIZ_ALL_SERVICES.'</option>');
			foreach ($optionsArray as $option)
			{
				echo $option;
			}
			echo '</select>';
			//echo '<span style="float:right"><input type="checkbox" id="allChannelIds" name="allChannelIds" value="*" onClick="if(this.checked) document.getElementById(\'selectRubrique\').style.display=\'none\'; else document.getElementById(\'selectRubrique\').style.display=\'inline\';" />';
			//echo '<span>'._BIZ_ALL_CHANNELS.'</span></span>';
            //echo '<a href="#" onclick="addAccountPermission();document.getElementById(\'list\').style.display=\'inline\';document.getElementById(\'selectZone\').style.display=\'none\'; return false;" class="list-builder"><span>'._BIZ_ADD.'</span></a><br /><br />';
        				
		}
		echo ']]></response>';
		echo "</ajax-response>\n";
		break;
		
	case "setRubriqueController":
		header("Content-Type: text/xml");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<ajax-response>\n";
		echo '<response type="item" id="' . $divId . '"><![CDATA[' ;

		if (($service)&&($service != '*'))
		{	
			if (isset($_SESSION["perm_user_id"]) && !empty($_SESSION["perm_user_id"]))
			{
				$acc = new account();
				$acc->refresh($_SESSION["perm_user_id"]);
				$userPerm = $acc->getArrayPermissions();
			}
			else
				$userPerm = "";		
				
			echo '<div id="selectRubrique" style="display:inline;">';          
			echo account::checkAllChannels($universId, $service, $userPerm); 
			echo "<br /><br />";
			$channelsHtml = account::getChannelsCheckBoxSelection($universId, $service, $userPerm, "checkCheckboxes");
			echo $channelsHtml;         
            echo '</div>';
             
			/*
			$url = $config['wcm.backOffice.url'] . 'business/ajax/biz.permissions.php';
			$acOptions = array('url' => $url,
					   'paramName' => 'prefix',
					   'parameters' => 'univers='.$universId.'&service='.$service,
					   'css_clear' => 'both');

			//wcmGUI::renderAutoCompletedField($url, 'rubrique', '*', null, array('style' => 'float: none; width:150px; margin-left: 5px' ), $acOptions, true);
			$channels = array('*' => '*' );
			channel::getChannelHierarchyBySiteId($universId,$channels);
			
			wcmGUI::renderDropdownField('rubrique', $channels, null, '', array('style' => 'float: none; width:200px; margin-left: 5px' ), true);
			*/
		}
		/*else
		{
			echo wcmGUI::renderSingleTag('input', array('id' => 'rubrique', 'disabled'=>'disabled', 'style'=>'float: none; width:150px; margin-left: 5px', 'type'=>'text'));
			
		}*/
		echo ']]></response>';
		echo "</ajax-response>\n";
		break;
		
	case "setAlertRubriqueController":
		header("Content-Type: text/xml");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<ajax-response>\n";
		echo '<response type="item" id="' . $divId . '"><![CDATA[' ;
		
		
		if (($service)&&($service != '*'))
		{
			$url = $config['wcm.backOffice.url'] . 'business/ajax/biz.permissions.php';
			$acOptions = array('url' => $url,
					   'paramName' => 'prefix',
					   'parameters' => 'univers='.$universId.'&service='.$service,
					   'css_clear' => 'both');
			wcmGUI::renderAutoCompletedField($url, 'rubrique', '*', null, array('style' => 'float: none; width:250px; margin-left: 5px' ), $acOptions, true);
		}
		else
		{
			echo wcmGUI::renderSingleTag('input', array('id' => 'rubrique', 'disabled'=>'disabled', 'style'=>'float: none; width:150px; margin-left: 5px', 'type'=>'text'));
			
		}	
		
		echo ']]></response>';
		echo "</ajax-response>\n";
		break;
		
		
	case "setPermissions":
		$currentAccount = new account();
		$currentAccount->refresh($accountId);
		$currentAccount->initPermissions();
		$permissions = explode('##',$permissionString);
		$nbPermissions = count($permissions);
		for($i=0;$i<$nbPermissions;$i++)
		{
			$temp = explode('||',$permissions[$i]);
			// remove intial permission before updating new ones
			//if ($i == 0 && !empty($temp[0]) && !empty($temp[1]) && $temp[1]!='*')
			//	$currentAccount->removePermissions($temp[0], $temp[1]);
			
			if (!empty($temp[0]))
				$currentAccount->addPermission($temp[0],$temp[1],$temp[2]);
		}
		$currentAccount->setPermissions();		
		break;
	
	case "setTreePermissions":
		ini_set('max_execution_time', 420);
		
		$currentAccount = new account();
		$currentAccount->refresh($accountId);
				
		$currentAccount->initPermissions();
		$permissions = explode(',',$permissionString);
		
		$nbPermissions = count($permissions);
		for($i=0;$i<$nbPermissions;$i++)
		{
			$temp = explode('_',$permissions[$i]);
			if (!empty($temp[0]))
			{	
				if (!isset($temp[1])) $temp[1] = "*";
				if (!isset($temp[2])) $temp[2] = "*";

				//wcmTrace("SET PERMISSIONS : ".$temp[0].$temp[1].$temp[2]);
				if ($currentAccount->checkPermBeforeInsert($temp[0],$temp[1],$temp[2]))
					$currentAccount->addPermission($temp[0],$temp[1],$temp[2]);
			}
		}
		$currentAccount->setPermissions($permissionString);		
		
		// update family permissions if necessary
		if ($currentAccount->updateFamilyPermissions())
			wcmTrace("Update childrens Permissions");
		else
			wcmTrace("Childrens Permissions - No Updates");	
							
		break;
	
	case "loadUserPermissions":
		// Refresh from name
			
		$bizobject = new account();
		if (!$bizobject->refreshByUserName($userName))
			echo "ERROR";
		else
		{
			// Init Puces From account $bizobject permissions
			$content = '';
			$account_permissions_values = '';
			foreach ($bizobject->permissions as $univers => $services)
			{
				foreach ($services as $service => $rubriques)
				{
					foreach ($rubriques as $rubrique)
					{
						$channelList = explode('/', $rubrique);
						$channel = new channel();
						$rubriqueLabel = '';
						foreach($channelList as $rub)
						{
							$channel->refresh($rub);
							$rubriqueLabel .= $channel->title.'/';
						}
						
						$id = $univers.'||'.$service.'||'.$rubrique;
						$site = new site();
						$site->refresh($univers);
						$value = $site->title.' \\ '.getConst($service).' \\ '.substr($rubriqueLabel,0,-1);
						//$content .= '<li style="clear:both" id="'.$id.'"><a href="#" onclick="delAccountPermission($(this).up()); return false;"><span>' . _DELETE . '</span></a> <em>' . $value . '</em></li>';
						$content .= '<li style="clear:both" id="'.$id.'"> <em>' . $value . '</em></li>';
						$account_permissions_values = ($account_permissions_values) ? $account_permissions_values.'##'.$id : $id;
					}
				}
			}

			// match
			header("Content-Type: text/xml");
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			echo "<ajax-response>\n";
			echo '<response type="item" id="permissions_list"><![CDATA[' ;
			echo $content;
			echo ']]></response>';
			echo "</ajax-response>\n";
		}
		break;

	case "loadUserIdPermissions":
		// Refresh from name
		require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');
        
		$bizobject = new account();
		if (!$bizobject->refresh($userId))
			echo "ERROR";
		else
		{
			// Init Puces From account $bizobject permissions
			$content = "";
			$account_permissions_values = '';
			foreach ($bizobject->permissions as $univers => $services)
			{
				$site = new site();
                $site->refresh($univers);                    
            	$content .= "<li style='clear:both'><b>"._BIZ_LABEL_UNIVERSE." : ".$site->title."</b></li>";
                
            	foreach ($services as $service => $rubriques)
				{
					if ($service == "*")
                	{
                		$content .= '<li style="margin-left:10px;clear:both"><em>'._BIZ_ALL_SERVICES.'</em></li>';
                	    $id = $univers.'||'.$service.'||*';                    
                        $account_permissions_values = ($account_permissions_values) ? $account_permissions_values.'##'.$id : $id;
                    	break;
                	}
                	
                	if (sizeof($rubriques)>0)
                	{
                		//$content .= '<li style="margin-left:10px;clear:both"><em><b>'.$service.' : </b></em></li><ul>';
                		$content .= '<li style="margin-left:10px;clear:both"><em><b>'.getServiceTrad($site->language, $service).' : </b></em></li><ul>';
                	}

                	$tempArray = array();
                	foreach ($rubriques as $rubrique) 
                    {    
                    	// case all topics cochée
                    	if ($rubrique == "*")
                        	$content .= '<li style="margin-left:30px;clear:both"><em><i>'._BIZ_ALL_CHANNELS.'</i></em></li>';
                        else
                        {
                        	// on range les résultats dans un tableau par pilier avec le parentId                	
	                    	$channel = new channel();
	                    	$channel->refresh($rubrique);
	                    	if (!empty($channel->parentId))
	                    		$tempArray[$channel->parentId][] = $channel->title;
                        }
                        
                        // on met à jour la variable cachée des permissions                		                    	
                        $id = $univers.'||'.$service.'||'.$rubrique;                    
                        $account_permissions_values = ($account_permissions_values) ? $account_permissions_values.'##'.$id : $id;
                    }
                	
                    // si des catégories sont présentes , on les affiche            		                    	                     
                    if (!empty($tempArray))
                    {
                    	ksort($tempArray);                  	
                    	$channelTemp = new channel();
                    	foreach ($tempArray as $key => $value)
                    	{
                    		// on regroupe par pilier            		                    	                     
                    		$channelTemp->refresh($key); 
	                        $content .= '<li style="margin-left:30px;clear:both"><em><i>'.$channelTemp->title.'</i> : </em>';   		                            	
                    		if (!empty($value))
                    		{
                    			foreach($value as $val)	
                    				$content .= '<em>'.$val.'</em>'; 
                    		}
                    			
                    		$content .= "</li>";
                    	}
                    }
                                      	
                    if (sizeof($rubriques)>0)
                		$content .= '</ul>';   
				}
			}

			// match
			header("Content-Type: text/xml");
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			echo "<ajax-response>\n";
			echo '<response type="item" id="permissions_list"><![CDATA[' ;
			echo $content;
			echo ']]></response>';
			echo "</ajax-response>\n";
		}
		break;
		
	case "saveBulkPermissions":
		$permissions = explode('##',$permissionString);
		$nbPermissions = count($permissions);
		$accountList = json_decode($accountId);
		$account = new account();
		foreach ($accountList as $anAccount)
		{
			$accountId = substr($anAccount, strrpos($anAccount,'_')+1);
			$account->refresh($accountId);
			if ($overWrite)			
				$account->initPermissions();
			for($i=0;$i<$nbPermissions;$i++)
			{
				if ($permissions[$i])
				{
					$temp = explode('||',$permissions[$i]);
					$account->addPermission($temp[0],$temp[1],$temp[2]);
				}
			}
			$account->setPermissions();		
		}
		break;
		
		
	default:
		// Rubrique Autocomplete
		$prefixes = explode('/', $prefix);
		$depth = count($prefixes);
		$where = '';
		$elements = array();
		if ($prefix == '/')
			$where = 'siteId='.$universId.' AND parentId is null';
		elseif ($depth > 1)	
		{
			$channelPrefix = $prefixes[$depth-2];
			$parentChannel = new channel();
			$parentChannel->refreshByTitle($channelPrefix);
			if ($parentChannel->id)
				$where = 'siteId='.$universId.' AND parentId='.$parentChannel->id.' AND title like \'%'.$prefixes[$depth-1].'%\'';
		}
		else
			$where = 'siteId='.$universId.' AND parentId is null AND title like \'%'.$prefixes[$depth-1].'%\'';

		$channels = bizobject::getBizobjects("channel", $where);

		if ($channels)
		{
			foreach ($channels as $channel)
			{
				if ((!$adminUser)&&(!$account->isAllowedTo($universId,$service,$channel->id)))
					continue;
				$path = $channel->getChannelPath('/', 'title');
				$elements[$path]= $channel->id;
			}
		}
		ksort($elements);
		echo '<ul>';
		foreach($elements as $title => $id)
		{
			echo '<li id="'.$id.'">';
			echo $title;
			echo '</li>';
		}
		echo '</ul>';
		break;
}
