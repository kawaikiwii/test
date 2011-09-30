<?php

include('initApi.php');
if ($chan = new channel(null, '151'))
{ 
	$chan->save();
	echo "ALL RIGHT !";
}
else
{
	echo "IL FAUT TROUVER UN ID  DE CHANNEL EXISTANT !";
}
//print_r($chan);

?>
