<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="lib/image.xsl"/>
<xsl:output method="html"/>

<xsl:template match="blok">
	<!--START XSL Varijable-->
	<xsl:variable name="nivo"><xsl:choose>
			<xsl:when test="pozicija = '1' or pozicija = '3'">0</xsl:when>
			<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:variable name="prelom"><xsl:choose>
			<xsl:when test="pozicija = '1' or pozicija = '2'">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>

	<!--END XSL Varijable-->

	<div class="block">
		<!--***********NASLOV***********-->
		<xsl:if test="$nivo != '1'">
			<xsl:call-template name="naslovTemplate">
				<xsl:with-param name="naslov" select="naslov"/>
				<xsl:with-param name="podnaslov" select="podnaslov"/>
				<xsl:with-param name="sizze" select="sizze"/>
			</xsl:call-template>
		</xsl:if> 
		<xsl:if test="./import/@type = 'slika' and ./import/urlSlike != ''">
			<xsl:call-template name="divSlikaTemplate">
				<xsl:with-param name="url" select="./import/urlSlike"/>
				<xsl:with-param name="okvir" select="./import/border"/>
				<xsl:with-param name="width" select="./import/width"/>
				<xsl:with-param name="height" select="./import/height"/>
				<xsl:with-param name="sign" select="./import/signature"/>
				<xsl:with-param name="alt" select="./import/labela"/>
				<xsl:with-param name="align" select="./import/alignment"/>
				<xsl:with-param name="link" select="./import/urlLinka"/>
			</xsl:call-template>
		</xsl:if>


		<xsl:if test="$nivo = '1'">
			<xsl:call-template name="naslovTemplate">
				<xsl:with-param name="naslov" select="naslov"/>
				<xsl:with-param name="podnaslov" select="podnaslov"/>
				<xsl:with-param name="sizze" select="sizze"/>
			</xsl:call-template>
		</xsl:if>
		<!--***********TEKST***********-->
		<xsl:if test="tekst != ''">
			<div>
				<xsl:if test="$prelom != '1'">
					<xsl:attribute name="class">optimize</xsl:attribute>
				</xsl:if>
				<xsl:value-of disable-output-escaping="yes" select="tekst"/>
			</div>
		</xsl:if>
	</div>

</xsl:template>


<xsl:template name="naslovTemplate">
	<xsl:param name="naslov"/>
	<xsl:param name="podnaslov"/>
	<xsl:param name="sizze"/>	
	<xsl:if test="$naslov!=''">
		<h2><xsl:value-of select="$naslov" disable-output-escaping="yes"/></h2>
	</xsl:if>
	<xsl:if test="$podnaslov!=''">
		<h3><xsl:value-of select="$podnaslov" disable-output-escaping="yes"/></h3>
	</xsl:if>
	<xsl:if test="sizze != ''">
		<p class="preamble"><xsl:value-of select="sizze" disable-output-escaping="yes"/></p>
	</xsl:if>
</xsl:template>

</xsl:stylesheet> 