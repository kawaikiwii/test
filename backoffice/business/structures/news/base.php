<?php
	$initiatedAt = split(" ", $this->createdAt);
	$revisedAt = split(" ", $this->modifiedAt);
	$embargoedAt = split(" ", $this->embargoDate);
	if (isset($this->publicationDate) && $this->publicationDate)
		$publishedAt = split(" ", $this->publicationDate);
	$expiredAt = split(" ", $this->expirationDate);
?>
<news id="<?php echo ($this->id);?>" cid="<?php echo ($this->cId);?>" siteId="<?php echo ($this->siteId);?>"  status="<?php echo ($this->workflowState);?>" class="<?php echo ($this->getClass());?>">
	<itemMeta>
		<managment>
			<source id="<?php echo($this->source); ?>">
				<location><?php echo($this->sourceLocation); ?></location>
				<name><?php echo($this->getListLabelFromId($this->source)); ?></name>
			</source>
			<initiated 	iso8601="<?php echo (dateToISO8601($this->createdAt));?>" date="<?php echo ($initiatedAt[0]);?>" time="<?php echo ($initiatedAt[1]);?>" by="<?php echo ($this->createdBy);?>"/>
			<revised 	iso8601="<?php echo (dateToISO8601($this->modifiedAt));?>" date="<?php echo ($revisedAt[0]);?>" time="<?php echo ($revisedAt[1]);?>" by="<?php echo ($this->modifiedBy);?>"/>
			<published	iso8601="<?php echo (dateToISO8601($this->publicationDate));?>" date="<?php echo ($publishedAt[0]);?>" time="<?php echo ($publishedAt[1]);?>"/>
			<embargoed	iso8601="<?php echo (dateToISO8601($this->embargoDate));?>" date="<?php echo ($embargoedAt[0]);?>" time="<?php echo ($embargoedAt[1]);?>"/>
			<expired	iso8601="<?php echo (dateToISO8601($this->expirationDate));?>" date="<?php echo ($expiredAt[0]);?>" time="<?php echo ($expiredAt[1]);?>"/>
		</managment>
		
		<classifications>
			<folders>
			<?php
				if (isset($this->folderIds) && $this->folderIds) {
				$folderIds = (is_array($this->folderIds)) ? $this->folderIds : unserialize($this->folderIds);
				foreach($folderIds as $folderId) {
					$oFolder = new channel(null, $folderId); 
					$sTitle = $oFolder->title; ?>
				<folder id="<?php echo($oFolder->id);?>">
					<path><?php echo($oFolder->getChannelPath()); ?></path>
					<label><?php echo ($sTitle) ?></label>
				</folder>
			<?php }	} ?>
			</folders>
			<rubrics>
			<?php
				if (isset($this->channelIds) && $this->channelIds) {
					$channelIds = (is_array($this->channelIds)) ? $this->channelIds : unserialize($this->channelIds);
					foreach($channelIds as $channelId) {
						$oChannel = new channel(null, $channelId);
						$bMainRubric = ($oChannel->id == $this->channelId) ? "true" : "false"; 
						$sTitle = $oChannel->title; ?>
				<rubric id="<?php echo($oChannel->id);?>" main="<?php echo($bMainRubric); ?>">
					<path><?php echo($oChannel->getChannelPath()); ?></path>
					<label><?php echo ($sTitle) ?></label>
				</rubric>
			<?php }	} ?>
			</rubrics>
			<themas>
			<?php
				if (isset($this->listIds) && $this->listIds) {
					$listIds = (is_array($this->listIds)) ? $this->listIds : unserialize($this->listIds);
					foreach($listIds as $listId) {
						$aList = wcmList::getFinalContent($listId, 1, 'path'); ?>
				<thema id="<?php echo($listId);?>">
					<path><?php echo(key($aList)); ?></path>
					<label><?php echo ($aList[key($aList)]) ?></label>
				</thema>
			<?php }	}?>		
			</themas>
		</classifications>
	</itemMeta>
	<?php 
		$semanticData = $this->propertyToXML("semanticData", $this->semanticData) ; //(is_array($this->semanticData)) ? $this->semanticData : unserialize($this->semanticData);
		echo ($semanticData);
	?>
	<related>
		<?php
			$xml = $this->getXmlContents (WCM_DIR . "/business/structures/links/base.php");
			echo($xml);
		?>
		<conceptRefs/>
	</related>

	<?php
		$xml = $this->getXmlContents (WCM_DIR . "/business/structures/content/base.php");
		echo($xml);
	?>
</news>










