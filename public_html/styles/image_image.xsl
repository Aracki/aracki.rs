<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="lib/image.xsl"/>
<xsl:output method="html"/>
<xsl:template match="blok">
<div class="block">
		<xsl:apply-templates/>
</div>
</xsl:template>

<xsl:template match="import">
	<xsl:if test="@type='slika' and ./urlSlike != ''">

		<xsl:variable name="linkSlike">
			<xsl:choose>
				<xsl:when test="@name = 'Slika1'">
					<xsl:if test="following-sibling::import[@type='link'] and following-sibling::import[@name='Link1'] and following-sibling::import[urlLinka != '']">
						<xsl:value-of select="following-sibling::import/urlLinka"/>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="following-sibling::import[@type='link'] and following-sibling::import[@name='Link2'] and following-sibling::import[urlLinka != '']">
						<xsl:value-of select="following-sibling::import/urlLinka"/>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>	
		
		<xsl:variable name="altSlike">
			<xsl:choose>
				<xsl:when test="@name = 'Slika1'">
					<xsl:if test="following-sibling::import[@type='link'] and following-sibling::import[@name='Link1'] and following-sibling::import[labela != '']">
						<xsl:value-of select="following-sibling::import/labela"/>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="following-sibling::import[@type='link'] and following-sibling::import[@name='Link2'] and following-sibling::import[labela != '']">
						<xsl:value-of select="following-sibling::import/labela"/>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>	
						
		<xsl:value-of select="./urlLinka"/>
		<xsl:call-template name="divSlikaTemplate">
			<xsl:with-param name="align" select="./alignment"/>
			<xsl:with-param name="link" select="$linkSlike"/>
			<xsl:with-param name="sign" select="./signature"/>
			<xsl:with-param name="alt" select="$altSlike"/>
			<xsl:with-param name="url" select="./urlSlike"/>
			<xsl:with-param name="okvir" select="./border"/>
			<xsl:with-param name="width" select="./width"/>
			<xsl:with-param name="height" select="./height"/>
	</xsl:call-template>	

	</xsl:if>
</xsl:template>

</xsl:stylesheet> 