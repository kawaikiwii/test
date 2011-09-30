<script type="text/javascript">


wcmCheckProperties = function() 
{
    if ($F('mapCityFile'))
    {
        if (!$F('mapCityCaption'))
        {
            $('mapCityCaption').up('li').addClassName('error');
            return $I18N.MISSING;
        }
        
        if (!$F('mapCityCredits'))
        {
            $('mapCityCredits').up('li').addClassName('error');
            return $I18N.MISSING;
        }
       
        return null;
    }
    else
        return null;
}
wcmActionController.registerCallback('save', wcmCheckProperties);
wcmActionController.registerCallback('checkin', wcmCheckProperties);


updateFileLesEchosList = function()
{
	$('photosList').innerHTML = "<div class='wait' style='display:inline;'>Loading...</div>";
	wcmBizAjaxController.call("biz.updateFileListLesEchos", {
			command: 'updateFileLesEchosList',
			folder: $('pathPhoto').value,
	}, null, null);

}

selectedPhoto = function(photo)
{
	var id = $('uniq_identifiant').value;
	$('selectedPicture_' + id).src = photo;
	$('photo_' + id).value = photo;
	closemodal(); 
	return false;
}

removePicture = function(id)
{
	$('selectedPicture_' + id).src = 'img/none.gif';
	$('chapter_photo_' + id).value = '';
}

</script>
