<?php
/*
 * Project:     WCM
 * File:        home.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('home');

    $config = wcmConfig::getInstance();  
    
    // specific dashboard for BIPH univers anglais/francais --- redirection
    if ($_SESSION['siteId'] == '11' || $_SESSION['siteId'] == '12')
    	header('Location: ?_wcmAction=home-biph');
    else if ($_SESSION['siteId'] == '13' || $_SESSION['siteId'] == '14')	// bang universe
    	header('Location: ?_wcmAction=home-bang');
    else if ($_SESSION['siteId'] == '15')	// ELLE universe
    	header('Location: ?_wcmTodo=initSearch&search_query=&search_baseQuery=classname:placeElle&view=list&_wcmAction=business/search');
    // Include header
    include('includes/header.php');

     $wcmMenu = new wcmMenu();
    // Home menu
	$wcmMenu->refresh(1);
	
if ($session->isAllowed($wcmMenu, wcmPermission::P_READ))
{
?>
<a name="top"></a>

<table>
<tr>
<td><a href="./index.php?_wcmAction=business/news" target="editNewItem">Create News</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
<td><a href="./index.php?_wcmAction=business/event" target="editNewItem">Create Event</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
<td><a href="./index.php?_wcmAction=business/slideshow" target="editNewItem">Create Slideshow</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
<td><a href="./index.php?_wcmAction=business/video" target="editNewItem">Create Video</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
<td><a href="./index.php?_wcmAction=business/photo" target="editNewItem">Create Photo</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
<td><a href="./index.php?_wcmAction=home"><b>Refresh</b></a></td>
<td><div id="displayMsg" style="font-weight:bold;color:red"></div></td>
</tr>
</table>

<br><br>

<!--<a href="javascript:void(0)" onClick="$('frontContent').toggle();">Production Content</a>-->

<div class="newContainer">

<h1>Content on Fire</h1>
<div id="bbrowseContent">
   <div class="menuToogleDashboard">
      <a id="browse_news_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'browse', 'browse_news')">News</a>
      <a id="browse_event_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'browse', 'browse_event')">Event</a>
      <a id="browse_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'browse', 'browse_slideshow')">Slideshow</a>
      <a id="browse_video_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'browse', 'browse_video')">Video</a>
      <div class="clearIt"></div>
   </div>
   
   <div id="browseContent"></div>
</div>


<div style="height:10px; clear:both;">&nbsp;</div>

<br><br>
<h1>Published Content</h1>
<div id="backBrowseContent">
   <div class="menuToogleDashboard">
      <a id="published_news_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_news');">News</a>
      <?php if ($_SESSION['siteId'] == '4' || $_SESSION['siteId'] == '5') 
      	{ 
      ?> 
      <a id="published_event_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_event')">Event by publication date</a>
      <a id="published_eventStart_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_eventStart')">Event by start date</a>
      <?php 
		}
	  ?>
      <a id="published_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_slideshow')">Slideshow</a>
      <a id="published_video_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_video')">Video</a>
      <?php if ($_SESSION['siteId'] == '5') 
      	{ 
      ?> 
      	<a id="published_afpENnews_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_afpENnews')">News AFP EN</a>
      	<a id="published_filFRnews_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_filFRnews')">News Relaxfil FR</a>
      	<a id="published_afpENslideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_afpENslideshow')">Slideshow AFP EN</a>
      <?php 
		}
		else if ($_SESSION['siteId'] == '6') 
		{
	  ?>
		<a id="published_afpFRnews_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_afpFRnews')">News AFP FR</a>
      <?php	
		}
      ?>
      <div class="clearIt"></div>
   </div>

  <div id="publishedContent"></div>
</div>


<div style="height:10px; clear:both;">&nbsp;</div>
<br><br>
<h1>Imported News</h1>
<div id="importAfp"  <?php if ($_SESSION['siteId'] == '6') { ?>style="DISPLAY: none"<?php } ?>>
   <div class="menuToogleDashboard">
      	<a id="import_afp_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'import', 'import_afp');">AFP News</a>
    	<a id="import_afp_video_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'import', 'import_afp_video');">AFP Videos</a>
        
	<?php if ($_SESSION['siteId'] == '5') { ?>
		<a id="import_fil_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'import', 'import_fil');">Relaxfil</a>
	<?php }else { ?>
		<a id="import_fil_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'import', 'import_fil');" style="display:none;"></a>
	<?php } ?>
      <div class="clearIt"></div>
   </div>

  <div id="importContent"></div>
  <?php if ($_SESSION['siteId'] == '5') { ?>
		<div id="importContentFil"></div>
  <?php } ?>
</div>




<div style="height:10px; clear:both;">&nbsp;</div>

<br><br>
<h1>My History</h1>
<div id="myactions">
   <div class="menuToogleDashboard">
      <a id="actions_modified_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'actions', 'actions_modified');">Modified items</a>
      <a id="actions_created_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'actions', 'actions_created');">Created items</a>
      <div class="clearIt"></div>
   </div>

  <div id="actionsContent"></div>
</div>


</div>



<script type="text/javascript">
   refreshDashboard('browse');
   refreshDashboard('published');
   refreshDashboard('import');
   refreshDashboard('myactions');
   switchPannel('xxx', 'xxx', 'xxx');

   //setTimeout("document.location=document.location",180000);
</script>


<?php
}
    include('includes/footer.php');