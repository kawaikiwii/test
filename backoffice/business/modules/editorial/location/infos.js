<script type="text/javascript">
updateCountry = function(intId, strName, strCountryCode, autocompleteURL){
	document.getElementById("countryId").value = intId;
};

updateCity = function(intId, strName){
	document.getElementById("cityId").value=intId;
};

cityUpdate = function(countryCode)
{
	openmodal('Select City','500'); 
	modalPopup('cityUpdate', countryCode);
}

</script>
