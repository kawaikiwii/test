<content sRef="relaxnews">
	<contentMeta/>
	<contentSets>
	<?php
		foreach ($this->getContents() as $content) {?>
		<contentSet variant="<?php echo($content->format); ?>" enabled="true">
			<title size="<?php echo($content->titleSigns); ?>" words="<?php echo($content->titleWords); ?>" contentType="<?php echo($content->titleContentType); ?>">
				<?php echo($content->title); ?>
			</title>
			<description size="<?php echo($content->descriptionSigns); ?>" words="<?php echo($content->descriptionWords); ?>" contentType="<?php echo($content->descriptionContentType); ?>">
				<![CDATA[<?php echo($content->description); ?>]]>
			</description>
			<text size="<?php echo($content->textSigns); ?>" words="<?php echo($content->textWords); ?>" contentType="<?php echo($content->textContentType); ?>">
				<![CDATA[<?php echo($content->text); ?> !>]]>
			</text>
		</contentSet>
	<?php } ?>
	</contentSets>
</content>
