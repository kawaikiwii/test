<links>
	<?php
		foreach ($this->getRelateds() as $related) {?>
	<link rel="<?php echo($related['relation']['kind']);?>" class="<?php echo($related['relation']['destinationClass']);?>" id="<?php echo($related['relation']['destinationId']);?>" sRef="relaxnews">
		<renditions>
			<?php
			if (isset($related["object"]->formats) && $related["object"]->formats) {
				$formats = (is_array($related["object"]->formats)) ? $related["object"]->formats : unserialize($related["object"]->formats);
				foreach ($formats as $key => $value) {?>
			<rendition kind="<?php echo($key) ?>" href="<?php echo(str_ireplace ('.original.', '.'.$key.'.', $related['object']->original));?>" width="<?php echo($formats[$key]['width']);?>" height="<?php echo($formats[$key]['height']);?>" size="<?php echo($formats[$key]['weight']);?>" resolution="" />
			<?php } } ?>
		</renditions>
		<credits><?php echo($related['object']->credits);?></credits>
		
		<title><?php echo($related['relation']['title']);?></title>
		<description><![CDATA[<?php echo($related['relation']['media_description']);?>]]></description>
		<text><![CDATA[<?php echo($related['relation']['media_text']);?>]]></text>
	</link>	
	 <?php } ?>
</links>
