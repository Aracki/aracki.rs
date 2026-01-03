<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:import href="../../styles/complex.xsl"/>
<xsl:import href="../../styles/select.xsl"/>
<xsl:import href="../../styles/radio.xsl"/>
<xsl:import href="../../styles/datum.xsl"/>
<xsl:import href="../../styles/blockInfo.xsl"/>
<xsl:import href="../../styles/color.xsl"/>
<xsl:import href="../../styles/textBox.xsl"/>
<xsl:import href="../../styles/textArea.xsl"/>
<xsl:import href="../../styles/checkBox.xsl"/>
<xsl:import href="../../styles/labela.xsl"/>
<xsl:import href="../../styles/intLink.xsl"/>
<xsl:import href="../../styles/file.xsl"/>
<xsl:import href="../../styles/fileImage.xsl"/>
<xsl:import href="../../styles/folder.xsl"/>
<xsl:import href="../../styles/htmlEditor.xsl"/>
<xsl:import href="../../styles/textArea.xsl"/>
<xsl:import href="../../styles/imgPosition.xsl"/>
<xsl:import href="../../styles/hidden.xsl"/>
<xsl:import href="../../styles/foreignKey.xsl"/>
<xsl:import href="../../styles/fkAutoComplete.xsl"/>

<xsl:output method="html"/>

<!--TEMPLATES KOJI SU NAMED-->

<xsl:template name="polje">
	<xsl:param name="imePolja"/>
	<xsl:param name="labelaPolja"/>
	<xsl:param name="tipPolja"/>
	<xsl:param name="vrednostPolja"/>
	<xsl:param name="value_label"/>
	<xsl:param name="dirPretrage"/>
	<xsl:param name="sirinaPolja"/>
	<xsl:param name="visinaPolja"/>
	<xsl:param name="sveVrednosti"/>
	<xsl:param name="sveLabele"/>
	<xsl:param name="straId"/>
	<xsl:param name="podtip"/>
	<xsl:param name="restrict"/>
	<xsl:param name="startIndex"/>
	<xsl:param name="labCalendar"/>
	<xsl:param name="labCreateLinkOnPage"/>
	<xsl:param name="labCreateLinkOnBlock"/>
	<xsl:param name="labBrowseServer"/>
	<xsl:param name="labSelectedImagePreview"/>
	<xsl:param name="labSelectedLinkPreview"/>
	<xsl:param name="labRichTextFormat"/>
	<xsl:param name="labColorPallete"/>
	<xsl:param name="labUpdateListOfValue"/>
	<xsl:param name="labSelect"/>
<!-- DEO KOJI JE PRE BIO U MATCH TEMPLATE-U -->

<xsl:choose>
	<xsl:when test="$tipPolja='color'">
	<xsl:variable name="style">width:<xsl:value-of select="$color-field-width"/>px;</xsl:variable>
	<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
	<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
	<xsl:variable name="colors"><xsl:value-of select="$labColorPallete"/></xsl:variable>

	<xsl:call-template name="color">
		<xsl:with-param name="style" select="$style"/>
		<xsl:with-param name="name" select="$name"/>
		<xsl:with-param name="value" select="$value"/>
		<xsl:with-param name="labColorPallete" select="$labColorPallete"/>
	</xsl:call-template>
	</xsl:when>
	
	<xsl:when test="$tipPolja = 'textBox'">
		<xsl:variable name="style">width:<xsl:value-of select="$text-field-width"/>px;</xsl:variable>
		<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
		<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
		<xsl:call-template name="textBox">
			<xsl:with-param name="style" select="$style"/>
			<xsl:with-param name="value" select="$value"/>
			<xsl:with-param name="name" select="$name"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'textArea'">
		<xsl:variable name="style">width:<xsl:value-of select="$text-area-width"/>px; height:<xsl:value-of select="$text-area-height"/>px;</xsl:variable>
		<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
		<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
		<xsl:call-template name="textArea">
			<xsl:with-param name="style" select="$style"/>
			<xsl:with-param name="value" select="$value"/>
			<xsl:with-param name="name" select="$name"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'textDate'">
		<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
		<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
		<xsl:call-template name="datum">
			<xsl:with-param name="name" select="$name"/>
			<xsl:with-param name="value" select="$value"/>
			<xsl:with-param name="labCalendar" select="$labCalendar"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'textDatetime'">
		<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
		<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
		<xsl:call-template name="datum">
			<xsl:with-param name="name" select="$name"/>
			<xsl:with-param name="value" select="$value"/>
			<xsl:with-param name="labCalendar" select="$labCalendar"/>
			<xsl:with-param name="hasTime" select="$tipPolja"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'check'">
		<xsl:call-template name="checkBox">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="value" select="$vrednostPolja"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'labela'">
		<xsl:call-template name="labela">
			<xsl:with-param name="value" select="$vrednostPolja"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'intLink'">
		<xsl:call-template name="intLink">
			<xsl:with-param name="style">width:<xsl:value-of select="$link-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="straId" select="$straId"/>
			<xsl:with-param name="labCreateLinkOnPage" select="$labCreateLinkOnPage"/>
			<xsl:with-param name="labCreateLinkOnBlock" select="$labCreateLinkOnBlock"/>
			<xsl:with-param name="labBrowseServer" select="$labBrowseServer"/>
			<xsl:with-param name="labSelectedLinkPreview" select="$labSelectedLinkPreview"/>
			<xsl:with-param name="dirPretrage" select="$dirPretrage"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'versionList' or $tipPolja = 'sectionList' or $tipPolja = 'pageList'">
		<xsl:variable name="type"><xsl:choose>
			<xsl:when test="$tipPolja = 'versionList'">verzija</xsl:when>
			<xsl:when test="$tipPolja = 'sectionList'">sekcija</xsl:when>
			<xsl:otherwise>stranica</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:call-template name="SMObjectList">
			<xsl:with-param name="style">width:<xsl:value-of select="$link-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="type" select="$type"/>
			<xsl:with-param name="straId" select="$straId"/>
			<xsl:with-param name="labSelect" select="$labSelect"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'file'">
		<xsl:call-template name="file">
			<xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="width" select="$sirinaPolja"/>
			<xsl:with-param name="height" select="$visinaPolja"/>
			<xsl:with-param name="labBrowseServer" select="$labBrowseServer"/>
			<xsl:with-param name="labSelectedImagePreview" select="$labSelectedImagePreview"/>
			<xsl:with-param name="dirPretrage" select="$dirPretrage"/>
		</xsl:call-template>
	</xsl:when>
        
	<xsl:when test="$tipPolja = 'fileImage'">
		<xsl:call-template name="fileImage">
			<xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="width" select="$sirinaPolja"/>
			<xsl:with-param name="height" select="$visinaPolja"/>
			<xsl:with-param name="labBrowseServer" select="$labBrowseServer"/>
			<xsl:with-param name="labSelectedImagePreview" select="$labSelectedImagePreview"/>
			<xsl:with-param name="type" select="$dirPretrage"/>
			<xsl:with-param name="dirPretrage" select="$dirPretrage"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'folder'">
		<xsl:call-template name="folder">
			<xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="width" select="$sirinaPolja"/>
			<xsl:with-param name="height" select="$visinaPolja"/>
			<xsl:with-param name="labBrowseServer" select="$labBrowseServer"/>
			<xsl:with-param name="labSelectedImagePreview" select="$labSelectedImagePreview"/>
			<xsl:with-param name="dirPretrage" select="$dirPretrage"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja = 'html-editor'">
		<xsl:call-template name="htmlEditor">
			<xsl:with-param name="width" select="$html-editor-width"/>
			<xsl:with-param name="height" select="$html-editor-height"/>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="label" select="$labelaPolja"/>
			<xsl:with-param name="showSimple" select="$show-simple-html-editor"/>
			<xsl:with-param name="labRichTextFormat" select="$labRichTextFormat"/>
		</xsl:call-template>
	</xsl:when>
		
	<xsl:when test="$tipPolja='select'">
		<xsl:variable name="selected">
			<xsl:choose>
				<xsl:when test="($vrednostPolja = '') and (@default != '')"><xsl:value-of select="@default"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$vrednostPolja"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:call-template name="select">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="style">width:<xsl:value-of select="$select-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="to-be-divided" select="$sveVrednosti"/>
			<xsl:with-param name="labels" select="$sveLabele"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja='radio'">
		<xsl:variable name="selected">
			<xsl:choose>
				<xsl:when test="($vrednostPolja = '') and (@default != '')"><xsl:value-of select="@default"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$vrednostPolja"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:call-template name="radio">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="to-be-divided" select="$sveVrednosti"/>
			<xsl:with-param name="labels" select="$sveLabele"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja='complex'">
		<xsl:call-template name="complex">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="style">width:<xsl:value-of select="$complex-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="to-be-divided" select="$sveVrednosti"/>
			<xsl:with-param name="labels" select="$sveLabele"/>
			<xsl:with-param name="selected" select="$vrednostPolja"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
		</xsl:call-template>		
	</xsl:when>

	<xsl:when test="$tipPolja='foreignKey'">
		<xsl:call-template name="foreignKey">
			<xsl:with-param name="to-be-divided" select="$sveLabele"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
			<xsl:with-param name="start" select="$startIndex"/>
			<xsl:with-param name="selName" select="$imePolja"/>
			<xsl:with-param name="counter" select="1"/>
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="type" select="$podtip"/>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="restrict" select="$restrict"/>
			<xsl:with-param name="chooseLabels" select="$sveLabele"/>
		</xsl:call-template>		
	</xsl:when>

	<xsl:when test="$tipPolja='fkAutoComplete'">
		<xsl:call-template name="fkAutoComplete">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="type" select="$podtip"/>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="label" select="$value_label"/>
			<xsl:with-param name="restrict" select="$restrict"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:when test="$tipPolja='imgPosition'">
		<xsl:call-template name="imgPosition">
			<xsl:with-param name="name" select="$imePolja"/>
			<xsl:with-param name="value" select="$vrednostPolja"/>
			<xsl:with-param name="default" select="@default"/>
		</xsl:call-template>	
	</xsl:when>
	
	<xsl:when test="$tipPolja = 'hidden'">
		<xsl:variable name="name"><xsl:value-of select="$imePolja"/></xsl:variable>
		<xsl:variable name="value"><xsl:value-of select="$vrednostPolja"/></xsl:variable>
		<xsl:call-template name="hidden">
			<xsl:with-param name="value" select="$value"/>
			<xsl:with-param name="name" select="$name"/>
		</xsl:call-template>
	</xsl:when>

	<xsl:otherwise/>
</xsl:choose>

<!-- KRAJ DELA KOJI JE PRE BIO U MATCH TEMPLATE-U -->
 </xsl:template>
 </xsl:stylesheet> 