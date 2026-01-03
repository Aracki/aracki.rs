<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="lib/image.xsl"/>
<xsl:output method="html"/>

<xsl:template match="blok">
<div class="large_image">
		<xsl:apply-templates/>
</div>
</xsl:template>

<xsl:template match="import">
	<xsl:if test="@type='slika' and ./urlSlike != ''">
			
		<xsl:variable name="linkSlike">
			<xsl:if test="../import/@type='link' and ../import/urlLinka != ''">
				<xsl:value-of select="../import/urlLinka"/>
			</xsl:if>
		</xsl:variable>
		<xsl:variable name="altSlike">
			<xsl:if test="../import/@type='link' and ../import/labela != ''">
				<xsl:value-of select="../import/labela"/>
			</xsl:if>
		</xsl:variable>
		<xsl:variable name="target">
			<xsl:choose>
				<xsl:when test="contains(../import/urlLinka, 'navigate.php')">_self</xsl:when>
				<xsl:otherwise>_blank</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:call-template name="divSlikaTemplate">
			<xsl:with-param name="url" select="./urlSlike"/>
			<xsl:with-param name="align" select="./alignment"/>
			<xsl:with-param name="okvir" select="./border"/>
			<xsl:with-param name="width" select="./width"/>
			<xsl:with-param name="height" select="./height"/>
			<xsl:with-param name="sign" select="./signature"/>
			<xsl:with-param name="link" select="$linkSlike"/>
			<xsl:with-param name="alt" select="$altSlike"/>			
			<xsl:with-param name="target" select="$target"/>			
		</xsl:call-template>
	 </xsl:if>
 </xsl:template>

</xsl:stylesheet>