<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="image">
	<xsl:param name="style"/>
	<xsl:param name="value"/>
	<xsl:param name="name"/>
	<xsl:param name="width"/>
	<xsl:param name="height"/>
	<xsl:param name="max"/>
	<xsl:param name="labBrowseServer"/>
	<xsl:param name="labSelectedImagePreview"/>
	<xsl:param name="dirPretrage"/>

	<xsl:variable name="rootFolder">
		<xsl:choose>
			<xsl:when test="($dirPretrage = '')">/upload</xsl:when>
			<xsl:otherwise><xsl:value-of select="$dirPretrage"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
		
	<table class="ocp_uni_table">
		<tr>
			<td class="ocp_dugmici_td_levi">
				<input type="text" class="ocp_forma">
					<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
				</input>
			</td>
			<td class="ocp_dugmici_td_desni_2">
				<a>
				<xsl:attribute name="href">javascript:x = window.open('/ocp/controls/fileControl/frameset.php?field=document.formObject.<xsl:value-of select="$name"/>&amp;basicFolder=<xsl:value-of select="$value"/>&amp;root=<xsl:value-of select="$rootFolder"/>&amp;width=<xsl:value-of select="$width"/>&amp;height=<xsl:value-of select="$height"/>&amp;max=<xsl:value-of select="$max"/>','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();</xsl:attribute>
				<img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola">
					<xsl:attribute name="title"><xsl:value-of select="$labBrowseServer"/></xsl:attribute>
				</img>
				</a>
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">urlCont=document.formObject.<xsl:value-of select="$name"/>;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars' );</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labSelectedImagePreview"/></xsl:attribute>
					</img>
				</a>
			</td>
		</tr>
	</table>


 </xsl:template>

 </xsl:stylesheet> 