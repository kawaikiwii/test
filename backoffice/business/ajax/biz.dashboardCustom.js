function switchPannel(linkId, pannelName, pannelId)
{

	if (pannelId != "xxx")
	{
		var currentTabOpenState = $(pannelId).style.display;
		var currentTabClassName = linkId.className;
	}
	
	if ($("actions_created") != null) 		$("actions_created").style.display = "none";
	if ($("actions_modified") != null) 		$("actions_modified").style.display = "none";
	if ($("import_afp") != null) 			$("import_afp").style.display = "none";
	if ($("import_afp_video") != null) 		$("import_afp_video").style.display = "none";
	if ($("import_fil") != null) 			$("import_fil").style.display = "none";
	if ($("published_news") != null) 		$("published_news").style.display = "none";
	if ($("published_eventStart") != null) 	$("published_eventStart").style.display = "none";
	if ($("published_event") != null) 		$("published_event").style.display = "none";
	if ($("published_slideshow") != null) 	$("published_slideshow").style.display = "none";
	if ($("published_video") != null) 		$("published_video").style.display = "none";
	
	if ($("published_afpENnews") != null) 		$("published_afpENnews").style.display = "none";
	if ($("published_filFRnews") != null) 		$("published_filFRnews").style.display = "none";
	if ($("published_afpENslideshow") != null) 	$("published_afpENslideshow").style.display = "none";
	if ($("published_afpFRnews") != null) 		$("published_afpFRnews").style.display = "none";
	
	if ($("browse_news") != null) 			$("browse_news").style.display = "none";
	if ($("browse_event") != null) 			$("browse_event").style.display = "none";
	if ($("browse_slideshow") != null) 		$("browse_slideshow").style.display = "none";
	if ($("browse_video") != null) 			$("browse_video").style.display = "none";
	
	if ($("browse_news_link") != null) 		$('browse_news_link').className = 'passiveState';
	if ($("browse_event_link") != null) 	$('browse_event_link').className = 'passiveState';
	if ($("browse_slideshow_link") != null) $('browse_slideshow_link').className = 'passiveState';
	if ($("browse_video_link") != null) 	$('browse_video_link').className = 'passiveState';
	if ($("published_news_link") != null) 	$('published_news_link').className = 'passiveState';
	if ($("published_event_link") != null) 	$('published_event_link').className = 'passiveState';
	if ($("published_eventStart_link") != null) $('published_eventStart_link').className = 'passiveState';
	if ($("published_slideshow_link") != null) 	$('published_slideshow_link').className = 'passiveState';
	if ($("published_video_link") != null) 	$('published_video_link').className = 'passiveState';
	
	if ($("published_afpENnews_link") != null) 		$('published_afpENnews_link').className = 'passiveState';
	if ($("published_filFRnews_link") != null) 		$('published_filFRnews_link').className = 'passiveState';
	if ($("published_afpENslideshow_link") != null) $('published_afpENslideshow_link').className = 'passiveState';
	if ($("published_afpFRnews_link") != null) 		$('published_afpFRnews_link').className = 'passiveState';
	
	if ($("import_afp_link") != null) 		$('import_afp_link').className = 'passiveState';
	if ($("import_afp_video_link") != null) $('import_afp_video_link').className = 'passiveState';
	if ($("import_fil_link") != null) 		$('import_fil_link').className = 'passiveState';
	if ($("actions_modified_link") != null) $('actions_modified_link').className = 'passiveState';
	if ($("actions_created_link") != null) 	$('actions_created_link').className = 'passiveState';

	if (pannelId != "xxx")
	{
		$(pannelId).style.display = (currentTabOpenState == "block") ? "none" : "block";
		linkId.className = (currentTabClassName == 'passiveState') ? 'activeState' : 'passiveState';
	}
}


/*ARe = {
   pview: function(objectClass, objectId){
      window.open("/?_wcmAction=business/" + objectClass + "&id=" + objectId);
   },
};*/

var periodicRefreshFront = null;
var periodicRefreshBackBrowse = null;

function startPeriodicRefresh(theObject, dashboardZone, delay)
{
   if (theObject == 'front')
   {
      periodicRefreshFront = new PeriodicalExecuter(dashboardZone, delay);
   }
   else if (theObject == 'browse')
   {
      periodicRefreshBackBrowse = new PeriodicalExecuter(dashboardZone, delay);
   }

   alert('Periodic Refresh Started');
}

function stopPeriodicRefresh(theObject)
{
   if (theObject == 'front')
   {
      periodicRefreshFront.stop();
   }
   else if (theObject == 'browse')
   {
      periodicRefreshBackBrowse.stop();
   }

   alert('Periodic Refresh Stopped');
}

function refreshDashboard(itemId)
{
    wcmBizAjaxController.call("biz.dashboardCustom", {
        command: 'refresh',
	itemId: itemId
    });
}

function refreshDashboardSpe(itemId)
{
    wcmBizAjaxController.call("biz.dashboardCustomSpe", {
        command: 'refresh',
	itemId: itemId
    });
}

function duplicateInCurrentUniverse(destinationSiteId, itemId, className)
{
    wcmBizAjaxController.call("biz.dashboardCustom", {
        command: 'duplicate',
        destinationSiteId: destinationSiteId,
        itemId: itemId,
        className: className
        });
    refreshDashboard('browse');
    $("browse_news").style.display = "none";
    $("browse_event").style.display = "none";
    $("browse_slideshow").style.display = "none";
    $("browse_video").style.display = "none";
}



