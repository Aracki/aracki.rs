<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="file">
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
				<input type="text" class="ocp_forma" style="{$style}" name="{$name}" value="{$value}"/>
			</td>
			<td class="ocp_dugmici_td_desni_2">
				<a>
				<xsl:attribute name="href">javascript:x=window.open('/ocp/controls/fileControl/frameset.php?random=<xsl:value-of select="./@random"/>&amp;width=<xsl:value-of select="$width"/>&amp;height=<xsl:value-of select="$height"/>&amp;field=formObject.<xsl:value-of select="$name"/>&amp;basicFolder='+document.formObject.<xsl:value-of select="$name"/>.value+'&amp;root=<xsl:value-of select="$rootFolder"/>&amp;max=<xsl:value-of select="$max"/>','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes');x.focus();</xsl:attribute>
				<img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola" title="{$labBrowseServer}"/>
				</a>
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">urlCont=formObject.<xsl:value-of select="$name"/>;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars' );</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola" title="{$labSelectedImagePreview}"/>
				</a>
			</td>
		</tr>
	</table>


 </xsl:template>

 </xsl:stylesheet> 