<?php 

// initialisation de la latitude
if (isset($_GET['lat']) && !empty($_GET['lat']))
	$lat = $_GET['lat'];
else 
	$lat = "46";

// initialisation de la longitude
if (isset($_GET['lon']) && !empty($_GET['lon']))
	$lon = $_GET['lon'];
else 
	$lon = "2";
	
?>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>City Map</title>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

var geocoder = new google.maps.Geocoder();
var map;
var marker;

//récupère une adresse à partir de la position du marker sur la map
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      updateMarkerAddress(responses[0].formatted_address);
    } else {
      updateMarkerAddress('impossible de déterminer une adresse sur cette position.');
    }
  });
}

//Mise à jour du statut du marker
function updateMarkerStatus(str) {
  document.getElementById('markerStatus').innerHTML = str;
}

//Mise à jour de la position du marker
function updateMarkerPosition(latLng) {
  document.getElementById('coordonnees').value = [
    latLng.lat(),
    latLng.lng()
  ].join(', ');

  //Mise à jour des champs latitude et longitude de l'interface parente WCM
  parent.document.getElementById('latitude').value = latLng.lat();
  parent.document.getElementById('longitude').value = latLng.lng();
   
}

//Mise à jour de l'adresse suivant position du marker
function updateMarkerAddress(str) {
  document.getElementById('address').innerHTML = str;
}

// initialisation de la map
function initialize() {

	// détermine l'affichage des éléments de la carte
	var stylez = [ {
		 featureType: "all", elementType: "labels", stylers: [ { visibility: "on" } ] },{
		 featureType: "transit.station", elementType: "all", stylers: [ { visibility: "on" }, { hue: "#1100ff" } ] },{
		 featureType: "water", elementType: "all", stylers: [ { visibility: "on" }, { hue: "#0011ff" } ] },{
		 featureType: "road.highway", elementType: "all", stylers: [ { visibility: "on" }, { hue: "#ff7700" } ] },{
		 featureType: "landscape.man_made", elementType: "all", stylers: [ { visibility: "on" }, { hue: "#00a1ff" }, { saturation: 66 }, { lightness: 0 } ] },{
		 featureType: "road.local", elementType: "all", stylers: [ { hue: "#08ff00" }, { saturation: 51 } ] }, {
	     featureType: "administrative", elementType: "all", stylers: [ { visibility: "on" } ] },{
	     featureType: "poi.place_of_worship", elementType: "all", stylers: [ { visibility: "on" } ] },{
	   	 featureType: "poi", elementType: "all", stylers: [ { visibility: "on" } ] 
	     } ];		 
	
  var latLng = new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lon;?>);
   map = new google.maps.Map(document.getElementById('mapCanvas'), {
	panControl: false,
	zoomControl: true,
	zoomControlOptions: {
	    style: google.maps.ZoomControlStyle.LARGE
	  },
	mapTypeControl: true,
	scaleControl: true,
	streetViewControl: false,
	overviewMapControl: false, 	   
    zoom: 11,
    center: latLng,
    mapTypeControlOptions: {
        mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'initial']
     }
 	
  });

   var styledMapOptions = {
		     name: "new map"
		  }

   var newMapType = new google.maps.StyledMapType(
	stylez, styledMapOptions);
	map.mapTypes.set('initial', newMapType);
	map.setMapTypeId('initial');
			
   
   marker = new google.maps.Marker({
    position: latLng,
    title: 'Lieu',
    map: map,
    draggable: true
  });
  
  // Mise à jour des infos de la postion courante.
  updateMarkerPosition(latLng);
  geocodePosition(latLng);
  
  //Mise en place d'un "listener" sur le drag du marker
  google.maps.event.addListener(marker, 'dragstart', function() {
    updateMarkerAddress('Dragging...');
  });
  
  google.maps.event.addListener(marker, 'drag', function() {
    //updateMarkerStatus('Dragging...');
    updateMarkerPosition(marker.getPosition());
  });
  
  google.maps.event.addListener(marker, 'dragend', function() {
    //updateMarkerStatus('Drag ended');
    geocodePosition(marker.getPosition());
  });
}

//initialisation de la position du marker par rapport aux champs adresse de l'interface parente
function codeAddress() 
{
	var address = parent.document.getElementById("address").value + ' ' + parent.document.getElementById("zipcode").value + ' ' + parent.document.getElementById("city").value ;
    if (address != '')
    {
	    geocoder.geocode( { 'address': address}, function(results, status) {
	      if (status == google.maps.GeocoderStatus.OK) {
	        map.setCenter(results[0].geometry.location);
	        marker.setPosition(results[0].geometry.location);
	        updateMarkerPosition(results[0].geometry.location);
	        geocodePosition(results[0].geometry.location);
	      } 
	      else 
	      {
	    	   //alert("Adresse non reconnue par google map : " + status);
		       alert("Adresse non reconnue par google map !");
	      }
	    });
    }
} 

// recentrage du marker sur la map
function centerOnMarker() 
{
	 initialLocation = new google.maps.LatLng(parent.document.getElementById('latitude').value,parent.document.getElementById('longitude').value);
     map.setCenter(initialLocation);
}

// initialisation au chargement de la page
google.maps.event.addDomListener(window, 'load', initialize);

</script>

</head>
<body style="margin:0px; padding:0px; font:verdana:">
  <div style="margin: 2px"> 
  	<input type="button" value="Initialiser avec l'adresse" onclick="codeAddress()"> 
    <input type="button" value="centrer position" onclick="centerOnMarker()"> 
  	<b>Latitude, Longitude :</b> <input type="text" id="coordonnees" style="width:240;" disabled="disabled">
  </div> 
  <div id="mapCanvas" style="width: 795px; height: 370px;"></div>
  <div style="margin: 2px"> 
  	<b>Adresse correspondante : </b><span id="address"></span>
  </div>
</body>
</html>
