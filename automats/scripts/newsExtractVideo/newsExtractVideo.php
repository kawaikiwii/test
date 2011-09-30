<?php
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$project = wcmProject::getInstance();

ini_set("max_execution_time","200");
$temps_debut = microtime(true);

echo "Starting news embeded videos extraction\n\r";


$enum = new news();

if (!$enum->beginEnum("publicationDate  >= '2011-01-01 00:00:01' AND embedVideo IS NOT NULL AND sourceVersion IS NULL", "publicationDate DESC LIMIT 0,30"))
	return null;

$i = 1;
while ($enum->nextEnum())
{
	// on récupère le content de la news
	$contents = $enum->getContents();

	$video = new video();

	// on teste si une vidéo n'existe pas déjà avec l'id de la news
	$testVideoId = $video->refreshBySourceId($enum->id);

	if (empty($testVideoId->id))
	{
		$video->title = $enum->title;
		$video->siteId = $enum->siteId;
		$video->sourceId = $enum->id;

		$videoEmbed = $enum->embedVideo;
		// on remplace les valeurs de hauteur et largeur par des valeurs par défaut
		$videoEmbed = preg_replace('~((?:width)\s?[=:]\s?[\'"]?)[0-9]+~i','${1}450', $videoEmbed);
		$videoEmbed = preg_replace('~((?:height)\s?[=:]\s?[\'"]?)[0-9]+~i','${1}360', $videoEmbed);
		$video->embed = $videoEmbed;

		$video->workflowState = $enum->workflowState;
		$video->publicationDate = $enum->publicationDate;
		$video->channelId = $enum->channelId;
		$video->channelIds = $enum->channelIds;

		if ($video->save())
		{
			echo "[".$i."] id : ".$video->id." / title : ".$video->title." (news : ".$enum->id.") saved ! <br/>";

			//init contenu de la vidéo
			$content = new content();
			$content->referentId = $video->id;
			$content->referentClass = $video->getClass();
			$content->provider = "";
			$content->title = $video->title;

			if (isset($contents) && is_array($contents))
				{
					foreach ($contents as $contentItem)
					{
						 $content->description = $contentItem->description;
						 $content->text = $contentItem->text;
					}
				}

			if ($content->save())
				echo "id : ".$video->id." content saved !\n\r";
			else
				echo "id : ".$video->id."  content error !\n\r";

			// on parcourt les bizrelations de type photo de la news pour les associer à la vidéo
			foreach ($enum->getPhotos() as $photo)
			{
				$bizRelation = new bizrelation();
				$bizRelation->sourceClass = "video";
				$bizRelation->sourceId = $video->id;
				$bizRelation->kind = bizrelation::IS_COMPOSED_OF;
				$bizRelation->destinationClass = "photo";
				$bizRelation->destinationId = $photo['destinationId'];
				$bizRelation->title = $photo['title'];
				$bizRelation->rank = $bizRelation->getLastPosition() + 1;
				$bizRelation->addBizrelation();
				echo "photo id : ".$photo['destinationId']."  bizrelation  created !\n\r";
			}

			// création d'une biz_relation entre la vidéo et la news d'origine
			/*
			$bizRelation = new bizrelation();
			$bizRelation->sourceClass = "news";
			$bizRelation->sourceId = $enum->id;
			$bizRelation->kind = bizrelation::IS_RELATED_TO;
			$bizRelation->destinationClass = "video";
			$bizRelation->destinationId = $video->id;
			$bizRelation->title = $enum->title;
			$bizRelation->header = ucfirst($enum->getClass());

			$bizRelation->rank = $bizRelation->getLastPosition() + 1;
			$bizRelation->addBizrelation();
			echo "id : ".$video->id."  bizrelation created !\n\r";
			*/

			$video->save();
			//$video->generate(false);

			//$enum->sourceVersion="extract";
			//$enum->save();
			$connector = $project->datalayer->getConnectorByReference("biz");
			$businessDB = $connector->getBusinessDatabase();

			$sql = "UPDATE biz_news SET sourceVersion='extract' WHERE id=?";
			$params = array($enum->id);
			$businessDB->executeStatement($sql, $params);

			$i++;
		}
		else
			echo "news title : ".$video->title." video creation error (news : ".$enum->id.") ! \n\r";

	}
	else
		echo "existing video : ".$testVideoId->id." (news : ".$enum->id.") ! \n\r";
}

$temps_fin = microtime(true);
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut . "         \n\n\r", 4);
