<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="color">
	<xsl:param name="style"/>
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<xsl:param name="labColorPallete"/>
	<input type="text" class="ocp_forma">
		<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
	</input>
	<a>
		<xsl:attribute name="href">javascript:paletteOpen('<xsl:value-of select="$name"/>')</xsl:attribute>
		<img src="/ocp/img/opsti/kontrole/kontrola_boja.gif" border="0" class="ocp_kontrola">
			<xsl:attribute name="title"><xsl:value-of select="$labColorPallete"/></xsl:attribute>
		</img>
	</a>
 </xsl:template>

 </xsl:stylesheet> 