<?php 

// initialisation de la carte à partir des coordonnées de la ville principale
if (isset($_GET['init']) && !empty($_GET['init']))
	$initMap = explode("#", $_GET['init']);

// récupération des données GPS pour chaque marker
if (isset($_GET['data']) && !empty($_GET['data']))
	$data = urldecode($_GET['data']);

$tabCoord = "";
$i = 1;

// récupère les données de la chaine passée dans l'iframe
// le séparateur principal par objet est le @
// le séparateur de données (titre, latitude, longitude) est le #

function wd_remove_accents($str, $charset='utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    
    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
    
    return $str;
}

function cleanData($data)
{
	$data = addslashes($data);
	$data = str_replace("\n"," ",$data);
	$data = str_replace("\r"," ",$data);
	$data = str_replace("\"","'",$data);
	$data = wd_remove_accents($data);
	$data = preg_replace("[^A-Z0-9\ ]", " ", $data);
	
	return $data;
}

if (!empty($data))
{
	$recup = explode("@",$data);
	if (!empty($recup))
	{
		foreach ($recup as $val)
		{
			$coord = explode("#",$val);
			{
				if (!empty($coord) && (isset($coord[2]) && !empty($coord[2]) && isset($coord[0]) && !empty($coord[0])))
				{
					$tabCoord .= "['".cleanData($coord[2])."', ".$coord[0].", ".$coord[1].", ".$i.", '".$coord[3]."', '".cleanData($coord[4])."', '".cleanData($coord[5])."', '".cleanData($coord[6])."'],\n";
					$i++;
				}
			}
		}
	}
}

?>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>City Map</title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
function initialize() {
  var myOptions = {
    zoom: 10,
    center: new google.maps.LatLng(<?php echo $initMap[0];?>,<?php echo $initMap[1];?>),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById("map_canvas"),
                                myOptions);

  setMarkers(map, datas);
}

// Datas pour les markers - tableau de données avec latitude, longitude, titre, adresse, codepostal, ville
var datas = [
<?php
if (!empty($tabCoord))
	echo $tabCoord;
?>
];

function attachInfowindow(marker, number, map, title, address, zipcode, city) 
{
	var contentString = '<p style=\'font-family:Arial,Helvetica; font-size:10pt\'><b>' + title + '</b><br /><br />' + address + '<br />' + zipcode + '<br />' + city + '</p>'; 
	var infowindow = new google.maps.InfoWindow({ content: contentString });
    google.maps.event.addListener(marker, 'click', function() {infowindow.open(map,marker);});
}

function setMarkers(map, locations) 
{
	for (var i = 0; i < locations.length; i++) 
	{
		var data = locations[i];
	    var myLatLng = new google.maps.LatLng(data[1], data[2]);
	    var img = '../../../img/icons/markers/' + data[4];	    		
	    var marker = new google.maps.Marker({
	        position: myLatLng,
	        map: map,
	        title: data[0],
	        zIndex: data[3],
	        icon:img
	    });

	    attachInfowindow(marker, i, map, data[0], data[5], data[6], data[7]);  	    
	  }
}
</script>

</head>
<body style="margin:0px; padding:0px;" onload="initialize()">
  <div id="map_canvas" style="width: 800px; height: 500px;"></div>
</body>
</html>
