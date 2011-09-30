<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        wcm.nSemanticDisplay.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">
    <xsl:param name="kind" select="''" />
    <xsl:param name="method" select="''" />
    <xsl:param name="locked" select="locked" />
    <xsl:param name="displayOnly" select="displayOnly" />
    <xsl:output method="xml" encoding="utf-8" indent="yes"
        omit-xml-declaration="yes" media-type="text/html" />

    <!-- NServer -->
    <xsl:template match="NServer">
        <xsl:apply-templates select="Concepts" />
        <xsl:apply-templates select="Categories" />
        <xsl:apply-templates select="EntitiesList" />
        <xsl:apply-templates select="Sentiment" />
        <xsl:apply-templates select="Summary" />
        <xsl:apply-templates select="SimilarTexts" />
    </xsl:template>

    <!-- Concepts -->
    <xsl:template match="Concepts">
        <xsl:for-each select="Concept">
            <div>
                <xsl:attribute name="id">div_concept_<xsl:value-of
                        select="count(preceding::Concept) +1" />
                </xsl:attribute>
                <xsl:if test="$locked='0'">
                    <xsl:attribute name="style">cursor:pointer</xsl:attribute>
                    <xsl:attribute name="onDblClick">javascript:ajaxCall_NConceptExtractor('edit', <xsl:value-of
                            select="count(preceding::Concept) +1" />);</xsl:attribute>
                </xsl:if>
                <div
                    style="width:305px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                    <xsl:if test="@Source='NServer'">
                        <xsl:if test="$locked='0'">
                            <a>
                                <xsl:attribute name="href">javascript:ajaxCall_NConceptExtractor('validate', <xsl:value-of
                                        select="count(preceding::Concept) +1" />);</xsl:attribute>
                                <img src="img/icons/nserver.gif"
                                    style="float:middle;margin-right:5px;">
                                </img>
                            </a>
                        </xsl:if>
                        <xsl:if test="$locked='1'">
                            <img src="img/icons/nserver.gif"
                                style="float:middle;margin-right:5px;">
                            </img>
                        </xsl:if>
                    </xsl:if>
                    <xsl:if test="@Source='User'">
                        <img src="img/icons/user.gif"
                            style="float:midle;margin-right:5px;">
                        </img>
                    </xsl:if>
                    <xsl:value-of select="." />
                    <div style="float:right">
                        <img>
                            <xsl:attribute name="src">img/icons/rating_<xsl:value-of
                                    select="round(@RelevancyScore div 20)" />s.gif</xsl:attribute>
                            <xsl:attribute name="ALT">Score : <xsl:value-of
                                    select="@RelevancyScore" />
                            </xsl:attribute>
                        </img>
                    </div>
                </div>
                <xsl:if test="$locked='0'">
                    <div
                        style="width:44px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NConceptExtractor('edit', <xsl:value-of
                                    select="count(preceding::Concept) +1" />);</xsl:attribute>
                            <img src="img/edit.gif" style="float:midle" />
                        </a>
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NConceptExtractor('delete', <xsl:value-of
                                    select="count(preceding::Concept) +1" />);</xsl:attribute>
                            <img src="img/delete.gif"
                                style="float:absmidle" />
                        </a>
                    </div>
                </xsl:if>
            </div>
        </xsl:for-each>
    </xsl:template>

    <!-- Categories -->
    <xsl:template match="Categories">
        <xsl:for-each select="Category">
            <div>
                <xsl:attribute name="id">div_category_<xsl:value-of
                        select="count(preceding::Category) +1" />
                </xsl:attribute>
                <div
                    style="width:305px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                    <xsl:if test="@Source='NServer'">
                        <xsl:if test="$locked='0'">
                            <div>
                                <xsl:attribute name="style">cursor:pointer</xsl:attribute>
                                <xsl:if test="$displayOnly='0'">
                                    <xsl:attribute name="onDblClick">javascript:ajaxCall_<xsl:value-of
                                            select="$method" />('validate', <xsl:value-of
                                            select="count(preceding::SimilarText)+1" />);</xsl:attribute>
                                </xsl:if>
                                <xsl:if test="$displayOnly='1'">
                                    <xsl:attribute name="onDblClick">javascript:ajaxCall_<xsl:value-of
                                            select="$method" />('select_category', '<xsl:value-of
                                            select="." />');</xsl:attribute>
                                </xsl:if>
                                <img src="img/icons/nserver.gif"
                                    style="float:middle;margin-right:5px;">
                                </img>
                            </div>
                        </xsl:if>
                        <xsl:if test="$locked='1'">
                            <img src="img/icons/nserver.gif"
                                style="float:middle;margin-right:5px;">
                            </img>
                        </xsl:if>
                    </xsl:if>
                    <xsl:if test="@Source='User'">
                        <img src="img/icons/user.gif"
                            style="float:midle;margin-right:5px;">
                        </img>
                    </xsl:if>
                    <xsl:value-of select="." />
                    <div style="float:right">
                        <img>
                            <xsl:attribute name="src">img/icons/rating_<xsl:value-of
                                    select="round(@Weight div 20)" />s.gif</xsl:attribute>
                            <xsl:attribute name="ALT">Score : <xsl:value-of
                                    select="@Weight" />
                            </xsl:attribute>
                        </img>
                    </div>
                </div>
                <xsl:if test="($locked='0') and ($displayOnly='0')">
                    <div
                        style="width:44px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NCategorizer('edit', <xsl:value-of
                                    select="count(preceding::Concept) +1" />)</xsl:attribute>
                            <img src="img/edit.gif" style="float:midle" />
                        </a>
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NCategorizer('delete', <xsl:value-of
                                    select="count(preceding::Concept) +1" />)</xsl:attribute>
                            <img src="img/delete.gif"
                                style="float:absmidle" />
                        </a>
                    </div>
                </xsl:if>
            </div>
        </xsl:for-each>
    </xsl:template>

    <!-- Entities -->
    <xsl:template match="EntitiesList">
        <xsl:if test="$method='NFinder_ON'">
            <xsl:apply-templates
                select="//Entities/Entity[@Kind='ON']">
                <xsl:with-param name="type" select="'ON'" />
            </xsl:apply-templates>
        </xsl:if>
        <xsl:if test="$method='NFinder_PN'">
            <xsl:apply-templates
                select="//Entities/Entity[@Kind='PN']">
                <xsl:with-param name="type" select="'PN'" />
            </xsl:apply-templates>
        </xsl:if>
        <xsl:if test="$method='NFinder_GL'">
            <xsl:apply-templates
                select="//Entities/Entity[@Kind='GL']">
                <xsl:with-param name="type" select="'GL'" />
            </xsl:apply-templates>
        </xsl:if>
    </xsl:template>
    <xsl:template match="Entity">
        <xsl:param name="type" />
        <div>
            <xsl:attribute name="id">div_entity_<xsl:value-of
                    select="@Kind" />_<xsl:value-of
                    select="count(preceding::Entity[@Kind=$type]) +1" />
            </xsl:attribute>
            <xsl:if test="$locked='0'">
                <xsl:attribute name="onDblClick">javascript:ajaxCall_<xsl:value-of
                        select="$method" />('edit', <xsl:value-of
                        select="count(preceding::Entity[@Kind=$type]) +1" />);</xsl:attribute>
                <xsl:attribute name="style">cursor:pointer</xsl:attribute>
            </xsl:if>
            <div
                style="width:300px;height:45px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                <xsl:if test="@Source='NServer'">
                    <xsl:if test="$locked='0'">
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_<xsl:value-of
                                    select="$method" />('validate', <xsl:value-of
                                    select="count(preceding::Entity[@Kind=$type]) +1" />);</xsl:attribute>
                            <img src="img/icons/nserver.gif"
                                style="float:middle;margin-right:5px;">
                            </img>
                        </a>
                    </xsl:if>
                    <xsl:if test="$locked='1'">
                        <img src="img/icons/nserver.gif"
                            style="float:middle;margin-right:5px;">
                        </img>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="@Source='User'">
                    <img src="img/icons/user.gif"
                        style="float:midle;margin-right:5px;">
                    </img>
                </xsl:if>
                <xsl:value-of select="." />
                <div style="float:right">
                    <img>
                        <xsl:attribute name="src">img/icons/rating_<xsl:value-of
                                select="round(@Weight div 20)" />s.gif</xsl:attribute>
                        <xsl:attribute name="alt">Score : <xsl:value-of
                                select="@Weight" />
                        </xsl:attribute>
                    </img>
                </div>
            </div>
            <xsl:if test="$locked='0'">
                <div
                    style="width:38px;height:45px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                    <a>
                        <xsl:attribute name="href">javascript:ajaxCall_<xsl:value-of
                                select="$method" />('edit', <xsl:value-of
                                select="count(preceding::Entity[@Kind=$type]) +1" />);</xsl:attribute>
                        <img src="img/edit.gif" style="float:midle" />
                    </a>
                    <a>
                        <xsl:attribute name="href">javascript:ajaxCall_<xsl:value-of
                                select="$method" />('delete', <xsl:value-of
                                select="count(preceding::Entity[@Kind=$type]) +1" />);</xsl:attribute>
                        <img src="img/delete.gif"
                            style="float:absmidle" />
                    </a>
                </div>
            </xsl:if>
        </div>
    </xsl:template>

    <!-- Sentiment -->
    <xsl:template match="Sentiment">
        <div>
            <xsl:attribute name="id">div_sentiment_tone</xsl:attribute>
            <xsl:if test="$locked='0'">
                <xsl:attribute name="style">cursor:pointer</xsl:attribute>
                <xsl:attribute name="onDblClick">javascript:ajaxCall_NSentiment('edit', 'tone');</xsl:attribute>
            </xsl:if>
            <div style="width:305px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                <xsl:if test="@Source='NServer'">
                    <xsl:if test="$locked='0'">
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NSentiment('validate', 'tone');</xsl:attribute>
                            <img src="img/icons/nserver.gif" style="float:middle;margin-right:5px;"></img>
                        </a>
                    </xsl:if>
                    <xsl:if test="$locked='1'">
                        <img src="img/icons/nserver.gif" style="float:middle;margin-right:5px;"></img>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="@Source='User'">
                    <img src="img/icons/user.gif" style="float:midle;margin-right:5px;"></img>
                </xsl:if>
                <strong><xsl:value-of select="php:function('constant', string('_BIZ_TONE'))"/></strong>
                <div style="float:right">
                    <xsl:value-of select="@Tone" />
                </div>
            </div>
            <xsl:if test="$locked='0'">
                <div style="width:44px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                    <a>
                        <xsl:attribute name="href">javascript:ajaxCall_NSentiment('edit', 'tone');</xsl:attribute>
                        <img src="img/edit.gif" style="float:midle" />
                    </a>
                </div>
            </xsl:if>
        </div>
        <div>
            <xsl:attribute name="id">div_sentiment_subjectivity</xsl:attribute>
            <xsl:if test="$locked='0'">
                <xsl:attribute name="style">cursor:pointer</xsl:attribute>
                <xsl:attribute name="onDblClick">javascript:ajaxCall_NSentiment('edit', 'subjectivity');</xsl:attribute>
            </xsl:if>
            <div style="width:305px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;float:left;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                <xsl:if test="@Source='NServer'">
                    <xsl:if test="$locked='0'">
                        <a>
                            <xsl:attribute name="href">javascript:ajaxCall_NSentiment('validate', 'subjectivity');</xsl:attribute>
                            <img src="img/icons/nserver.gif" style="float:middle;margin-right:5px;"></img>
                        </a>
                    </xsl:if>
                    <xsl:if test="$locked='1'">
                        <img src="img/icons/nserver.gif" style="float:middle;margin-right:5px;"></img>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="@Source='User'">
                    <img src="img/icons/user.gif" style="float:midle;margin-right:5px;"></img>
                </xsl:if>
                <strong><xsl:value-of select="php:function('constant', string('_BIZ_SUBJECTIVITY'))"/></strong>
                <div style="float:right">
                    <xsl:value-of select="@Subjectivity" />
                </div>
            </div>
            <xsl:if test="$locked='0'">
                <div style="width:44px;height:40px;border-style:solid;border-width:1px;border-color:CCCCCC;position:right;margin-left:310px;padding-top:2px;padding-bottom:2px;padding-left:2px;padding-right:2px;margin-bottom:3px;">
                    <a>
                        <xsl:attribute name="href">javascript:ajaxCall_NSentiment('edit', 'subjectivity');</xsl:attribute>
                        <img src="img/edit.gif" style="float:midle" />
                    </a>
                </div>
            </xsl:if>
        </div>
    </xsl:template>

    <!-- Summary -->
    <xsl:template match="Summary">
        <textarea id="nSummary" style="width:378px; margin: 2px; height:250px">
            <xsl:if test="$locked='1'">
                <xsl:attribute name="readonly">true</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="." />
        </textarea>
    </xsl:template>

    <!-- SimilarTexts -->
    <xsl:template match="SimilarTexts">
        <xsl:for-each select="SimilarText">
            <xsl:variable name="object" select="substring-before(@Id, '_')"/>
            <xsl:variable name="objectId" select="substring-after(@Id, '_')"/>
            <div>
                <xsl:choose>
                    <xsl:when test="($locked = '0')">
                        <xsl:attribute name="style">cursor:pointer; clear: both;border: 1px solid #cccccc; margin: 5px; padding: 3px 0;</xsl:attribute>
                        <xsl:attribute name="onDblClick">javascript:ajaxCall_<xsl:value-of
                                select="$method" />('createRelation', <xsl:value-of
                                select="count(preceding::SimilarText)+1" />, 'class:<xsl:value-of
                                select="string($object)" />~id:<xsl:value-of
                                select="string($objectId)" />');</xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="style">border: 1px solid #cccccc; margin: 5px; padding: 3px 0;</xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:attribute name="id">div_similar_text_<xsl:value-of
                        select="count(preceding::SimilarText)+1" />
              </xsl:attribute>
                <xsl:value-of disable-output-escaping="yes"
                    select="php:function('renderBizobjectById', string($object), string($objectId), 'render_search_item')" />
                <p>
                    <xsl:attribute name="style">margin: 0; padding: 0; text-align: right;</xsl:attribute>
                    <img>
                        <xsl:attribute name="src">img/icons/rating_<xsl:value-of
                                select="round(@Weight * 10)" />s.gif</xsl:attribute>
                        <xsl:attribute name="alt">Weight: <xsl:value-of
                                select="format-number(@Weight, '##.#%')" />
                      </xsl:attribute>
                    </img>
                </p>
            </div>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
