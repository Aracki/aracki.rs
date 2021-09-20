<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="textBox">
	<xsl:param name="style"/>
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<xsl:param name="labAdvancedSearch"/>

	<xsl:choose>
		<xsl:when test="../@action != 'objects.php' and ../@action != 'selected.php'">
			<input type="text" class="ocp_forma">
				<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
			</input>
		</xsl:when>
		<xsl:otherwise>
			<input type="text" class="ocp_forma">
				<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
			</input>
			<br/>
			<a class="ocp_link" href="#">
				<xsl:attribute name="onMouseDown">
				var temp = document.getElementById('advancedSearch_<xsl:value-of select="$name"/>');
				if (temp.style.display == 'none'){
					temp.style.display='block';
					window.frames['advancedSearchIframe_<xsl:value-of select="$name"/>'].fillFromParent();
				} else {
					temp.style.display='none';
				}
				return false;
				</xsl:attribute>
				<xsl:value-of select="$labAdvancedSearch"/>
			</a>
			<div style="display:none;">
				<xsl:attribute name="id">advancedSearch_<xsl:value-of select="$name"/></xsl:attribute>
				<iframe height="140" frameborder="0" scrolling="no">
					<xsl:attribute name="name">advancedSearchIframe_<xsl:value-of select="$name"/></xsl:attribute>
					<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
					<xsl:attribute name="src">/ocp/controls/advanced_search/form.php?random=<xsl:value-of select="../@random"/>&amp;field=document.formObject.<xsl:value-of select="$name"/></xsl:attribute>
					<img src="/ocp/img/blank.gif" width="1" border="0"/>
				</iframe>
			</div>
		</xsl:otherwise>
	</xsl:choose>

 </xsl:template>

 </xsl:stylesheet> 