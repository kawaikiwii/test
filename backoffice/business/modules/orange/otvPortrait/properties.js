<script type="text/javascript">
addPage = function(url, id, obj)
{
    var ajax = new Ajax.Request(url, {
                onSuccess: function(transport) {
                    if (id)
                    {
                        $(id).insert({bottom: transport.responseText});
                        document.location.href='#newpagebutton';
                    }
                    else
                    {
                        $(obj).up('div.collapsable').insert({after: transport.responseText});
                    }
                }
            });
}

removeChapter = function(button, id)
{
	if($$('#pages .collapsable').length > 1)
	{
		tinyMCE.execCommand('mceRemoveControl', null, id);
		$(button).up('.collapsable').remove();
	}
	else
	{
		wcmMessage.warning($I18N.CANNOTREMOVECHAPTER, 1500);
	}
}

updatePhotosList = function()
{
	$('photosList').innerHTML = "<div class='wait' style='display:inline;'>Loading...</div>";
	wcmBizAjaxController.call("biz.updatePhotoListOTV", {
			command: 'updatePhotoList',
			folder: $('pathPhoto').value,
	}, null, null);

}

selectedPhoto = function(photo)
{
	var id = $('uniq_identifiant').value;
	$('selectedPicture_' + id).src = photo;
	$('chapter_photo_' + id).value = photo;
	closemodal(); 
	return false;
}

removePicture = function(id)
{
	$('selectedPicture_' + id).src = 'img/none.gif';
	$('chapter_photo_' + id).value = '';
}

</script>
