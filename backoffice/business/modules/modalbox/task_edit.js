<script type="text/javascript">
computePlanning = function()
{
	$('planning').value = $('minutes').value + '#' + $('hour').value + '#' + $('day').value + '#' + $('month').value + '#' + $('dayOfWeek').value
}

checkField = function(fieldName)
{
	var msg = '';
	$('errorMsg').innerHTML='';
	var recherche = /[0-9\,\-*]/;
	var resultat = recherche.test($(fieldName).value);
	if(resultat == false)
		msg = '<div style="width:100%; background-color:red; text-align:center; color: #FFFFFF; font-weight: bold">Uniquement des chiffres, tirets, virgules ou étoile</div>';
	$('errorMsg').innerHTML=msg;
}

checkDayOfWeek = function(fieldName)
{
	$('errorMsg').innerHTML='';
	var recherche = /[0-6*]/;
	var resultat = recherche.test($(fieldName).value);
	var msg = '';
	if(resultat == false)
		msg = '<div style="width:100%; background-color:red; text-align:center; color: #FFFFFF; font-weight: bold">Uniquement des chiffres de 0 (lundi) à 6 (dimanche) ou étoile</div>';
	$('errorMsg').innerHTML=msg;
}
</script>
