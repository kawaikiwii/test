<script type="text/javascript">

getData = function()
{
	var data ="";
	var e=document.getElementsByName("chapter_text[]");
	for(var i=0;i<e.length;i++)
	{
		data += tinyMCE.get(e[i].id).getContent({format : 'text'}) + "\n";
	}
	
	return data;
}

</script>