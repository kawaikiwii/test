<script type="text/javascript">

quickSearch = function(query, filter)
{
	new Ajax.Updater($('relation_resultset'), wcmBaseURL+'ajax/controller.php', {
						parameters: {
							ajaxHandler: 'search/quickSearch',
							qs_xsl: 'relationSearch',
							qs_query: query,
							qs_filter: filter,
							qs_uid: 'relations',
							qs_simpleMode: '1',
						},
						onComplete: linksManager.initDraggables.bind(linksManager)
					});
}


</script>
