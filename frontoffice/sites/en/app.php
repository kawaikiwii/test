<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "en";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (!isset($session->userId)) {
    header('Location: '.$site->url);
    exit();
}

$minifyJsBase = new Minify_Build($_gc["base.js"]);
$minifyJs = new Minify_Build($_gc[$site->code.".js"]);
$minifyCss = new Minify_Build($_gc["en.css"]);

ob_start("ob_gzhandler");
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo APP_TITLE?></title>
        <link rel="shortcut icon" href="<?php echo APP_FAVICON?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $minifyCss->uri('/min/m.php/en.css')?>" />
        <link rel='stylesheet' href="../../inc/js_timeline/styles.css" type='text/css' />
        <script>
		Timeline_ajax_url = "http://www.relaxfil.com/inc/js_timeline/ajax/api/simile-ajax-api.js";
		Timeline_urlPrefix = "http://www.relaxfil.com/inc/js_timeline/api/";       
		Timeline_parameters = "bundle=true&forceLocale=en";
		</script>
		<script src="http://www.relaxfil.com/inc/js_timeline/api/timeline-api.js" type="text/javascript"></script>
		<script>
		function performFiltering(timeline, bandIndices, text, quand, ville, raz) {
    	    timerID = null;
    	    
    	    if(raz)
    	    	var dateFromEvent = new Date(document.getElementById("event-startDate").value).add(Date.YEAR, -1);
    	    else
    	    	var dateFromEvent = new Date(document.getElementById("event-startDate").value);
    	    dateFromEvent = Date.parse(dateFromEvent)-3600000;
    	    var dateToEvent = Date.parse(document.getElementById("event-endDate").value)-3600000;
    	    
    	    var dayCenter = new Date().format("Y-m-d");
    	    var minDayDiff = "";
    	    var dayDiff = "";
    	    var filterMatcher = null;

    	    if (text.length > 0)
    	    	var regexText = new RegExp(text, "i");

    	    if (ville.length > 0)
    	        var regexVille = new RegExp(ville, "i");
    	    
    	    filterMatcher = function(evt) {
    	    	var textMatch = false;
    	    	var dateMatch = false;
    	    	var villeMatch = false;

    	    	if (text.length > 0) {
    	    		if(regexText.test(evt.getText()) || regexText.test(evt.getDescription()) || regexText.test(evt.getImageTitle()) || regexText.test(evt.getImageDescription()))
    	    			textMatch = true;
    	    	}
    	    	else
    	    		textMatch = true;

    	    	if (ville.length > 0) {
    	    		if(regexVille.test(evt.getVille()))
    	    			villeMatch = true;
    	    	}
    	    	else
    	    		villeMatch = true;
    	    	
    	    	if((quand == "start" && Date.parse(evt.getStart()) >= dateFromEvent && Date.parse(evt.getStart()) <= dateToEvent)
					|| (quand == "end" && Date.parse(evt.getEnd()) >= dateFromEvent && Date.parse(evt.getEnd()) <= dateToEvent)
					|| (quand == "between" && ((Date.parse(evt.getStart()) >= dateFromEvent && Date.parse(evt.getStart()) <= dateToEvent)
					|| (Date.parse(evt.getEnd()) >= dateFromEvent && Date.parse(evt.getEnd()) <= dateToEvent)
					|| (Date.parse(evt.getStart()) <= dateFromEvent && Date.parse(evt.getEnd()) >= dateToEvent)))){
    	    		dateMatch = true;
        	    	if(textMatch && villeMatch) {
        	    		dayDiff = Date.parse(document.getElementById("event-startDate").value)-Date.parse(evt.getStart());
	        	    	if(dayDiff < 0)
	    	            	dayDiff *= -1;
	                	if(minDayDiff == "" || dayDiff < minDayDiff) {
	                	    minDayDiff = dayDiff;
	                	    dayCenter = evt.getStart();
	                	}
        	    	}
        	    }
    	    	
    	    	return (textMatch && dateMatch && villeMatch);
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
                            moreMessageTemplate:    "%0 more events",
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
            var url = window.location.toString().substring(27);
            var preUrl = "";
            if(url == "/en/" || url == "/en")
            	preUrl = "../"
            tl.loadJSON(preUrl+"event_en.json?"+ (new Date().getTime()), function(json, url) { eventSource.loadJSON(json, url); });
        }
        
        var resizeTimerID = null;
        function onResizeTimeLine() {
            if (resizeTimerID == null && document.getElementById("tl").innerHTML != "") {
                resizeTimerID = window.setTimeout(function() {
                    resizeTimerID = null;
                    document.getElementById("tl").style.height = (document.getElementById("services").offsetHeight-103)+"px";
                    tl.layout();
                    ARe.search.qEventRAZ();
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
            </ul>
        </div>
        <div id="clock">
            <h3><span id="clockDate">date</span>&nbsp;<span id="clockTime">time</span></h3>
        </div>
        <div id="homes" class="x-hidden">
            <?php 
            $news = new news();
            $newsClass = ($news->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $event = new event();
            $eventClass = ($event->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $forecast = new forecast();
            $forecastClass = ($forecast->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $slideshow = new slideshow();
            $slideshowClass = ($slideshow->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
            $video = new video();
            $videoClass = ($video->isServiceAllowed("children")) ? "" : "ari-not-allowed";
            
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
            <div id="home-event" class="ari-home <?php echo $eventClass ?>">
                <div id="event-channel-ourselection">
                    <?php @ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/timeLine.html")?>
                </div>
                <!-- <div id="event-channel-mustsee">
                    <?php /*@ include ($config["wcm.webSite.repository"]."sites/".$site->code."/homes/event.mustSee.html")*/?>
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
        </div>
        <div id="folders" class="x-hidden">
        </div>
        <div id="myBins" class="x-hidden">
        </div>
        <div id="externalFolders" class="x-hidden">
        </div>
        <div id="contact" class="x-hidden ari-sidebar-panel-body">
            <?php 
            include_once (dirname(__FILE__).'/../../sidebar/question.php');
            ?>
        </div>
        <div id="help" class="x-hidden">
            <p>
                <h3 style="color:red"><?php echo _SIDEBAR_HELP_USERGUIDE?></h3>
                <a href="/rp/docs/userguide-<?php echo $language ?>.pdf" target="_blank"><?php echo _SIDEBAR_HELP_READIT?>.</a>
                <br/>
                <br/>
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
      
            <h1 class="ari-restricted-title">Sorry, your “relax” access doesn’t include this service yet.</h1>
            <?php 
            $subject = "I would like to subscribe to new AFP Relaxnews services";
            if (strpos("@afp.com", $CURRENT_ACCOUNT_MANAGER->email) > 0) {
                
            ?>
            <p>
                To subscribe it, please contact us : <a href="mailto:<?php echo $CURRENT_ACCOUNT_MANAGER->email; ?>?subject=<?php echo $subject; ?>"><?php echo $CURRENT_ACCOUNT_MANAGER->email; ?></a>
            </p>
            <?php 
            } elseif (strpos("@relaxnews.com", $CURRENT_ACCOUNT_MANAGER->email) > 0) {
            
            ?>
            <p>
                To subscribe it, please contact us : <a href="mailto:marketing@relaxnews.com?subject=<?php echo $subject; ?>">marketing@relaxnews.com / +33 1 53 19 89 70</a>
            </p>
            <?php 
            } else {
                
            ?>
            <p>
                To subscribe it, please contact us : <a href="mailto:contact@afprelaxnews.com?subject=<?php echo $subject; ?>">contact@afprelaxnews.com</a>
            </p>
            <?php 
            }
            ?>
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
