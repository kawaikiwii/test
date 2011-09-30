<?php
$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false)
	echo "pas ok erreur de creation de socket";
else{
	$res = @socket_connect($socket, "10.23.65.151", 40000);
	if ($res === false){
		echo "Erreur de connection à la socket ".socket_strerror(socket_last_error());
        }else{
		echo "connectuin ok";
	}
}
	
/*


if($test = fsockopen("smtp.bcstechno.com", 25, $errno, $errstr, 5))
	echo "OK";
else
	echo "Pas OK";*/
?>
