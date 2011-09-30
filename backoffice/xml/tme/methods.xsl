<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        wcm.methods.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="utf-8" indent="yes" omit-xml-declaration="no" media-type="text/xml"/>
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- ================================== -->
    <!-- Template used to build TME command -->
    <!-- ================================== -->
    <xsl:template match="Command">
        <Nserver>
            <ResultEncoding>UTF-8</ResultEncoding>
            <TextID> <xsl:value-of select="TextID" /> </TextID>
            <xsl:if test="Text != ''">
                <NSTEIN_Text> <xsl:value-of select="Text" /> </NSTEIN_Text>
            </xsl:if>
            <LanguageID> <xsl:value-of select="LanguageID" /> </LanguageID>
            <Methods>
            <!-- NConceptExtractor Method -->
            <xsl:if test="Methods/NConceptExtractor/@Enabled != 'FALSE'">
              <nconceptextractor>
                <SimpleConcepts>
                  <NumberOfSimpleConcepts>
                    <xsl:value-of select="Methods/NConceptExtractor/NumberOfSimpleConcepts"/>
                  </NumberOfSimpleConcepts>
                </SimpleConcepts>
                <ComplexConcepts>
                  <NumberOfComplexConcepts>
                    <xsl:value-of select="Methods/NConceptExtractor/NumberOfComplexConcepts"/>
                  </NumberOfComplexConcepts>
                  <RelevancyLevel>
                    <xsl:value-of select="Methods/NConceptExtractor/RelevancyLevel"/>
                  </RelevancyLevel>
                </ComplexConcepts>
              </nconceptextractor>
            </xsl:if>
            <!-- NCategorizer Method -->
            <xsl:if test="Methods/NCategorizer/@Enabled != 'FALSE'">
              <ncategorizer>
                <KBid>
                  <xsl:value-of select="Methods/NCategorizer/KBid"/>
                </KBid>
                <NumberOfCategories>
                  <xsl:value-of select="Methods/NCategorizer/NumberOfCategories"/>
                </NumberOfCategories>
              </ncategorizer>
            </xsl:if>
            <!-- NFinder Method -->
            <xsl:if test="Methods/NFinder/@Enabled != 'FALSE'">
              <nfinder>
                <nfExtract>
                  <Cartridges>
                    <xsl:for-each select="Methods/NFinder/Cartridges/Cartridge">
                      <Cartridge>
                        <xsl:value-of select="."/>
                      </Cartridge>
                    </xsl:for-each>
                  </Cartridges>
                </nfExtract>
              </nfinder>
            </xsl:if>
            <!-- NLikeThis Methods -->
            <xsl:if test="Methods/NLikeThis_Index/@Enabled != 'FALSE'">
              <nlikethis>
                <Index>
                  <KBid><xsl:value-of select="Methods/NLikeThis_Index/KBid"/></KBid>
                </Index>
              </nlikethis>
            </xsl:if>
            <xsl:if test="Methods/NLikeThis_Delete/@Enabled != 'FALSE'">
              <nlikethis>
                <Delete>
                  <KBid><xsl:value-of select="Methods/NLikeThis_Delete/KBid"/></KBid>
                </Delete>
              </nlikethis>
            </xsl:if>
            <xsl:if test="Methods/NLikeThis_Compare/@Enabled != 'FALSE'">
              <nlikethis>
                <Compare>
                  <KBid><xsl:value-of select="Methods/NLikeThis_Compare/KBid"/></KBid>
                  <NumberOfMatchedFiles><xsl:value-of select="Methods/NLikeThis_Compare/NumberOfMatchedFiles"/></NumberOfMatchedFiles>
                </Compare>
              </nlikethis>
            </xsl:if>
            <!-- Nretriever Methods -->
            <xsl:if test="Methods/NRetriever/@Enabled != 'FALSE'">
              <NRetriever>
                <parameters>
                  <xsl:copy-of select="Methods/NRetriever/Parameters/*"/>
                </parameters>
                <xsl:for-each select="Methods/NRetriever/Documents/Document">
                  <doc>
                    <xsl:attribute name="id"><xsl:value-of select="@Id"/></xsl:attribute>
                    <xsl:attribute name="weight"><xsl:value-of select="@Weight"/></xsl:attribute>
                    <xsl:for-each select="Term">
                      <n>
                        <xsl:attribute name="id"><xsl:value-of select="@Type"/></xsl:attribute>
                        <xsl:value-of select="."/>
                      </n>
                    </xsl:for-each>
                  </doc>
                </xsl:for-each>
              </NRetriever>
            </xsl:if>
            <!-- NSummarizer Method -->
            <xsl:if test="Methods/NSummarizer/@Enabled != 'FALSE'">
              <nsummarizer>
                <Percentage>
                  <xsl:value-of select="Methods/NSummarizer/Percentage"/>
                </Percentage>
                <KBid>
                  <xsl:value-of select="Methods/NSummarizer/KBid"/>
                </KBid>
                <xsl:if test="Methods/NSummarizer/UseCategories != ''">
                  <UseCategories>
                    <xsl:for-each select="Methods/NSummarizer/UseCategories/Category">
                      <Category>
                        <xsl:value-of select="."/>
                      </Category>
                    </xsl:for-each>
                  </UseCategories>
                </xsl:if>
              </nsummarizer>
            </xsl:if>
            <!-- NSentiment Method -->
            <xsl:if test="Methods/NSentiment/@Enabled != 'FALSE'">
              <NSentiment>
                <Mode>
                  <xsl:value-of select="Methods/NSentiment/Mode"/>
                </Mode>
                <Type>
                  <xsl:value-of select="Methods/NSentiment/Type"/>
                </Type>
              </NSentiment>
            </xsl:if>
            <!-- NSentiment2 Method -->
            <xsl:if test="Methods/NSentiment2/@Enabled != 'FALSE'">
              <NSentiment2/>
            </xsl:if>
          </Methods>
        </Nserver>
    </xsl:template>

    <!-- ==================================== -->
    <!-- Template used to retrieve TME result -->
    <!-- ==================================== -->

    <!-- Collect all NFinder sub-terms into a string of the form "x|y|z|" in order to filter them out of extracted concepts later -->
    <xsl:variable name="nfinder_subterms"><xsl:for-each select="/Nserver/Results/nfinder/nfExtract//Subterm">|<xsl:value-of select="translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>|</xsl:for-each></xsl:variable>
    <xsl:template match="Nserver">
        <NServer>
          <xsl:apply-templates select="Results/ncategorizer"/>
          <xsl:apply-templates select="Results/nfinder/nfExtract"/>
          <xsl:apply-templates select="Results/nsentiment"/>
          <xsl:apply-templates select="Results/NSentiment"/>
          <xsl:apply-templates select="Results/NSentiment2"/>
          <xsl:apply-templates select="Results/nconceptextractor"/>
          <xsl:apply-templates select="Results/nsummarizer"/>
          <xsl:apply-templates select="Results/nlikethis"/>
          <xsl:apply-templates select="Results/NRetriever/nretriever"/>
        </NServer>
    </xsl:template>

    <!-- Summary -->
    <xsl:template match="nsummarizer">
      <Summary Source="NServer">
       <xsl:value-of select="Summary"/>
      </Summary>
    </xsl:template>
    <!-- Concepts -->
    <xsl:template match="nconceptextractor">
      <Concepts>
      <xsl:for-each select="ComplexConcepts/Concept|SimpleConcepts/Concept">
        <xsl:sort select="@Relevancy" data-type="number" order="descending"/>

        <!-- Include concept only if it is not an NFinder sub-term -->
        <xsl:if test="not(contains($nfinder_subterms, concat('|', ., '|')))">
          <xsl:if test="(. != 'NO SIMPLE CONCEPTS') and (. != 'NO COMPLEX CONCEPTS')">
            <Concept Source="NServer">
              <xsl:attribute name="Kind">
                <xsl:if test="ancestor::SimpleConcepts">Simple</xsl:if>
                <xsl:if test="ancestor::ComplexConcepts">Complex</xsl:if>
              </xsl:attribute>
              <xsl:attribute name="Frequency">
                <xsl:value-of select="@Frequency"/>
              </xsl:attribute>
              <xsl:attribute name="RelevancyScore">
                <xsl:value-of select="@Relevancy"/>
              </xsl:attribute>
              <xsl:variable name="f" select="substring(., 1, 1)" />
              <xsl:variable name="s" select="substring(., 2)" />
              <xsl:value-of select="translate($f,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')"/>
              <xsl:value-of select="$s"/>
            </Concept>
          </xsl:if>

        </xsl:if>
      </xsl:for-each>
    </Concepts>
  </xsl:template>
  <!-- Sentiment -->
  <xsl:template match="nsentiment|NSentiment|NSentiment2">
    <Sentiment Source="NServer">
      <xsl:attribute name="Tone">
        <xsl:value-of select="DocumentLevel/Tone"/>
      </xsl:attribute>
      <xsl:attribute name="PositiveTone">
        <xsl:value-of select="DocumentLevel/PositiveTone/@score"/>
      </xsl:attribute>
      <xsl:attribute name="NegativeTone">
        <xsl:value-of select="DocumentLevel/NegativeTone/@score"/>
      </xsl:attribute>
      <xsl:attribute name="Subjectivity">
        <xsl:value-of select="DocumentLevel/Subjectivity/@score"/>
      </xsl:attribute>
    </Sentiment>
  </xsl:template>
  <!-- Categories -->
  <xsl:template match="ncategorizer">
    <Categories>
      <xsl:attribute name="ConfidenceScore">
        <xsl:value-of select="RelevancyScore"/>
      </xsl:attribute>
      <xsl:for-each select="Categories/Category">
        <xsl:if test=". != 'NO CATEGORIES'">
          <Category Source="NServer">
            <xsl:attribute name="Weight">
              <xsl:value-of select="@Weight"/>
            </xsl:attribute>
            <xsl:attribute name="ConfidenceScore">
              <xsl:value-of select="../RelevancyScore"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
          </Category>
        </xsl:if>
      </xsl:for-each>
    </Categories>
  </xsl:template>
  <xsl:template match="nfExtract">
    <EntitiesList>
      <Entities Kind="ON">
          <xsl:apply-templates select="ExtractedTerm[@CartridgeID='ON']"/>
      </Entities>
      <Entities Kind="GL">
        <xsl:apply-templates select="ExtractedTerm[@CartridgeID='GL']"/>
      </Entities>
      <Entities Kind="PN">
        <xsl:apply-templates select="ExtractedTerm[@CartridgeID='PN']"/>
      </Entities>
    </EntitiesList>
  </xsl:template>
  <xsl:template match="ExtractedTerm">
      <Entity Source="NServer">
        <xsl:attribute name="Kind">
          <xsl:value-of select="@CartridgeID"/>
        </xsl:attribute>
        <xsl:attribute name="Weight">
          <xsl:value-of select="@Weight"/>
        </xsl:attribute>
        <xsl:attribute name="RelevancyScore">
          <xsl:value-of select="@RelevancyScore"/>
        </xsl:attribute>
        <xsl:attribute name="ConfidenceScore">
          <xsl:value-of select="@ConfidenceScore"/>
        </xsl:attribute>
        <xsl:attribute name="Frequency">
          <xsl:value-of select="@Frequency"/>
        </xsl:attribute>
        <xsl:choose>
          <xsl:when test="@CartridgeID = 'PN'">
            <xsl:value-of select="nfinderNormalized"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:choose>
              <xsl:when test="nfinderNormalized != ''">
                <xsl:value-of select="nfinderNormalized"/>
              </xsl:when>
              <xsl:otherwise>
                <xsl:value-of select="MainTerm"/>
              </xsl:otherwise>
            </xsl:choose>
          </xsl:otherwise>
        </xsl:choose>
      </Entity>
  </xsl:template>
  <xsl:template match="nlikethis">
    <xsl:choose>
      <xsl:when test="Compare != ''">
        <SimilarTexts>
          <xsl:for-each select="Compare/Text">
            <xsl:sort select="@Weight" data-type="number" order="descending"/>
            <xsl:if test="(. != 'NO SIMILAR TEXTS') and (@Weight >= 0.1)">
              <SimilarText Source="NServer">
                <xsl:attribute name="Id">
                  <xsl:value-of select="."/>
                </xsl:attribute>
                <xsl:attribute name="Weight">
                  <xsl:value-of select="@Weight"/>
                </xsl:attribute>
              </SimilarText>
            </xsl:if>
          </xsl:for-each>
        </SimilarTexts>
      </xsl:when>
    </xsl:choose>
  </xsl:template>
  <xsl:template match="nretriever">
    <xsl:apply-templates select="groups"/>
  </xsl:template>
  <xsl:template match="groups">
    <TermGroups>
      <xsl:apply-templates select="group"/>
    </TermGroups>
  </xsl:template>
  <xsl:template match="group">
    <TermGroup>
      <xsl:attribute name="Depth"><xsl:value-of select="@depth"/></xsl:attribute>
      <xsl:attribute name="DisplayFrequency"><xsl:value-of select="@displayFrequency"/></xsl:attribute>
      <xsl:attribute name="Frequency"><xsl:value-of select="@frequency"/></xsl:attribute>
      <xsl:if test="id != ''">
        <Id><xsl:value-of select="id"/></Id>
      </xsl:if>
      <xsl:if test="label != ''">
        <Label>
          <xsl:attribute name="Type"><xsl:value-of select="label/@type"/></xsl:attribute>
          <xsl:value-of select="label"/>
        </Label>
      </xsl:if>
      <xsl:if test="tokens != ''">
        <Tokens>
          <xsl:for-each select="tokens/token">
            <Token><xsl:value-of select="."/></Token>
          </xsl:for-each>
        </Tokens>
      </xsl:if>
      <xsl:if test="docs != ''">
        <Documents>
          <xsl:for-each select="docs/doc">
            <Document><xsl:value-of select="."/></Document>
          </xsl:for-each>
        </Documents>
      </xsl:if>
      <xsl:apply-templates select="groups"/>
    </TermGroup>
  </xsl:template>
</xsl:stylesheet>