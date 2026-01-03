<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="fkAutoComplete">
	<xsl:param name="name"/>
	<xsl:param name="type"/>
	<xsl:param name="value"/>
	<xsl:param name="label"/>
	<xsl:param name="restrict"/>
	
	<input type="hidden" name="{$name}" value="{$value}"/>

	<table border="0" cellpadding="0" cellspacing="0" style="width:450px;">
		<tr>
			<td align="left">
				<select id="{$name}_list" name="{$name}_list" style="width:200px;" onchange="updateParent();">
					<option value="{$value}"><xsl:value-of select="$label"/></option>
				</select>
			</td>
		</tr>
	</table>


	<script language="JavaScript">
		var ac_<xsl:value-of select="$name"/>=new dhtmlXComboFromSelect('<xsl:value-of select="$name"/>_list');
		ac_<xsl:value-of select="$name"/>.enableFilteringMode(true, '/ocp/controls/auto_complete/data.php?type=<xsl:value-of select="$type"/>&amp;fieldName=<xsl:value-of select="$name"/>&amp;fieldValue=<xsl:value-of select="$value"/>&amp;restrict=<xsl:value-of select="$restrict"/>');

		function updateParent(){
			document.formObject.<xsl:value-of select="$name"/>.value =  ac_<xsl:value-of select="$name"/>.getSelectedValue();
		}
	</script>
	
 </xsl:template>

 </xsl:stylesheet> 