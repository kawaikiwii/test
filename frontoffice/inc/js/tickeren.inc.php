<div id="ticker">
	<span id="titre">(AFP-RELAXNEWS) - </span>
	<span id="tickernews" style="overflow:hidden;">
	<?php // lecture du flux RSS
		require_once("simplepie.class.php");
		// on initialise
		$feed = new SimplePie();
		$feed->enable_cache(false);
		$feed->set_feed_url("http://feeds.relaxnews.net/relaxnews_d1247c6fc10cfb4e2cffa1688bc284fb/signatures/afprelaxnewsEN.xml");
		$feed->init();
		$feed->handle_content_type();
		// pour chaque entrée
		$i=1;
		foreach($feed->get_items() as $k => $item) {
			// infos sur l'entrée
			$title = $item->get_title();
			echo '<a href="#">'.$title.'</a>';
			$i++;
		}
	?>
	</span>
</div>
