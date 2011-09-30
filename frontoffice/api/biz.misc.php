<?php

function die_ie($default_statement='', $language) {					
	$announcement = _DIE_IE;
	$announcement .= "<p><ul>";
	$announcement .= "<li><a href=\"http://www.getfirefox.com/\" target=\"_blank\">Mozilla Firefox</a> [PC, MAC, Linux]</li>";
	$announcement .= "<li><a href=\"http://www.apple.com/safari/download/\" target=\"_blank\">Apple Safari</a> [MAC]</li>";
	$announcement .= "<li><a href=\"http://www.google.com/chrome/\" target=\"_blank\">Google Chrome</a> [PC]</li>";
	$announcement .= "<li><a href=\"http://www.microsoft.com/windows/downloads/ie/\" target=\"_blank\">Microsoft Internet Explorer</a> [PC]</li>";
	$announcement .= "</ul></p>";
	$announcement = "<div id=\"dieIE\">$announcement</div>";
		
	$agent_left = substr($_SERVER['HTTP_USER_AGENT'], 0, 31);
	if ( $agent_left=='Mozilla/4.0 (compatible; MSIE 5' ) {
		return $announcement;
	} elseif ( $agent_left=='Mozilla/4.0 (compatible; MSIE 6' OR $agent_left=='Mozilla/4.0 (compatible;)' ) { 
		return $announcement;
	} elseif ( $agent_left=='Mozilla/4.0 (compatible; MSIE 7' ) { 
		return $default_statement;
	} elseif ( $agent_left=='Mozilla/4.0 (compatible; MSIE 8' ) { 
		return $default_statement;
	} else {
		return $default_statement;
	}
}

?>