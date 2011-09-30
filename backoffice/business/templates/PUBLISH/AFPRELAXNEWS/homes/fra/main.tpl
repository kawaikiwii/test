{capture name="MAIN"}
		<div id="home">
				<div class="right">
					<div class="ari-section slideshow">
{assign var="slideshowURL" value="`$config.wcm.webSite.urlRepository``$SLIDESHOW->permalinks`"}
{assign var="slideshowURL" value=$slideshowURL|replace:'%format%.html':'detail.xml'}
						<div class="ari-section-title mid"><span class="ari-more"><a href="#" onclick=" ARe.search.type('Slide shows', 'slideshow')" title="Browse news">more</a></span><span class="ari-title">RELAX DIAPORAMAS</span></div>
						<div class="ari-content">
							<div class="ari-item focused">
								<h3 class="ari-title ari-<?php echo $css ?>"><a href="#" onclick="ARe.pview('slideshow', {$SLIDESHOW->id});">{$SLIDESHOW->title}</a></h3>
								<div class="ari-animation">
									<object name="ars-slideshow_{$SLIDESHOW->id}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" id="ars-slideshow_{$SLIDESHOW->id}" height="290" width="290">
										<param name="movie" value="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$slideshowURL}"/>
										<param name="quality" value="high" />
										<param name="wmode" value="transparent" />
										<embed src="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$slideshowURL}" height="290" width="290" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="ars-slideshow_{$SLIDESHOW->id}" swliveconnect="true" wmode="transparent"></embed>
									</object>
								</div>
							</div>
						</div>
					</div>
{assign var="videoEmbed" value="`$VIDEO->embed`"}
{assign var="videoEmbed" value=$videoEmbed|replace:'.flv':'.flv&backcolor=64247C&frontcolor=FFFFFF&lightcolor=64247C&screencolor=64247C'}
{assign var="videoEmbed" value=$videoEmbed|replace:'&autostart=true':'&autostart=false'}
{assign var="videoEmbed" value=$videoEmbed|replace:'height=':'height="216" oldH='}
{assign var="videoEmbed" value=$videoEmbed|replace:'width=':'width="278" oldW='}	
{assign var="videoEmbed" value=$videoEmbed|replace:'http://video.relaxnews.com/uploads/thumbs/':'http://video.relaxnews.com/uploads/video_afp.jpg'}
					<div class="ari-section video">
						<div class="ari-section-title video"><span class="ari-more"><a href="#" onclick=" ARe.search.type('Videos', 'video')" title="Browse videos">more</a></span><span class="ari-title">RELAX VIDEOS</span></div>
						<div class="ari-content">
							<div class="ari-item focused">
								<h3 class="ari-title ari-<?php echo $css ?>"><a href="#" onclick="ARe.pview('video',{$VIDEO->id});">{$VIDEO->title}</a></h3>
								<div class="ari-animation">{$videoEmbed}</div>
							</div>
						</div>
					</div>
				</div>
				<div class="left">
					<div class="ari-section essential">
						<div class="ari-section-title mid"><div class="ari-more"><a href="#" onclick=" ARe.search.type('News', 'news')" title="Browse news">more</a></div><span class="ari-title">RELAX ESSENTIEL</span></div>
						<div class="ari-content">
							<div id="homeCaroussel" style="height:120px;white-space:nowrap;">
{foreach from=`$ESSENTIAL_NEWS` item=news}
{foreach from=$news->getRelateds() item=related}
	{if $related.relation.destinationClass == 'photo'}
					<img src="{$related.object->getPhotoUrlByFormat('h100')}" alt="{$related.relation.title|trim}" title="{$related.relation.title|trim}" style="height:100px"/>
{/if}
{/foreach}
{/foreach}
							</div>
							<ul class="ari-list-item">
{foreach from=`$ESSENTIAL_NEWS` item=news}
{assign var=content value=$news->getContentByFormat('default')}
{assign var=description value=$content.description|strip_tags}
{assign var=description value=$description|truncate:80:" ..."}
{assign var=categorization value=$news->getAssoc_categorization()}
{assign var=css value=$categorization.mainChannel_css}
								<li class="ari-item" onclick="ARe.pview('news', {$news->id});"><h4 class="ari-title ari-{$css}" title="">{$news->title}</h4><p>{$description}</p></li>
{/foreach}
							</ul>
						</div>
					</div>
				<div class="ari-section agenda">
						<div class="ari-section-title mid"><span class="ari-more"><a href="#" onclick=" ARe.search.type('Events', 'event')" title="Browse events">more</a></span><span class="ari-title">RELAX PRÉVISIONS</span></div>
						<div class="ari-content">
							<div class="ari-event ari-next-week">
								<h4 class="ari-section-subtitle  ari-entertainment">La semaine prochaine</h4>
								<div class="ari-item" onclick="ARe.pview('prevision', {$EVENT_NEXT_WEEK->id});">
									{foreach from=$EVENT_NEXT_WEEK->getRelatedsByClassAndKind('photo') item=photo name=foo}							
									{if $photo.relation.title != "" && $smarty.foreach.foo.first}
																			<img src="{$photo.object->getPhotoUrlByFormat('w50')}" alt="{$photo.relation.title|trim}" title="{$photo.relation.title|trim}" width="50" class="ari-illustration"/>	
									{/if}	
									{/foreach}
																		<h4 class="ari-title" >{$EVENT_NEXT_WEEK->title}</h4>
									{foreach from=$EVENT_NEXT_WEEK->getRelatedsByClassAndKind('location') item=location name=foo}
									{if $location.object->title != "" && $smarty.foreach.foo.first}
																				<h4>({$location.object->title|trim})</h4>
									{/if}	
									{/foreach}	
									<p>{$EVENT_NEXT_WEEK->startDate|date_format:"%d-%m-%Y"}{ if $EVENT_NEXT_WEEK->endDate != "" } - {/if}{$EVENT_NEXT_WEEK->endDate|date_format:"%d-%m-%Y"}</p>
								</div>
							</div>
							<div class="ari-event ari-next-month">
								<h4 class="ari-section-subtitle  ari-entertainment">Le mois prochain</h4>
								<div class="ari-item" onclick="ARe.pview('event', {$EVENT_NEXT_MONTH->id});">
									{foreach from=$EVENT_NEXT_MONTH->getRelatedsByClassAndKind('photo') item=photo name=foo}							
									{if $photo.relation.title != "" && $smarty.foreach.foo.first}
																			<img src="{$photo.object->getPhotoUrlByFormat('w50')}" alt="{$photo.relation.title|trim}" title="{$photo.relation.title|trim}" width="50" class="ari-illustration"/>	
									{/if}	
									{/foreach}
																		<h4 class="ari-title" >{$EVENT_NEXT_MONTH->title}</h4>
									{foreach from=$EVENT_NEXT_MONTH->getRelatedsByClassAndKind('location') item=location name=foo}							
									{if $location.object->title != "" && $smarty.foreach.foo.first}
																				<h4>({$location.object->title|trim})</h4>
									{/if}	
									{/foreach}
									<p>{$EVENT_NEXT_MONTH->startDate|date_format:"%d-%m-%Y"}{ if $EVENT_NEXT_MONTH->endDate != "" } - {/if}{$EVENT_NEXT_MONTH->endDate|date_format:"%d-%m-%Y"}</p>
								</div>
							</div>
							<div class="ari-event ari-later">
								<h4 class="ari-section-subtitle  ari-tourism">Et après ...</h4>
								{if isset($EVENT_LAST) && !empty($EVENT_LAST)}
								<div class="ari-item" onclick="ARe.pview('event', {$EVENT_LAST->id});">	
                                                                        {foreach from=$EVENT_LAST->getRelatedsByClassAndKind('photo') item=photo name=foo}                                                      
                                                                        {if $photo.relation.title != "" && $smarty.foreach.foo.first}
                                                                                                                                                        <img src="{$photo.object->getPhotoUrlByFormat('w50')}" alt="{$photo.relation.title|trim}" title="{$photo.relation.title|trim}" width="50" class="ari-illustration"/>    
                                                                        {/if}   
                                                                        {/foreach}      
                                                                                                                                                <h4 class="ari-title" >{$EVENT_LAST->title}</h4>
                                                                        {foreach from=$EVENT_LAST->getRelatedsByClassAndKind('location') item=location name=foo}                                                        
                                                                        {if $location.object->title != "" && $smarty.foreach.foo.first}
                                                                                                                                                                <h4>({$location.object->title|trim})</h4>
                                                                        {/if}   
                                                                        {/foreach}
                                                                        <p>{$EVENT_LAST->startDate|date_format:"%d-%m-%Y"}{ if $EVENT_LAST->endDate != "" } - {/if}{$EVENT_LAST->endDate|date_format:"%d-%m-%Y"}</p>
                                                                </div>
								{/if}
							</div>
						</div>			
					</div>
				</div>
				<div class="center">
					<div class="ari-section tagcloud">
						<div class="ari-section-title mid"><span class="ari-title">RELAX MOTS-CLEFS</span></div>
						<div class="ari-content">
							<p class="ari-tagcloud-click">cliquez sur un terme</p>
							<div class="ari-animation">
								<object id="injected" height="200" width="290" data="/rp/swf/tagCloud.swf" type="application/x-shockwave-flash">
									<param name="src" value="/rp/swf/tagCloud.swf" />
									<param value="high" name="quality"/>
									<param value="always" name="allowScriptAccess"/>
									<param value="transparent" name="wMode"/>
									<param value="true" name="swLiveConnect"/>
									<param value="transparent" name="wmode"/>
									<param value="cloud_data=../sites/{$site->code}/homes/tagCloud.xml&tspeed=100" name="flashVars"/>
								</object>
							</div>
						</div>
					</div>
{foreach from=`$THEMAS` item=section name=foo}
{if $smarty.foreach.foo.index  == 0}{assign var=sectionTitle value="Luxe"}{assign var=sectionId value="319"}{/if}
{if $smarty.foreach.foo.index  == 1}{assign var=sectionTitle value="Produits"}{assign var=sectionId value="508"}{/if}
{if $smarty.foreach.foo.index  == 2}{assign var=sectionTitle value="People"}{assign var=sectionId value="348"}{/if}
{if $smarty.foreach.foo.index  == 3}{assign var=sectionTitle value="Tendances"}{assign var=sectionId value="274"}{/if}
	<div id="home-thema-people" class="ari-section">
			<div class="ari-section-title small"><span class="ari-sub-title" onclick="ARe.folder.open('{$sectionTitle}', {$sectionId})">{$sectionTitle}</span></div>
			<div class="ari-content">
				<ul class="ari-list-item">
{foreach from=`$section` item=news}
{assign var=content value=$news->getContentByFormat('default')}
{assign var=description value=$content.description|strip_tags}
{assign var=description value=$description|truncate:80:" ..."}
{assign var=categorization value=$news->getAssoc_categorization()}
{assign var=css value=$categorization.mainChannel_css}
<li class="ari-item" onclick="ARe.pview('news', {$news->id});"><h4 class="ari-title ari-{$css}" title="">{$news->title|truncate:40:"..."}</h4></li>
{/foreach}							
			</ul>
		</div>
	</div>	
{/foreach}
	</div>
		<div class="bottom">
			<div class="ari-section">
				<div class="ari-section-title full"><span class="ari-title">RELAX UNIVERS</span></div>
				<div class="ari-content">
					<div class="ari-sub-section">
						<h2 class="ari-sub-title">Les rubriques relax :</h2>
						<p>							
{foreach from=`$CHANNELS` item=channel}
<a href="#" onclick="ARe.search.rubric(this, 'news', {$channel->id})">{$channel->title}</a>, 
{/foreach}				
						</p>
					</div>
					<div class="ari-sub-section">
						<h2 class="ari-sub-title">Les sélections relax : </h2>
						<p>
{foreach from=`$FOLDERS` item=folder}
<!--<a href="#" class="ari-selection-name" onclick="ARe.selection.open(this, 'selection',  {$folder->id})">{$folder->title}</a>, -->
<a href="#" class="ari-selection-name" onclick="ARe.folder.open('{$folder->title|escape:"quotes"}', {$folder->id})">{$folder->title}</a>,
{/foreach}	
						</p>
					</div>
					<div class="ari-sub-section">
						<h2 class="ari-sub-title">Les contacts relax : </h2>
						<p>
							<a href="mailto:redaction@relaxfil.com">La rédaction</a>, <a href="mailto:support.marketing@relaxfil.com">le marketing/commercial</a>, <a href="mailto:support.technique@relaxfil.com">la technique</a>.
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			<p>&copy; RELAXNEWS 2005-{$smarty.now|date_format:"%Y"}</p>
			<p style="font-size:0.65em;">Mise à jour : {$smarty.now|date_format:"%R"}</p>
		</div>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/main.html"}
{dump file=$filename content=$smarty.capture.MAIN utf8=true}
