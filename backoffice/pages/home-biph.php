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
<td><a href="./index.php?_wcmAction=business/slideshow" target="editNewItem">Create Slideshow</a>&nbsp;&nbsp;-&nbsp;&nbsp;</td>
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
      <a id="browse_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'browse', 'browse_slideshow')">Slideshow</a>
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
      <a id="published_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'published', 'published_slideshow')">Slideshow</a>
      <div class="clearIt"></div>
   </div>

  <div id="publishedContent"></div>
</div>


<script type="text/javascript">
   refreshDashboard('browse');
   refreshDashboardSpe('published');
   switchPannel('xxx', 'xxx', 'xxx');

   //setTimeout("document.location=document.location",180000);
</script>


<?php
}
    include('includes/footer.php');
	