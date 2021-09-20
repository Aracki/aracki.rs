<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:import href="../../styles/complex.xsl"/>
<xsl:import href="../../styles/datum.xsl"/>
<xsl:import href="../../styles/editGroups.xsl"/>
<xsl:import href="../../styles/textBox.xsl"/>
<xsl:import href="../../styles/textArea.xsl"/>
<xsl:import href="../../styles/color.xsl"/>
<xsl:import href="../../styles/select.xsl"/>
<xsl:import href="../../styles/checkBox.xsl"/>
<xsl:import href="../../styles/radio.xsl"/>
<xsl:import href="../../styles/hidden.xsl"/>
<xsl:import href="../../styles/fileImage.xsl"/>
<xsl:import href="../../styles/file.xsl"/>
<xsl:import href="../../styles/folder.xsl"/>
<xsl:import href="../../styles/intLink.xsl"/>
<xsl:import href="../../styles/hidden.xsl"/>
<xsl:import href="../../styles/htmlEditor.xsl"/>
<xsl:import href="../../styles/foreignKey.xsl"/>
<xsl:import href="../../styles/fkAutoComplete.xsl"/>

<xsl:output method="html"/>

<xsl:template name="firstCell">
	<xsl:param name="label"/>
	<xsl:param name="necessary"/>
	
	<td class="ocp_opcije_td" style="width:22%">
		<span class="ocp_opcije_tekst1"><xsl:value-of select="$label"/></span>
		<xsl:if test="$necessary = 'true'"><span class="ocp_opcije_obavezno">*</span></xsl:if>
	</td>
 </xsl:template>

 <xsl:template name="thirdCell">
	<xsl:param name="action"/>
	<xsl:param name="name"/>

	<xsl:if test="($action != 'form.php' and $action != 'subforms.php')">
		<td class="ocp_opcije_td_forma" style="text-align:center; width: 70px;">
			<input type="radio" name="sortName">
				<xsl:attribute name="value"><xsl:value-of select="$name"/></xsl:attribute>
			</input>
		</td>
	</xsl:if>
 </xsl:template>

 <xsl:template name="action">
	<xsl:param name="url"/>
	<xsl:param name="image"/>
	<xsl:param name="label"/>
	<xsl:param name="place"/>

	<xsl:variable name="type"><xsl:value-of select="/asset/fields/@type"/></xsl:variable>
	<xsl:variable name="id"><xsl:value-of select="/asset/fields/field[name='Id']/value"/></xsl:variable>

	<xsl:choose>
		<xsl:when test="$place = 'button'">
			
			<xsl:choose>
				<xsl:when test="$image != ''">
					<input type="button" name="{$url}{$place}" class="ocp_dugme" style="background:url('{$image}')">
						<xsl:attribute name="onclick"><xsl:value-of select="$url"/>('<xsl:value-of select="$type"/>','<xsl:value-of select="$id"/>'); return false;</xsl:attribute>
					</input>
				</xsl:when>
				<xsl:otherwise>
					<input type="button" name="{$url}{$place}" class="ocp_dugme">
						<xsl:attribute name="value"><xsl:value-of select="$label"/></xsl:attribute>
						<xsl:attribute name="onclick"><xsl:value-of select="$url"/>('<xsl:value-of select="$type"/>','<xsl:value-of select="$id"/>')</xsl:attribute>
					</input>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="$image != ''">
					<img src="{$image}" border="0" style="cursor:pointer;">
						<xsl:attribute name="onclick"><xsl:value-of select="$url"/>('<xsl:value-of select="$type"/>','<xsl:value-of select="$id"/>');return false;</xsl:attribute>
						<xsl:if test="$label != '' and $label != '!'"><xsl:attribute name="title"><xsl:value-of select="$label"/></xsl:attribute></xsl:if>
					</img>
				</xsl:when>
				<xsl:otherwise>
					<a href="#" style="color:#666;">
						<xsl:attribute name="onclick"><xsl:value-of select="$url"/>('<xsl:value-of select="$type"/>','<xsl:value-of select="$id"/>');return false;</xsl:attribute>
						<xsl:value-of select="$label"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

</xsl:stylesheet> 