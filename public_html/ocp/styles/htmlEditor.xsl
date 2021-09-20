<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="htmlEditor">
	<xsl:param name="width"/>
	<xsl:param name="height"/>
	<xsl:param name="value"/>
	<xsl:param name="name"/>
	<xsl:param name="label"/>
	<xsl:param name="showSimple"/>
	<xsl:param name="labRichTextFormat"/>

	<xsl:if test="$showSimple = '1'">
		<iframe frameborder="0" scrolling="no">
			<xsl:attribute name="id">simpleEditorObject_<xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="name">simpleEditorObject_<xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="src">/ocp/controls/simple_editor/editor.php?random=<xsl:value-of select="../@random"/>&amp;label=<xsl:value-of select="@label"/>&amp;field=<xsl:value-of select="$name"/>&amp;width=<xsl:value-of select="$width"/>&amp;height=<xsl:value-of select="$height"/></xsl:attribute>
			<xsl:attribute name="style">width:<xsl:value-of select="$width"/>px; height:<xsl:value-of select="$height"/>px; border:0;</xsl:attribute>
			<img src="/ocp/img/blank.gif" width="1"/>
		</iframe>
	</xsl:if>
	<textarea class="ocp_forma">
		<xsl:attribute name="style">width:<xsl:value-of select="$width"/>px; height: <xsl:value-of select="$height"/>px</xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
		<xsl:choose>
			<xsl:when test="$show-simple-html-editor = '1'">
				<xsl:attribute name="style">display:none</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="$value = ''">&amp;nbsp;</xsl:when>
			<xsl:otherwise><xsl:value-of select="$value"/></xsl:otherwise>
		</xsl:choose>
	</textarea>
	<script>
		if (!simpleEditorExists) {
			simpleEditorExists = true;
			simpleEditorArr = new Array();
		}
		simpleEditorArr.push("simpleEditorObject_<xsl:value-of select="$name"/>");
	</script>
	<script src="/ocp/jscript/openHtmlEditor.js"><img src="/ocp/img/blank.gif"/></script>
	<br/>
	<div style="padding: 4px;">
		<table cellpadding="0" cellspacing="0">
			<xsl:attribute name="onClick">openEditor('formObject.<xsl:value-of select="$name"/>','<xsl:value-of select="@label"/>','<xsl:value-of select="$show-simple-html-editor"/>');</xsl:attribute>
			<tr>
				<td style="cursor:pointer;">
					<img src="/ocp/img/opsti/kontrole/dugme_napred_tekst.gif" width="21" height="21">
						<xsl:attribute name="title"><xsl:value-of select="$labRichTextFormat"/></xsl:attribute>
					</img>
				</td>
				<td class="ocp_opcije_dugme" style="cursor:pointer;">
					<xsl:value-of select="$labRichTextFormat"/>
				</td>
				<td style="cursor:pointer;">
					<img src="/ocp/img/opsti/kontrole/dugme_desni.gif" width="6" height="21"/>
				</td>
			</tr>
		</table>
	</div>
 </xsl:template>

 </xsl:stylesheet> 