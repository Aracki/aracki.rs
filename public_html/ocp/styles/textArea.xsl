<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="textArea">
	<xsl:param name="style"/>
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<textarea class="ocp_forma">
		<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
		<xsl:comment>  </xsl:comment>
		<xsl:value-of select="$value"/>
	 </textarea>
 </xsl:template>

 </xsl:stylesheet> 