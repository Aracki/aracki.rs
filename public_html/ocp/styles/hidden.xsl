<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="hidden">
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<input type="hidden">
		<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
	</input>
 </xsl:template>

 </xsl:stylesheet> 