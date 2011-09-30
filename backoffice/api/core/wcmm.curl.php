<?php

/*$urlsTemplates = array(
						array(
							'name' => 'Letters Links',
							'xpath' => '/html/body/div/div/div//table/tr/td/a',
							'parseall' => false),
						array(
							'name' => 'Artists Names + Links',
							'xpath' => '/html/body/div/div//table/tr/td//a',
							'parseall' => false),
							// P lease add a check on the catched URL : is this already in DB ?
						array(
							'name' => 'Artists Albums Name',
							'xpath' => '/html/body/div/div//table/tr/td/b',
							'parseall' => true),
						array(
							'name' => 'Artists Songs Names + Links',
							'xpath' => '/html/body/div/div//table/tr/td//a',
							'parseall' => true));*/


//$target_url = "http://www.parolesmania.com/paroles_heavenwood_6126.html";
$target_url = "http://dev.bo.afprelax.net/api/core/wcmm.curl.html";
//$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
$userAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";

$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$html = curl_exec($ch);
if (!$html) {
	echo "<br />cURL error number:" .curl_errno($ch);
	echo "<br />cURL error:" . curl_error($ch);
	exit;
}
curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHtml($html);

$xpath = new DOMXPath($dom);
$domNodes = $xpath->query('//td/a');

for ($i = 0; $i < $domNodes->length; $i++)
{
	$domNode = $domNodes->item($i);
	$url = $domNode->getAttribute('href');
	echo "<br />".$domNode->nodeValue." - ".$url;
}

?>