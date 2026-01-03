<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="checkBox">
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<input type="checkbox" value="1">
		<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
		<xsl:if test="$value='1' or $value='true' or ($name='prelom' and ../@Blok_Id='')">
			<xsl:attribute name="checked"/>
		</xsl:if>
	</input>
 </xsl:template>

 </xsl:stylesheet> 