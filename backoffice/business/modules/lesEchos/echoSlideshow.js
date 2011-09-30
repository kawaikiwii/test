<script type="text/javascript">


wcmCheckProperties = function() 
{	
	for(var i= 0; i < document.getElementsByName("echoSlideshow_file[]").length; i++)
	{
		if (document.getElementsByName("echoSlideshow_file[]")[i].value != "")
		{
			if (document.getElementsByName("echoSlideshow_title[]")[i].value == "")
			{
				document.getElementsByName("echoSlideshow_title[]")[i].up('li').addClassName('error');
				 return $I18N.MISSING;
			}
			
			if (document.getElementsByName("echoSlideshow_credits[]")[i].value == "")
			{
				document.getElementsByName("echoSlideshow_credits[]")[i].up('li').addClassName('error');
				 return $I18N.MISSING;
			}
			return null;
		}	
	}
	return null;
}
wcmActionController.registerCallback('save', wcmCheckProperties);
wcmActionController.registerCallback('checkin', wcmCheckProperties);


addEchoSlideshow = function(url, idTransport, obj)
{
    var ajax = new Ajax.Request(url, {
                onSuccess: function(transport) {
                    if (idTransport)
                    {
                        $(idTransport).insert({bottom: transport.responseText});
                        document.location.href='#newpagebutton';
                    }
                    else
                    {
                        $(obj).up('div.collapsable').insert({after: transport.responseText});
                    }
                }
            });
}

removeEchoSlideshow = function(button, id)
{
	if($$('#echoSlideshow .collapsable').length > 1)
	{
		$(button).up('.collapsable').remove();
	}
	else
	{
		wcmMessage.warning('Impossible', 1500);
	}
}


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
