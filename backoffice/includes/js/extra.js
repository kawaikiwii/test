
function addToTextarea(textAreaId, textId) 
{
	if (document.getElementById(textAreaId))
	{
		if (text != "")
		{
			var val = document.getElementById(textAreaId).value;	 
			var text = document.getElementById(textId).value;	 
			val = val + "<p>" + text + "</p>";
			tinyMCE.get(textAreaId).setContent(val);
			alert('text has been copied');
		}
	}
	else
		alert('cannot copy, please click on tab content in order to load data');
}
