<?php
/**
 * Project:     WCM
 * File:        echoCity.php
 *
 * @copyright   (c)2010 Relaxnews
 * @version     4.x
 *
 */
 
/**
 * This class implements the action controller for the channel
 */
class echoCityAction extends wcmMVC_BizAction
{
    /**
     * Save last edited design zones
     */
    private function saveZones()
    {
        // Retrieve new design zones?
        $zones = getArrayParameter($_REQUEST, '_zones', null);
        $this->context->updateZones($zones);
    }
      
    /**
	 * Save chapters from content tab
	 */
	private function saveInserts()
	{
		ini_set('max_execution_time', 120);
        
		$config = wcmConfig::getInstance();
    
		$echoEmbassy = array();
		$echoHospital = array();
		$echoPolice = array();
		$echoAirport = array();
		$echoSlideshow = array();
		$echoHousing = array();
		$echoTakeOuts = array();
		$echoMustSees = array();
		$echoEvent = array();
		$echoStation = array();
		$echoShow = array();
		
		$photoPath = WCM_DIR.DIRECTORY_SEPARATOR.$config['wcm.backOffice.photosPathLesEchos'];
		
		if (isset($_FILES["mapCityFile"]) && !empty($_FILES["mapCityFile"]))
		{
			if (move_uploaded_file($_FILES["mapCityFile"]["tmp_name"], $photoPath.DIRECTORY_SEPARATOR.$_FILES["mapCityFile"]["name"]))
				$_REQUEST["mapCityFile"] = $_FILES["mapCityFile"]["name"];
		}
		
		if (isset($_FILES["mapTransportFile"]) && !empty($_FILES["mapTransportFile"]))
		{
			if (move_uploaded_file($_FILES["mapTransportFile"]["tmp_name"], $photoPath.DIRECTORY_SEPARATOR.$_FILES["mapTransportFile"]["name"]))
				$_REQUEST["mapTransportFile"] = $_FILES["mapTransportFile"]["name"];	
		}		

		$mainCity = getArrayParameter($_REQUEST, 'title');
		if (empty($mainCity) && isset($this->context->title)) $mainCity = $this->context->title;
		$mainCountry = getArrayParameter($_REQUEST, 'country');
		if (empty($mainCountry) && isset($this->context->country)) $mainCountry = $this->context->country;
		$maincityGeonameId = getArrayParameter($_REQUEST, 'cityGeoNameId');
		if (empty($maincityGeonameId) && isset($this->context->cityGeoNameId)) $maincityGeonameId = $this->context->cityGeoNameId;
		$mainCountryGeonameId = getArrayParameter($_REQUEST, 'countryGeoNameId');
		if (empty($mainCountryGeonameId) && isset($this->context->countryGeoNameId)) $mainCountryGeonameId = $this->context->countryGeoNameId;
		
		if(isset($_REQUEST['echoEmbassy_title'])) 
		{
			$echoEmbassyId 			= getArrayParameter($_REQUEST, 'echoEmbassy_id');
			$echoEmbassyTitle 		= getArrayParameter($_REQUEST, 'echoEmbassy_title');
			$echoEmbassyAddress 	= getArrayParameter($_REQUEST, 'echoEmbassy_address');
			$echoEmbassyPostalCode 	= getArrayParameter($_REQUEST, 'echoEmbassy_postalCode');
			$echoEmbassyCity 		= getArrayParameter($_REQUEST, 'echoEmbassy_city');
			$echoEmbassyCountry 	= getArrayParameter($_REQUEST, 'echoEmbassy_country');
			$echoEmbassyPhone 		= getArrayParameter($_REQUEST, 'echoEmbassy_phone');
			$echoEmbassyEmail 		= getArrayParameter($_REQUEST, 'echoEmbassy_email');
			$echoEmbassyWeb 		= getArrayParameter($_REQUEST, 'echoEmbassy_web');
			$echoEmbassyCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoEmbassy_cityGeoNameId');
			$echoEmbassyCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoEmbassy_countryGeoNameId');
			$echoEmbassyLongitude 	= getArrayParameter($_REQUEST, 'echoEmbassy_longitude');
			$echoEmbassyLatitude 	= getArrayParameter($_REQUEST, 'echoEmbassy_latitude');
			$echoEmbassyAutogmap 	= getArrayParameter($_REQUEST, 'echoEmbassy_autogmap');
			
			if(is_array($echoEmbassyTitle))
			{
				foreach($echoEmbassyTitle as $key => $title)
				{
					$city = getArrayParameter($echoEmbassyCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoEmbassyCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoEmbassyCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoEmbassyCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoEmbassy[] = array(
					'id' => getArrayParameter($echoEmbassyId, $key), 
					'title' => getArrayParameter($echoEmbassyTitle, $key), 
					'address' => getArrayParameter($echoEmbassyAddress, $key),
					'postalCode' => getArrayParameter($echoEmbassyPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoEmbassyPhone, $key),
					'email' => getArrayParameter($echoEmbassyEmail, $key),
					'web' => getArrayParameter($echoEmbassyWeb, $key),
					'longitude' => getArrayParameter($echoEmbassyLongitude, $key),
					'latitude' => getArrayParameter($echoEmbassyLatitude, $key),
					'autogmap' => getArrayParameter($echoEmbassyAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoEmbassy[] = array(
			'title' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
		
		if(isset($_REQUEST['echoHospital_title'])) 
		{
			$echoHospitalId 		= getArrayParameter($_REQUEST, 'echoHospital_id');
			$echoHospitalTitle 		= getArrayParameter($_REQUEST, 'echoHospital_title');
			$echoHospitalAddress 	= getArrayParameter($_REQUEST, 'echoHospital_address');
			$echoHospitalPostalCode 	= getArrayParameter($_REQUEST, 'echoHospital_postalCode');
			$echoHospitalCity 		= getArrayParameter($_REQUEST, 'echoHospital_city');
			$echoHospitalCountry 	= getArrayParameter($_REQUEST, 'echoHospital_country');
			$echoHospitalPhone 		= getArrayParameter($_REQUEST, 'echoHospital_phone');
			$echoHospitalEmail 		= getArrayParameter($_REQUEST, 'echoHospital_email');
			$echoHospitalWeb 		= getArrayParameter($_REQUEST, 'echoHospital_web');
			$echoHospitalCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoHospital_cityGeoNameId');
			$echoHospitalCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoHospital_countryGeoNameId');
			$echoHospitalLongitude 	= getArrayParameter($_REQUEST, 'echoHospital_longitude');
			$echoHospitalLatitude 	= getArrayParameter($_REQUEST, 'echoHospital_latitude');
			$echoHospitalAutogmap 	= getArrayParameter($_REQUEST, 'echoHospital_autogmap');
			
			if(is_array($echoHospitalTitle))
			{
				foreach($echoHospitalTitle as $key => $title)
				{
					$city = getArrayParameter($echoHospitalCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoHospitalCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoHospitalCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoHospitalCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoHospital[] = array(
					'id' => getArrayParameter($echoHospitalId, $key), 
					'title' => getArrayParameter($echoHospitalTitle, $key), 
					'address' => getArrayParameter($echoHospitalAddress, $key),
					'postalCode' => getArrayParameter($echoHospitalPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoHospitalPhone, $key),
					'email' => getArrayParameter($echoHospitalEmail, $key),
					'web' => getArrayParameter($echoHospitalWeb, $key),
					'longitude' => getArrayParameter($echoHospitalLongitude, $key),
					'latitude' => getArrayParameter($echoHospitalLatitude, $key),
					'autogmap' => getArrayParameter($echoHospitalAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoHospital[] = array(
			'title' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
		
		if(isset($_REQUEST['echoPolice_title'])) 
		{
			$echoPoliceId 		= getArrayParameter($_REQUEST, 'echoPolice_id');
			$echoPoliceTitle 		= getArrayParameter($_REQUEST, 'echoPolice_title');
			$echoPoliceAddress 	= getArrayParameter($_REQUEST, 'echoPolice_address');
			$echoPolicePostalCode 	= getArrayParameter($_REQUEST, 'echoPolice_postalCode');
			$echoPoliceCity 		= getArrayParameter($_REQUEST, 'echoPolice_city');
			$echoPoliceCountry 	= getArrayParameter($_REQUEST, 'echoPolice_country');
			$echoPolicePhone 		= getArrayParameter($_REQUEST, 'echoPolice_phone');
			$echoPoliceEmail 		= getArrayParameter($_REQUEST, 'echoPolice_email');
			$echoPoliceWeb 		= getArrayParameter($_REQUEST, 'echoPolice_web');
			$echoPoliceCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoPolice_cityGeoNameId');
			$echoPoliceCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoPolice_countryGeoNameId');
			$echoPoliceLongitude 	= getArrayParameter($_REQUEST, 'echoPolice_longitude');
			$echoPoliceLatitude 	= getArrayParameter($_REQUEST, 'echoPolice_latitude');
			$echoPoliceAutogmap 	= getArrayParameter($_REQUEST, 'echoPolice_autogmap');
			
			if(is_array($echoPoliceTitle))
			{
				foreach($echoPoliceTitle as $key => $title)
				{
					$city = getArrayParameter($echoPoliceCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoPoliceCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoPoliceCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoPoliceCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoPolice[] = array(
					'id' => getArrayParameter($echoPoliceId, $key), 
					'title' => getArrayParameter($echoPoliceTitle, $key), 
					'address' => getArrayParameter($echoPoliceAddress, $key),
					'postalCode' => getArrayParameter($echoPolicePostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoPolicePhone, $key),
					'email' => getArrayParameter($echoPoliceEmail, $key),
					'web' => getArrayParameter($echoPoliceWeb, $key),
					'longitude' => getArrayParameter($echoPoliceLongitude, $key),
					'latitude' => getArrayParameter($echoPoliceLatitude, $key),
					'autogmap' => getArrayParameter($echoPoliceAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoPolice[] = array(
			'title' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
			
		if(isset($_REQUEST['echoAirport_title'])) 
		{
			$echoAirportId 		= getArrayParameter($_REQUEST, 'echoAirport_id');
			$echoAirportTitle 		= getArrayParameter($_REQUEST, 'echoAirport_title');
			$echoAirportDescription 		= getArrayParameter($_REQUEST, 'echoAirport_description');
			$echoAirportAddress 	= getArrayParameter($_REQUEST, 'echoAirport_address');
			$echoAirportPostalCode 	= getArrayParameter($_REQUEST, 'echoAirport_postalCode');
			$echoAirportMapTransportation 	= getArrayParameter($_REQUEST, 'echoAirport_transportation');
			$echoAirportCity 		= getArrayParameter($_REQUEST, 'echoAirport_city');
			$echoAirportCountry 	= getArrayParameter($_REQUEST, 'echoAirport_country');
			$echoAirportPhone 		= getArrayParameter($_REQUEST, 'echoAirport_phone');
			$echoAirportEmail 		= getArrayParameter($_REQUEST, 'echoAirport_email');
			$echoAirportWeb 		= getArrayParameter($_REQUEST, 'echoAirport_web');
			$echoAirportCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoAirport_cityGeoNameId');
			$echoAirportCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoAirport_countryGeoNameId');
			$echoAirportMapTitle 		= getArrayParameter($_REQUEST, 'echoAirport_mapTitle');
			$echoAirportMapCredits 		= getArrayParameter($_REQUEST, 'echoAirport_mapCredits');
			//$echoAirportMapFile 		= getArrayParameter($_REQUEST, 'echoAirport_mapFile');
			$echoAirportMapFile2 	= getArrayParameter($_REQUEST, 'echoAirport_mapFile2');
			$echoAirportLongitude 	= getArrayParameter($_REQUEST, 'echoAirport_longitude');
			$echoAirportLatitude 	= getArrayParameter($_REQUEST, 'echoAirport_latitude');
			$echoAirportAutogmap 	= getArrayParameter($_REQUEST, 'echoAirport_autogmap');
			
			if(is_array($echoAirportTitle))
			{
				foreach($echoAirportTitle as $key => $title)
				{						
					if (!empty($_FILES["echoAirport_mapFile"]["tmp_name"][$key]) && move_uploaded_file($_FILES["echoAirport_mapFile"]["tmp_name"][$key], $photoPath.DIRECTORY_SEPARATOR.$_FILES["echoAirport_mapFile"]["name"][$key]))
						$echoAirportMapFile[$key] = $_FILES["echoAirport_mapFile"]["name"][$key];
					else
						$echoAirportMapFile[$key] = $echoAirportMapFile2[$key];
					
					$city = getArrayParameter($echoAirportCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoAirportCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoAirportCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoAirportCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoAirport[] = array(
						'id' => getArrayParameter($echoAirportId, $key), 
						'title' => getArrayParameter($echoAirportTitle, $key), 
						'description' => getArrayParameter($echoAirportDescription, $key), 
						'address' => getArrayParameter($echoAirportAddress, $key),
						'postalCode' => getArrayParameter($echoAirportPostalCode, $key),
						'transportation' => getArrayParameter($echoAirportMapTransportation, $key),
						'city' => $city,
						'country' => $country,
						'phone' => getArrayParameter($echoAirportPhone, $key),
						'email' => getArrayParameter($echoAirportEmail, $key),
						'web' => getArrayParameter($echoAirportWeb, $key),
						'longitude' => getArrayParameter($echoAirportLongitude, $key),
						'latitude' => getArrayParameter($echoAirportLatitude, $key),
						'autogmap' => getArrayParameter($echoAirportAutogmap, $key),
						'cityGeoNameId' => $cityGeonameId,
						'countryGeoNameId' => $countryGeonameId,
						'mapTitle' => getArrayParameter($echoAirportMapTitle, $key), 
						'mapCredits' => getArrayParameter($echoAirportMapCredits, $key), 
						'mapFile' => getArrayParameter($echoAirportMapFile, $key) 
						);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoAirport[] = array(
			'title' => '', 
			'description' => '', 
			'address' => '',
			'postalCode' => '',
			'transportation' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => '',
			'mapTitle' => '', 
			'mapCredits' => '', 
			'mapFile' => '' 
			);
		}
		
		if(isset($_REQUEST['echoSlideshow_title'])) 
		{
			$echoSlideshowId 	= getArrayParameter($_REQUEST, 'echoSlideshow_id');
			$echoSlideshowTitle 	= getArrayParameter($_REQUEST, 'echoSlideshow_title');
			$echoSlideshowCredits 	= getArrayParameter($_REQUEST, 'echoSlideshow_credits');
			//$echoSlideshowFile 		= getArrayParameter($_REQUEST, 'echoSlideshow_file');
			$echoSlideshowFile2 	= getArrayParameter($_REQUEST, 'echoSlideshow_file2');
					
			if(is_array($echoSlideshowTitle))
			{
				foreach($echoSlideshowTitle as $key => $title)
				{
					if (!empty($_FILES["echoSlideshow_file"]["tmp_name"][$key]) && move_uploaded_file($_FILES["echoSlideshow_file"]["tmp_name"][$key], $photoPath.DIRECTORY_SEPARATOR.$_FILES["echoSlideshow_file"]["name"][$key]))
						$echoSlideshowFile[$key] = $_FILES["echoSlideshow_file"]["name"][$key];
					else
						$echoSlideshowFile[$key] = $echoSlideshowFile2[$key];
				
					$echoSlideshow[] = array(
					'id' => getArrayParameter($echoSlideshowId, $key), 
					'title' => getArrayParameter($echoSlideshowTitle, $key), 
					'credits' => getArrayParameter($echoSlideshowCredits, $key),
					'file' => getArrayParameter($echoSlideshowFile, $key)
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoSlideshow[] = array(
			'title' => '', 
			'credits' => '',
			'file' => ''
			);
		}
		
		if(isset($_REQUEST['echoHousing_title'])) 
		{
			$echoHousingId 		= getArrayParameter($_REQUEST, 'echoHousing_id');
			$echoHousingTitle 		= getArrayParameter($_REQUEST, 'echoHousing_title');
			$echoHousingKind 		= getArrayParameter($_REQUEST, 'echoHousing_kind');
			$echoHousingCommentDates = getArrayParameter($_REQUEST, 'echoHousing_commentDates');
			$echoHousingCommentPrice = getArrayParameter($_REQUEST, 'echoHousing_commentPrice');
			$echoHousingAddress 	= getArrayParameter($_REQUEST, 'echoHousing_address');
			$echoHousingPostalCode 	= getArrayParameter($_REQUEST, 'echoHousing_postalCode');
			$echoHousingCity 		= getArrayParameter($_REQUEST, 'echoHousing_city');
			$echoHousingCountry 	= getArrayParameter($_REQUEST, 'echoHousing_country');
			$echoHousingPhone 		= getArrayParameter($_REQUEST, 'echoHousing_phone');
			$echoHousingEmail 		= getArrayParameter($_REQUEST, 'echoHousing_email');
			$echoHousingWeb 		= getArrayParameter($_REQUEST, 'echoHousing_web');
			$echoHousingCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoHousing_cityGeoNameId');
			$echoHousingCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoHousing_countryGeoNameId');
			$echoHousingLongitude 	= getArrayParameter($_REQUEST, 'echoHousing_longitude');
			$echoHousingLatitude 	= getArrayParameter($_REQUEST, 'echoHousing_latitude');
			$echoHousingAutogmap 	= getArrayParameter($_REQUEST, 'echoHousing_autogmap');
			
			if(is_array($echoHousingTitle))
			{
				foreach($echoHousingTitle as $key => $title)
				{
					$city = getArrayParameter($echoHousingCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoHousingCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoHousingCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoHousingCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoHousing[] = array(
					'id' => getArrayParameter($echoHousingId, $key), 
					'title' => getArrayParameter($echoHousingTitle, $key), 
					'kind' => getArrayParameter($echoHousingKind, $key), 
					'commentDates' => getArrayParameter($echoHousingCommentDates, $key), 
					'commentPrice' => getArrayParameter($echoHousingCommentPrice, $key), 
					'address' => getArrayParameter($echoHousingAddress, $key),
					'postalCode' => getArrayParameter($echoHousingPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoHousingPhone, $key),
					'email' => getArrayParameter($echoHousingEmail, $key),
					'web' => getArrayParameter($echoHousingWeb, $key),
					'longitude' => getArrayParameter($echoHousingLongitude, $key),
					'latitude' => getArrayParameter($echoHousingLatitude, $key),
					'autogmap' => getArrayParameter($echoHousingAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoHousing[] = array(
			'title' => '', 
			'kind' => '', 
			'commentDates' => '', 
			'commentPrice' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
		
		if(isset($_REQUEST['echoTakeOuts_title'])) 
		{
			$echoTakeOutsId 		= getArrayParameter($_REQUEST, 'echoTakeOuts_id');
			$echoTakeOutsTitle 		= getArrayParameter($_REQUEST, 'echoTakeOuts_title');
			$echoTakeOutsKind 		= getArrayParameter($_REQUEST, 'echoTakeOuts_kind');
			$echoTakeOutsCommentDates = getArrayParameter($_REQUEST, 'echoTakeOuts_commentDates');
			$echoTakeOutsCommentPrice = getArrayParameter($_REQUEST, 'echoTakeOuts_commentPrice');
			$echoTakeOutsAddress 	= getArrayParameter($_REQUEST, 'echoTakeOuts_address');
			$echoTakeOutsPostalCode 	= getArrayParameter($_REQUEST, 'echoTakeOuts_postalCode');
			$echoTakeOutsCity 		= getArrayParameter($_REQUEST, 'echoTakeOuts_city');
			$echoTakeOutsCountry 	= getArrayParameter($_REQUEST, 'echoTakeOuts_country');
			$echoTakeOutsPhone 		= getArrayParameter($_REQUEST, 'echoTakeOuts_phone');
			$echoTakeOutsEmail 		= getArrayParameter($_REQUEST, 'echoTakeOuts_email');
			$echoTakeOutsWeb 		= getArrayParameter($_REQUEST, 'echoTakeOuts_web');
			$echoTakeOutsCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoTakeOuts_cityGeoNameId');
			$echoTakeOutsCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoTakeOuts_countryGeoNameId');
			$echoTakeOutsLongitude 	= getArrayParameter($_REQUEST, 'echoTakeOuts_longitude');
			$echoTakeOutsLatitude 	= getArrayParameter($_REQUEST, 'echoTakeOuts_latitude');
			$echoTakeOutsAutogmap 	= getArrayParameter($_REQUEST, 'echoTakeOuts_autogmap');
			
			if(is_array($echoTakeOutsTitle))
			{
				foreach($echoTakeOutsTitle as $key => $title)
				{
					$city = getArrayParameter($echoTakeOutsCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoTakeOutsCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoTakeOutsCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoTakeOutsCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoTakeOuts[] = array(
					'id' => getArrayParameter($echoTakeOutsId, $key), 
					'title' => getArrayParameter($echoTakeOutsTitle, $key), 
					'kind' => getArrayParameter($echoTakeOutsKind, $key), 
					'commentDates' => getArrayParameter($echoTakeOutsCommentDates, $key), 
					'commentPrice' => getArrayParameter($echoTakeOutsCommentPrice, $key), 
					'address' => getArrayParameter($echoTakeOutsAddress, $key),
					'postalCode' => getArrayParameter($echoTakeOutsPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoTakeOutsPhone, $key),
					'email' => getArrayParameter($echoTakeOutsEmail, $key),
					'web' => getArrayParameter($echoTakeOutsWeb, $key),
					'longitude' => getArrayParameter($echoTakeOutsLongitude, $key),
					'latitude' => getArrayParameter($echoTakeOutsLatitude, $key),
					'autogmap' => getArrayParameter($echoTakeOutsAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoTakeOuts[] = array(
			'title' => '', 
			'kind' => '', 
			'commentDates' => '', 
			'commentPrice' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
		
		if(isset($_REQUEST['echoMustSees_title'])) 
		{
			$echoMustSeesId 		= getArrayParameter($_REQUEST, 'echoMustSees_id');
			$echoMustSeesTitle 		= getArrayParameter($_REQUEST, 'echoMustSees_title');
			$echoMustSeesKind 		= getArrayParameter($_REQUEST, 'echoMustSees_kind');
			$echoMustSeesCommentDates = getArrayParameter($_REQUEST, 'echoMustSees_commentDates');
			$echoMustSeesCommentPrice = getArrayParameter($_REQUEST, 'echoMustSees_commentPrice');
			$echoMustSeesAddress 	= getArrayParameter($_REQUEST, 'echoMustSees_address');
			$echoMustSeesPostalCode 	= getArrayParameter($_REQUEST, 'echoMustSees_postalCode');
			$echoMustSeesCity 		= getArrayParameter($_REQUEST, 'echoMustSees_city');
			$echoMustSeesCountry 	= getArrayParameter($_REQUEST, 'echoMustSees_country');
			$echoMustSeesPhone 		= getArrayParameter($_REQUEST, 'echoMustSees_phone');
			$echoMustSeesEmail 		= getArrayParameter($_REQUEST, 'echoMustSees_email');
			$echoMustSeesWeb 		= getArrayParameter($_REQUEST, 'echoMustSees_web');
			$echoMustSeesCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoMustSees_cityGeoNameId');
			$echoMustSeesCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoMustSees_countryGeoNameId');
			$echoMustSeesLongitude 	= getArrayParameter($_REQUEST, 'echoMustSees_longitude');
			$echoMustSeesLatitude 	= getArrayParameter($_REQUEST, 'echoMustSees_latitude');
			$echoMustSeesAutogmap 	= getArrayParameter($_REQUEST, 'echoMustSees_autogmap');
			
			if(is_array($echoMustSeesTitle))
			{
				foreach($echoMustSeesTitle as $key => $title)
				{
					$city = getArrayParameter($echoMustSeesCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoMustSeesCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoMustSeesCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoMustSeesCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoMustSees[] = array(
					'id' => getArrayParameter($echoMustSeesId, $key), 
					'title' => getArrayParameter($echoMustSeesTitle, $key), 
					'kind' => getArrayParameter($echoMustSeesKind, $key), 
					'commentDates' => getArrayParameter($echoMustSeesCommentDates, $key), 
					'commentPrice' => getArrayParameter($echoMustSeesCommentPrice, $key), 
					'address' => getArrayParameter($echoMustSeesAddress, $key),
					'postalCode' => getArrayParameter($echoMustSeesPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoMustSeesPhone, $key),
					'email' => getArrayParameter($echoMustSeesEmail, $key),
					'web' => getArrayParameter($echoMustSeesWeb, $key),
					'longitude' => getArrayParameter($echoMustSeesLongitude, $key),
					'latitude' => getArrayParameter($echoMustSeesLatitude, $key),
					'autogmap' => getArrayParameter($echoMustSeesAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoMustSees[] = array(
			'title' => '', 
			'kind' => '', 
			'commentDates' => '', 
			'commentPrice' => '', 
			'address' => '',
			'postalCode' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => ''
			);
		}
		
		if(isset($_REQUEST['echoEvent_title'])) 
		{
			$echoEventId 		= getArrayParameter($_REQUEST, 'echoEvent_id');
			$echoEventTitle 		= getArrayParameter($_REQUEST, 'echoEvent_title');
			$echoEventPlaceName 		= getArrayParameter($_REQUEST, 'echoEvent_placeName');
			$echoEventKind 		= getArrayParameter($_REQUEST, 'echoEvent_kind');
			$echoEventStartDate 		= getArrayParameter($_REQUEST, 'echoEvent_startDate');
			$echoEventEndDate 		= getArrayParameter($_REQUEST, 'echoEvent_endDate');
			$echoEventCommentDates 		= getArrayParameter($_REQUEST, 'echoEvent_commentDates');
			$echoEventDescription 		= getArrayParameter($_REQUEST, 'echoEvent_description');
			$echoEventPrice 		= getArrayParameter($_REQUEST, 'echoEvent_price');
			$echoEventCommentPrice 		= getArrayParameter($_REQUEST, 'echoEvent_commentPrice');
			$echoEventAddress 	= getArrayParameter($_REQUEST, 'echoEvent_address');
			$echoEventPostalCode 	= getArrayParameter($_REQUEST, 'echoEvent_postalCode');
			$echoEventCity 		= getArrayParameter($_REQUEST, 'echoEvent_city');
			$echoEventCountry 	= getArrayParameter($_REQUEST, 'echoEvent_country');
			$echoEventPhone 		= getArrayParameter($_REQUEST, 'echoEvent_phone');
			$echoEventEmail 		= getArrayParameter($_REQUEST, 'echoEvent_email');
			$echoEventWeb 		= getArrayParameter($_REQUEST, 'echoEvent_web');
			$echoEventCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoEvent_cityGeoNameId');
			$echoEventCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoEvent_countryGeoNameId');
			$echoEventLinkTitle 		= getArrayParameter($_REQUEST, 'echoEvent_linkTitle');
			$echoEventLinkCredits 		= getArrayParameter($_REQUEST, 'echoEvent_linkCredits');
			//$echoEventLinkFile 		= getArrayParameter($_REQUEST, 'echoEvent_linkFile');
			$echoEventLinkFile2 		= getArrayParameter($_REQUEST, 'echoEvent_linkFile2');
			$echoEventLongitude 		= getArrayParameter($_REQUEST, 'echoEvent_longitude');
			$echoEventLatitude 			= getArrayParameter($_REQUEST, 'echoEvent_latitude');
			$echoEventAutogmap 	= getArrayParameter($_REQUEST, 'echoEvent_autogmap');
			
			if(is_array($echoEventTitle))
			{
				foreach($echoEventTitle as $key => $title)
				{
					if (!empty($_FILES["echoEvent_linkFile"]["tmp_name"][$key]) && move_uploaded_file($_FILES["echoEvent_linkFile"]["tmp_name"][$key], $photoPath.DIRECTORY_SEPARATOR.$_FILES["echoEvent_linkFile"]["name"][$key]))
						$echoEventLinkFile[$key] = $_FILES["echoEvent_linkFile"]["name"][$key];
					else
						$echoEventLinkFile[$key] = $echoEventLinkFile2[$key];

					$city = getArrayParameter($echoEventCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoEventCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoEventCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoEventCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoEvent[] = array(
					'id' => getArrayParameter($echoEventId, $key), 
					'title' => getArrayParameter($echoEventTitle, $key), 
					'placeName' => getArrayParameter($echoEventPlaceName, $key), 
					'kind' => getArrayParameter($echoEventKind, $key), 
					'startDate' => getArrayParameter($echoEventStartDate, $key), 
					'endDate' => getArrayParameter($echoEventEndDate, $key), 
					'commentDates' => getArrayParameter($echoEventCommentDates, $key), 
					'description' => getArrayParameter($echoEventDescription, $key), 
					'price' => getArrayParameter($echoEventPrice, $key), 
					'commentPrice' => getArrayParameter($echoEventCommentPrice, $key), 
					'address' => getArrayParameter($echoEventAddress, $key),
					'postalCode' => getArrayParameter($echoEventPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoEventPhone, $key),
					'email' => getArrayParameter($echoEventEmail, $key),
					'web' => getArrayParameter($echoEventWeb, $key),
					'longitude' => getArrayParameter($echoEventLongitude, $key),
					'latitude' => getArrayParameter($echoEventLatitude, $key),
					'autogmap' => getArrayParameter($echoEventAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId,
					'linkTitle' => getArrayParameter($echoEventLinkTitle, $key), 
					'linkCredits' => getArrayParameter($echoEventLinkCredits, $key), 
					'linkFile' => getArrayParameter($echoEventLinkFile, $key) 
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoEvent[] = array(
			'title' => '', 
			'placeName' => '', 
			'kind' => '', 
			'startDate' => '', 
			'endDate' => '', 
			'commentDates' => '', 
			'description' => '', 
			'price' => '', 
			'commentPrice' => '', 
			'address' => '',
			'postalCode' => '',
			'transportation' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => '',
			'linkTitle' => '', 
			'linkCredits' => '', 
			'linkFile' => '' 
			);
		}

		if(isset($_REQUEST['echoStation_title'])) 
		{
			$echoStationId 		= getArrayParameter($_REQUEST, 'echoStation_id');
			$echoStationTitle 		= getArrayParameter($_REQUEST, 'echoStation_title');
			$echoStationDescription 		= getArrayParameter($_REQUEST, 'echoStation_description');
			$echoStationAddress 	= getArrayParameter($_REQUEST, 'echoStation_address');
			$echoStationPostalCode 	= getArrayParameter($_REQUEST, 'echoStation_postalCode');
			$echoStationMapTransportation 	= getArrayParameter($_REQUEST, 'echoStation_transportation');
			$echoStationCity 		= getArrayParameter($_REQUEST, 'echoStation_city');
			$echoStationCountry 	= getArrayParameter($_REQUEST, 'echoStation_country');
			$echoStationPhone 		= getArrayParameter($_REQUEST, 'echoStation_phone');
			$echoStationEmail 		= getArrayParameter($_REQUEST, 'echoStation_email');
			$echoStationWeb 		= getArrayParameter($_REQUEST, 'echoStation_web');
			$echoStationCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoStation_cityGeoNameId');
			$echoStationCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoStation_countryGeoNameId');
			$echoStationMapTitle 		= getArrayParameter($_REQUEST, 'echoStation_mapTitle');
			$echoStationMapCredits 		= getArrayParameter($_REQUEST, 'echoStation_mapCredits');
			//$echoStationMapFile 		= getArrayParameter($_REQUEST, 'echoStation_mapFile');
			$echoStationMapFile2 	= getArrayParameter($_REQUEST, 'echoStation_mapFile2');
			$echoStationLongitude 	= getArrayParameter($_REQUEST, 'echoStation_longitude');
			$echoStationLatitude 	= getArrayParameter($_REQUEST, 'echoStation_latitude');
			$echoStationAutogmap 	= getArrayParameter($_REQUEST, 'echoStation_autogmap');
			
			if(is_array($echoStationTitle))
			{
				foreach($echoStationTitle as $key => $title)
				{						
					if (!empty($_FILES["echoStation_mapFile"]["tmp_name"][$key]) && move_uploaded_file($_FILES["echoStation_mapFile"]["tmp_name"][$key], $photoPath.DIRECTORY_SEPARATOR.$_FILES["echoStation_mapFile"]["name"][$key]))
						$echoStationMapFile[$key] = $_FILES["echoStation_mapFile"]["name"][$key];
					else
						$echoStationMapFile[$key] = $echoStationMapFile2[$key];
					
					$city = getArrayParameter($echoStationCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoStationCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoStationCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoStationCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoStation[] = array(
						'id' => getArrayParameter($echoStationId, $key), 
						'title' => getArrayParameter($echoStationTitle, $key), 
						'description' => getArrayParameter($echoStationDescription, $key), 
						'address' => getArrayParameter($echoStationAddress, $key),
						'postalCode' => getArrayParameter($echoStationPostalCode, $key),
						'transportation' => getArrayParameter($echoStationMapTransportation, $key),
						'city' => $city,
						'country' => $country,
						'phone' => getArrayParameter($echoStationPhone, $key),
						'email' => getArrayParameter($echoStationEmail, $key),
						'web' => getArrayParameter($echoStationWeb, $key),
						'longitude' => getArrayParameter($echoStationLongitude, $key),
						'latitude' => getArrayParameter($echoStationLatitude, $key),
						'autogmap' => getArrayParameter($echoStationAutogmap, $key),
						'cityGeoNameId' => $cityGeonameId,
						'countryGeoNameId' => $countryGeonameId,
						'mapTitle' => getArrayParameter($echoStationMapTitle, $key), 
						'mapCredits' => getArrayParameter($echoStationMapCredits, $key), 
						'mapFile' => getArrayParameter($echoStationMapFile, $key) 
						);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoStation[] = array(
			'title' => '', 
			'description' => '', 
			'address' => '',
			'postalCode' => '',
			'transportation' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => '',
			'mapTitle' => '', 
			'mapCredits' => '', 
			'mapFile' => '' 
			);
		}
		
		if(isset($_REQUEST['echoShow_title'])) 
		{
			$echoShowId 		= getArrayParameter($_REQUEST, 'echoShow_id');
			$echoShowTitle 		= getArrayParameter($_REQUEST, 'echoShow_title');
			$echoShowPlaceName 		= getArrayParameter($_REQUEST, 'echoShow_placeName');
			$echoShowKind 		= getArrayParameter($_REQUEST, 'echoShow_kind');
			$echoShowStartDate 		= getArrayParameter($_REQUEST, 'echoShow_startDate');
			$echoShowEndDate 		= getArrayParameter($_REQUEST, 'echoShow_endDate');
			$echoShowCommentDates 		= getArrayParameter($_REQUEST, 'echoShow_commentDates');
			$echoShowDescription 		= getArrayParameter($_REQUEST, 'echoShow_description');
			$echoShowPrice 		= getArrayParameter($_REQUEST, 'echoShow_price');
			$echoShowCommentPrice 		= getArrayParameter($_REQUEST, 'echoShow_commentPrice');
			$echoShowAddress 	= getArrayParameter($_REQUEST, 'echoShow_address');
			$echoShowPostalCode 	= getArrayParameter($_REQUEST, 'echoShow_postalCode');
			$echoShowCity 		= getArrayParameter($_REQUEST, 'echoShow_city');
			$echoShowCountry 	= getArrayParameter($_REQUEST, 'echoShow_country');
			$echoShowPhone 		= getArrayParameter($_REQUEST, 'echoShow_phone');
			$echoShowEmail 		= getArrayParameter($_REQUEST, 'echoShow_email');
			$echoShowWeb 		= getArrayParameter($_REQUEST, 'echoShow_web');
			$echoShowCityGeoNameId 		= getArrayParameter($_REQUEST, 'echoShow_cityGeoNameId');
			$echoShowCountryGeoNameId 	= getArrayParameter($_REQUEST, 'echoShow_countryGeoNameId');
			$echoShowLinkTitle 		= getArrayParameter($_REQUEST, 'echoShow_linkTitle');
			$echoShowLinkCredits 		= getArrayParameter($_REQUEST, 'echoShow_linkCredits');
			//$echoShowLinkFile 		= getArrayParameter($_REQUEST, 'echoShow_linkFile');
			$echoShowLinkFile2 		= getArrayParameter($_REQUEST, 'echoShow_linkFile2');
			$echoShowLongitude 		= getArrayParameter($_REQUEST, 'echoShow_longitude');
			$echoShowLatitude 			= getArrayParameter($_REQUEST, 'echoShow_latitude');
			$echoShowAutogmap 	= getArrayParameter($_REQUEST, 'echoShow_autogmap');
			
			if(is_array($echoShowTitle))
			{
				foreach($echoShowTitle as $key => $title)
				{
					if (!empty($_FILES["echoShow_linkFile"]["tmp_name"][$key]) && move_uploaded_file($_FILES["echoShow_linkFile"]["tmp_name"][$key], $photoPath.DIRECTORY_SEPARATOR.$_FILES["echoShow_linkFile"]["name"][$key]))
						$echoShowLinkFile[$key] = $_FILES["echoShow_linkFile"]["name"][$key];
					else
						$echoShowLinkFile[$key] = $echoShowLinkFile2[$key];

					$city = getArrayParameter($echoShowCity, $key);
					if (empty($city)) $city = $mainCity;				
					$country = getArrayParameter($echoShowCountry, $key);
					if (empty($country)) $country = $mainCountry;
					$cityGeonameId = getArrayParameter($echoShowCityGeoNameId, $key);
					if (empty($cityGeonameId)) $cityGeonameId = $maincityGeonameId;				
					$countryGeonameId = getArrayParameter($echoShowCountryGeoNameId, $key);
					if (empty($countryGeonameId)) $countryGeonameId = $mainCountryGeonameId;				
					
					$echoShow[] = array(
					'id' => getArrayParameter($echoShowId, $key), 
					'title' => getArrayParameter($echoShowTitle, $key), 
					'placeName' => getArrayParameter($echoShowPlaceName, $key), 
					'kind' => getArrayParameter($echoShowKind, $key), 
					'startDate' => getArrayParameter($echoShowStartDate, $key), 
					'endDate' => getArrayParameter($echoShowEndDate, $key), 
					'commentDates' => getArrayParameter($echoShowCommentDates, $key), 
					'description' => getArrayParameter($echoShowDescription, $key), 
					'price' => getArrayParameter($echoShowPrice, $key), 
					'commentPrice' => getArrayParameter($echoShowCommentPrice, $key), 
					'address' => getArrayParameter($echoShowAddress, $key),
					'postalCode' => getArrayParameter($echoShowPostalCode, $key),
					'city' => $city,
					'country' => $country,
					'phone' => getArrayParameter($echoShowPhone, $key),
					'email' => getArrayParameter($echoShowEmail, $key),
					'web' => getArrayParameter($echoShowWeb, $key),
					'longitude' => getArrayParameter($echoShowLongitude, $key),
					'latitude' => getArrayParameter($echoShowLatitude, $key),
					'autogmap' => getArrayParameter($echoShowAutogmap, $key),
					'cityGeoNameId' => $cityGeonameId,
					'countryGeoNameId' => $countryGeonameId,
					'linkTitle' => getArrayParameter($echoShowLinkTitle, $key), 
					'linkCredits' => getArrayParameter($echoShowLinkCredits, $key), 
					'linkFile' => getArrayParameter($echoShowLinkFile, $key) 
					);
				}
			}
		}
		else if (!$this->context->checkExistence())
		{
			$echoShow[] = array(
			'title' => '', 
			'placeName' => '', 
			'kind' => '', 
			'startDate' => '', 
			'endDate' => '', 
			'commentDates' => '', 
			'description' => '', 
			'price' => '', 
			'commentPrice' => '', 
			'address' => '',
			'postalCode' => '',
			'transportation' => '',
			'city' => '',
			'country' => '',
			'phone' => '',
			'email' => '',
			'web' => '',
			'longitude' => '',
			'latitude' => '',
			'autogmap' => '',
			'cityGeoNameId' => '',
			'countryGeoNameId' => '',
			'linkTitle' => '', 
			'linkCredits' => '', 
			'linkFile' => '' 
			);
		}
		
		$this->context->updateEchoEmbassy($echoEmbassy);
    	$this->context->updateEchoHospital($echoHospital);
    	$this->context->updateEchoPolice($echoPolice);
    	$this->context->updateEchoAirport($echoAirport);
    	$this->context->updateEchoSlideshow($echoSlideshow);
    	$this->context->updateEchoHousing($echoHousing);
    	$this->context->updateEchoTakeOuts($echoTakeOuts);
    	$this->context->updateEchoMustSees($echoMustSees);
    	$this->context->updateEchoEvent($echoEvent);
    	$this->context->updateEchoStation($echoStation);
    	$this->context->updateEchoShow($echoShow);
    }
    
    /**
     * beforeSaving is called on checkin and on save before the store
     *
     * @param wcmSession $session Current session
     * @param wcmProject $project Current project
     */
    protected function beforeSaving($session, $project)
    {
        parent::beforeSaving($session, $project);

        $this->saveZones();
        $this->saveInserts();
    }
}
