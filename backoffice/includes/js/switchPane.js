function switchPane(target)
{
	if (target == 'left')
	{
		alert('ok');
		document.getElementById('switchPaneManager').className = "search switchPane large";
		//document.getElementById('switchPaneRight').className = "switchPane small";
	}
	else if (target == 'regular')
	{
		document.getElementById('switchPaneLeft').className = "switchPane regular";
		document.getElementById('switchPaneRight').className = "switchPane regular";
	}
	else if (target == 'right')
	{
		document.getElementById('switchPaneLeft').className = "switchPane small";
		document.getElementById('switchPaneRight').className = "switchPane large";
	}
}