<?php
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "fra";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (!isset($session->userId)) {
    header('Location: '.$site->url);
    exit();
}

$minifyJsBase = new Minify_Build($_gc["base.js"]);
$minifyJs = new Minify_Build($_gc[$site->code.".js"]);
$minifyCss = new Minify_Build($_gc["fra.css"]);

ob_start("ob_gzhandler");
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_TITLE?></title>
        <link rel="shortcut icon" href="<?php echo APP_FAVICON?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCss->uri('/min/m.php/fra.css')?>" />
        <link rel='stylesheet' href="../../inc/js_timeline/styles.css" type='text/css' />
        <script>
		Timeline_ajax_url = "http://www.relaxfil.com/inc/js_timeline/ajax/api/simile-ajax-api.js";
		Timeline_urlPrefix = "http://www.relaxfil.com/inc/js_timeline/api/";       
		Timeline_parameters = "bundle=true&forceLocale=fr";
		</script>
        <script src="http://www.relaxfil.com/inc/js_timeline/api/timeline-api.js" type="text/javascript"></script>
    	<script>
    	function performFiltering(timeline, bandIndices, text, quand, rubric, types, note, raz) {
    	    timerID = null;
    	    
    	    if(raz)
    	    	var dateFromPrevision = new Date(document.getElementById("prevision-startDate").value).add(Date.YEAR, -1);
    	    else
    	    	var dateFromPrevision = new Date(document.getElementById("prevision-startDate").value);
    	    dateFromPrevision = Date.parse(dateFromPrevision)-3600000;
    	    var dateToPrevision = Date.parse(document.getElementById("prevision-endDate").value)-3600000;
    	    
    	    var dayCenter = new Date().format("Y-m-d");
    	    var minDayDiff = "";
    	    var dayDiff = "";
    	    var filterMatcher = null;

    	    if (text.length > 0)
    	    	var regexText = new RegExp(text, "i");

    	    if (rubric.length > 0)
        	    var rubricArray = rubric.split(',');

    	    if (types.length > 0)
        	    var typesArray = types.split(',');
    	    
    	    filterMatcher = function(evt) {
    	    	var textMatch = false;
    	    	var dateMatch = false;
    	    	var rubricMatch = false;
    	    	var typesMatch = false;
    	    	var noteMatch = false;

    	    	if (text.length > 0) {
    	    		if(regexText.test(evt.getText()) || regexText.test(evt.getDescription()) || regexText.test(evt.getImageTitle()) || regexText.test(evt.getImageDescription()))
    	    			textMatch = true;
    	    	}
    	    	else
    	    		textMatch = true;

    	    	if (rubric.length > 0) {
    	    		var eventRubricArray = unserialize(evt.getChannelIds());
        	        var eventRubricCount = count(eventRubricArray);
        	        for(var i=0; i<eventRubricCount; i++) {
        	        	if(in_array(eventRubricArray[i], rubricArray))
			        	    rubricMatch = true;
        	        }
    	    	}
    	    	else
    	    		rubricMatch = true;
    	    	
    	    	if (types.length > 0) {
    	    		var eventTypesArray = unserialize(evt.getListIds());
        	        var eventTypesCount = count(eventTypesArray);
        	        for(var i=0; i<eventTypesCount; i++) {
        	        	if(in_array(eventTypesArray[i], typesArray))
			        	    typesMatch = true;
        	        }
    	    	}
    	    	else
    	    		typesMatch = true;
    	    	
    	    	if (note.length > 0) {
    	    		if(evt.getRatingValue() == note)
    	    			noteMatch = true;
    	    	}
    	    	else
    	    		noteMatch = true;

    	    	if((quand == "start" && Date.parse(evt.getStart()) >= dateFromPrevision && Date.parse(evt.getStart()) <= dateToPrevision)
					|| (quand == "end" && Date.parse(evt.getEnd()) >= dateFromPrevision && Date.parse(evt.getEnd()) <= dateToPrevision)
					|| (quand == "between" && ((Date.parse(evt.getStart()) >= dateFromPrevision && Date.parse(evt.getStart()) <= dateToPrevision)
					|| (Date.parse(evt.getEnd()) >= dateFromPrevision && Date.parse(evt.getEnd()) <= dateToPrevision)
					|| (Date.parse(evt.getStart()) <= dateFromPrevision && Date.parse(evt.getEnd()) >= dateToPrevision)))){
    	    		dateMatch = true;
        	    	if(textMatch && rubricMatch && typesMatch && noteMatch) {
        	    		dayDiff = Date.parse(document.getElementById("prevision-startDate").value)-Date.parse(evt.getStart());
	        	    	if(dayDiff < 0)
	    	            	dayDiff *= -1;
	                	if(minDayDiff == "" || dayDiff < minDayDiff) {
	                	    minDayDiff = dayDiff;
	                	    dayCenter = evt.getStart();
	                	}
        	    	}
        	    }
    	    	
    	    	return (textMatch && dateMatch && rubricMatch && typesMatch && noteMatch);
    	    }
    	    
    	    for (var i = 0; i < bandIndices.length; i++) {
    	        var bandIndex = bandIndices[i];
    	        timeline.getBand(bandIndex).getEventPainter().setFilterMatcher(filterMatcher);
    	    }
    	    timeline.paint();
    	    
    	    timeline.getBand(0).scrollToCenter(Date.parse(dayCenter));
    	}
    	
    	Date.prototype.sameDayEachWeek = function (d, date) { /* Returns array of dates of same day each week in range */
			var aDays = new Array();
			var eDate, nextDate, adj;

			if (this > date) {
				eDate = this;
				nextDate = new Date(date.getTime());
			} else {
				eDate = date;
				nextDate = new Date(this.getTime());
			}
			/* Find when the first week day of interest occurs */
			adj = (d - nextDate.getDay() + 7) %7;
			if(adj == 0)
				adj += 7;
			nextDate.setDate(nextDate.getDate() + adj);
			while (nextDate < eDate) {
				aDays[aDays.length] = new Date(nextDate.getTime());
				nextDate.setDate(nextDate.getDate() + 7);
			}

			return aDays;
		};
    	
		var tl;
        
        function onLoadTimeLine() {
            var eventSource = new Timeline.DefaultEventSource(0);
            
            var theme = Timeline.ClassicTheme.create();
            theme.event.instant.icon = "no-image-40.png";
            theme.event.instant.iconWidth = 40;  // These are for the default stand-alone icon
            theme.event.instant.iconHeight = 40;
            var d = new Date();
            theme.timeline_start = new Date(Date.UTC(d.getFullYear()-1, d.getMonth(), d.getDate()));
            theme.timeline_stop = new Date(Date.UTC(d.getFullYear()+1, d.getMonth(), d.getDate()));
            
            var bandInfos = [
                Timeline.createBandInfo({
                    width:          "88%", 
                    intervalUnit:   Timeline.DateTime.DAY, 
                    intervalPixels: 190,
                    eventSource:    eventSource,
					timeZone:       1,
                    theme:          theme,
                    eventPainter:   Timeline.CompactEventPainter,
                    eventPainterParams: {
                        iconLabelGap:     5,
                        labelRightMargin: 20,
                        
                        iconWidth:        55, // These are for per-event custom icons
                        iconHeight:       55,
                        
                        stackConcurrentPreciseInstantEvents: {
                            limit: 9,
                            moreMessageTemplate:    "%0 Prévisions supplémentaires",
                            icon:                   "no-image-80.png", // default icon in stacks
                            iconWidth:              80,
                            iconHeight:             80
                        }
                    }
                }),
                Timeline.createBandInfo({
                    width:          "12%", 
                    intervalUnit:   Timeline.DateTime.MONTH, 
                    intervalPixels: 115,
                    eventSource:    eventSource,
                    theme:          theme,
                    layout:         'overview'  // original, overview, detailed
                })
            ];
            bandInfos[1].syncWith = 0;
            bandInfos[0].highlight = true;
            bandInfos[1].highlight = true;
            
            bandInfos[0].decorators = [];
			
			var listOfSaturdays = theme.timeline_start.sameDayEachWeek(6, theme.timeline_stop);
			var listOfSundays = theme.timeline_start.sameDayEachWeek(0, theme.timeline_stop);
			
			var weekendStartDateTime = new Date();
			var weekendStopDateTime = new Date();
			
			for (var i2 = 0; i2 < listOfSaturdays.length; i2++) {
				weekendStartDateTime = new Date(listOfSaturdays[i2]);
				weekendEndDateTime = new Date(listOfSundays[i2]);
				if((weekendEndDateTime.getMonth() > 2 && weekendEndDateTime.getMonth() <= 9) || (weekendEndDateTime.getMonth() == 2 && weekendEndDateTime.getDate() == 31))
					weekendStartDateTime.setHours(01);
				else
					weekendStartDateTime.setHours(00);
				weekendStartDateTime.setMinutes(00);
				weekendStartDateTime.setSeconds(00);
				
				if((weekendEndDateTime.getMonth() > 2 && weekendEndDateTime.getMonth() < 9) || (weekendEndDateTime.getMonth() == 2 && weekendEndDateTime.getDate() >= 25) || (weekendEndDateTime.getMonth() == 9 && weekendEndDateTime.getDate() < 25))
					weekendEndDateTime.setHours(24);
				else
					weekendEndDateTime.setHours(23);
				weekendEndDateTime.setMinutes(59);
				weekendEndDateTime.setSeconds(59);
				
				bandInfos[0].decorators.push(new Timeline.SpanHighlightDecorator({
					startDate: Timeline.DateTime.parseGregorianDateTime(weekendStartDateTime),
					endDate: Timeline.DateTime.parseGregorianDateTime(weekendEndDateTime),
					color: "#FFC080",
					opacity: 50,
					theme: theme
				}));
			}

            document.getElementById("tl").style.height = (document.getElementById("services").offsetHeight-103)+"px";
            
            tl = Timeline.create(document.getElementById("tl"), bandInfos, Timeline.HORIZONTAL);
            tl.loadJSON("previsions.json?"+ (new Date().getTime()), function(json, url) { eventSource.loadJSON(json, url); });
        }
        
        var resizeTimerID = null;
        function onResizeTimeLine() {
            if (resizeTimerID == null && document.getElementById("tl").innerHTML != "") {
                resizeTimerID = window.setTimeout(function() {
                    resizeTimerID = null;
                    document.getElementById("tl").style.height = (document.getElementById("services").offsetHeight-103)+"px";
                    tl.layout();
                    ARe.search.qPrevisionRAZ();
                }, 500);
            }
        }
    	</script>
    	<style>
        div.timeline-event-icon {
            border: 1px solid #aaa;
            padding: 2px;
        }
        div.timeline-event-icon-default {
            border: none;
            padding: 0px;
        }
    	</style>
    </head>
    <body onresize="onResizeTimeLine();">
        <div id="loading-mask">
            <div id="loading">
                <div class="loading-message">
                    <h4>Chargement...</h4>
                    <div class="loading-indicator">
                        <br/>
                    </div>
                </div>
            </div>
        </div>
        <div id="logo">
            <div class="ari-logo">
                <a href="#" onclick="ARi.services.setActiveTab(0);"><img src="<?php echo APP_LOGO_IMG ?>" alt="<?php echo APP_LOGO_IMGALT ?>" title="<?php echo APP_LOGO_IMGALT ?>"/></a>
            </div><h3 class="ari-firstsource"><?php echo APP_LOGO_TITLE?></h3>
        </div>
        <div id="qBar" style="margin-top:22px;">
        </div>
        <div id="user">
            <ul>
                <li>
                    <a href="#" onclick="ARe.profile();"><?php echo str_replace("|", " ", $CURRENT_USER->name)?></a>
                </li>
                <li>
                    <a href="/logout.php" title="Déconnexion" style="position:reltavive;top:4px;"><img src="/rp/images/default/16x16/logout.gif" border="0" alt="Déconnexion"/></a>
                </li>
            </ul><a href="http://old.relaxfil.com/relaxfil.asp" target="_blank" style="color:red">Accès au service événements</a>&nbsp;&nbsp;
        </div>
        <div id="clock">
            <h3><span id="clockDate">date</span>&nbsp;<span id="clockTime">time</span></h3>
        </div>
        <div id="homes" class="x-hidden">
            <?php 
            $news = new news();
            $newsClass = ($news->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $prevision = new prevision();
            $previsionClass = ($prevision->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $slideshow = new slideshow();
            $slideshowClass = ($slideshow->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $video = new video();
            $videoClass = ($video->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $event = new event();
            $eventClass = ($event->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            ?>
            <div id="home-news" class="ari-home <?php echo $newsClass ?>">
                <div id="news-channel-wellbeing">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/news.wellbeing.html")?>
                </div>
                <div id="news-channel-househome">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/news.househome.html")?>
                </div>
                <div id="news-channel-entertainment">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/news.entertainment.html")?>
                </div>
                <div id="news-channel-tourism">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/news.tourism.html")?>
                </div>
            </div>
            <div id="home-prevision" class="ari-home <?php echo $previsionClass ?>">
                <div id="prevision-left">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/timeLine.html")?>
                </div>
                <!--<div id="forecast-center">
                    <?php /*@ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/forecast.center.html")*/?>
                </div>
                <div id="forecast-right">
                    <?php /*@ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/forecast.right.html")*/?>
                </div>-->
            </div>
            <div id="home-slideshow"class="ari-home <?php echo $slideshowClass ?>">
                <div id="slideshow-channel-wellbeing">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/slideshow.wellbeing.html")?>
                </div>
                <div id="slideshow-channel-househome">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/slideshow.househome.html")?>
                </div>
                <div id="slideshow-channel-entertainment">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/slideshow.entertainment.html")?>
                </div>
                <div id="slideshow-channel-tourism">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/slideshow.tourism.html")?>
                </div>
            </div>
            <div id="home-video"class="ari-home <?php echo $videoClass ?>">
                <div id="video-channel-wellbeing">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/video.wellbeing.html")?>
                </div>
                <div id="video-channel-househome">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/video.househome.html")?>
                </div>
                <div id="video-channel-entertainment">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/video.entertainment.html")?>
                </div>
                <div id="video-channel-tourism">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/video.tourism.html")?>
                </div>
            </div>
            <div id="home-event-rf" class="ari-home <?php echo $eventClass ?>">
            </div>
        </div>
        <div id="folders" class="x-hidden">
        </div>
        <div id="myBins" class="x-hidden">
        </div>
        <div id="externalFolders" class="x-hidden">
        </div>
        <div id="contact" class="x-hidden ari-sidebar-panel-body">
            <p>
                <h3><?php echo _SIDEBAR_HELP_EDITOQUESTION?>?</h3>
                <a href="mailto:redaction@relaxfil.com">redaction@relaxfil.com</a>
                <br/>
                <br/>
            </p>
            <p>
                <h3><?php echo _SIDEBAR_HELP_COMMERCIALQUESTION?>?</h3>
                <a href="mailto:support.marketing@relaxfil.com">support.marketing@relaxfil.com</a>
                <br/>
                <br/>
            </p>
            <p>
                <h3><?php echo _SIDEBAR_HELP_TECHNICALQUESTION?>?</h3>
                <a href="mailto:support.technique@relaxfil.com">support.technique@relaxfil.com</a>
                <br/>
                <br/>
            </p>
            <p>
                <h3><?php echo _SIDEBAR_HELP_OTHERQUESTION?>?</h3>
                <?php echo _SIDEBAR_HELPCONTACTSUPERVISOR?> :       <a href="mailto:<?php echo $CURRENT_ACCOUNT_MANAGER->email ?>"><?php echo getConst($CURRENT_ACCOUNT_MANAGER->name)?></a>
            </p>
        </div>
        <div id="help" class="x-hidden">
            <p>
                <h3><?php echo _SIDEBAR_HELP_OTHERQUESTION?>?</h3>
                <?php echo _SIDEBAR_HELPCONTACTSUPERVISOR?> :       <a href="mailto:<?php echo $CURRENT_ACCOUNT_MANAGER->email ?>"><?php echo getConst($CURRENT_ACCOUNT_MANAGER->name)?></a>
            </p>
        </div>
        <div id="previewzone" class="x-hidden">
            <?php 
            @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/lastNews.html");
            ?>
        </div>
        <div id="search" class="x-hidden">
        </div>
        <div id="restricted-service" class="x-hidden">
            <h1 class="ari-restricted-title">Désolé, votre relax accès n’inclut pas - encore - ce service.</h1>
            <p>
                Pour vous y abonner, merci de contacter : <a href="mailto:marketing@relaxnews.com?subject=Je souhaite m’abonner à des services supplémentaires du Relaxfil">marketing@relaxnews.com</a>
                / +33 1 53 19 89 70 
            </p>
        </div>
    </body>
    <script type="text/javascript" src="http://www.google-analytics.com/ga.js">
    </script>
    <script type="text/javascript" src="<?php echo $minifyJsBase->uri('/min/m.php/base.js') ?>      ">
    </script>
    <script type="text/javascript" src="<?php echo $minifyJs->uri('/min/m.php/'.$site->code.'.js') ?> ">
    </script>
</html>
<?php 
ob_flush();
?>
