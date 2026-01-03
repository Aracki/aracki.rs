<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="intLink">
	<xsl:param name="style"/>
	<xsl:param name="value"/>
	<xsl:param name="name"/>
	<xsl:param name="straId"/>
	<xsl:param name="labCreateLinkOnPage"/>
	<xsl:param name="labCreateLinkOnBlock"/>
	<xsl:param name="labBrowseServer"/>
	<xsl:param name="labSelectedLinkPreview"/>
	<xsl:param name="dirPretrage"/>

	<table class="ocp_uni_table">
		<tr>
			<td class="ocp_dugmici_td_levi">
				<input type="text" class="ocp_forma" style="{$style}" name="{$name}" value="{$value}"/>
			</td>
			<td class="ocp_dugmici_td_desni_4">
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">window.open('/ocp/admin/siteManager/intlink.php?random=<xsl:value-of select="../@random"/>&amp;Id=<xsl:value-of select="$straId"/>&amp;field=document.formObject.<xsl:value-of select="$name"/>', 'intLink', 'top=100, left=150, width=600, height=260, scrollbars=no, resizable=no, status=yes');return false;</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_browse_page.gif" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labCreateLinkOnPage"/></xsl:attribute>
					</img>
				</a>
				<a>
					<xsl:attribute name="href">javascript:x = window.open('/ocp/controls/fileControl/frameset.php?random=<xsl:value-of select="../@random"/>&amp;basicFolder=<xsl:value-of select="$value"/>&amp;root=<xsl:value-of select="$dirPretrage"/>&amp;field=document.formObject.<xsl:value-of select = "$name"/>','imgKontrola','top=100, left=50, width=760, height=560, scrollbars=yes, resizable=yes, status=yes'); x.focus();</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_browse.gif" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labBrowseServer"/></xsl:attribute>
					</img>
				</a>
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">window.open('/ocp/controls/advanced_editor/chooseblok.php?random=<xsl:value-of select="../@random"/>&amp;Stra_Id='+document.formObject.<xsl:value-of select="$name"/>.value+'&amp;field=document.formObject.<xsl:value-of select="$name"/>', 'intLinkBlock', 'top=100, left=150, width=600, height=260, scrollbars=yes, resizable=no, status=yes');return false;</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_browse_blok.gif" width="20" height="21" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labCreateLinkOnBlock"/></xsl:attribute>
					</img>
				</a>
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">urlCont=document.formObject.<xsl:value-of select="$name"/>;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars' );
					</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labSelectedLinkPreview"/></xsl:attribute>
					</img>
				</a>
			</td>
		</tr>
	</table>

 </xsl:template>

  <xsl:template name="SMObjectList">
	<xsl:param name="style"/>
	<xsl:param name="value"/>
	<xsl:param name="name"/>
	<xsl:param name="type"/>
	<xsl:param name="straId"/>
	<xsl:param name="labSelect"/>

	<table class="ocp_uni_table">
		<tr>
			<td class="ocp_dugmici_td_levi">
				<input type="text" class="ocp_forma" style="{$style};width:28px;" name="{$name}" value="{$value}" onchange="update{$name}_label(this.value)"/>
				<div id="{$name}_label" style="display:inline;margin-left:10px;"></div>
			</td>
			<td class="ocp_dugmici_td_desni_2">
				<a href="javascript: void(0);">
					<xsl:attribute name="onClick">window.open('/ocp/admin/siteManager/smobject_list.php?random=<xsl:value-of select="../@random"/>&amp;Id=<xsl:value-of select="$straId"/>&amp;type=<xsl:value-of select="$type"/>&amp;field=document.formObject.<xsl:value-of select="$name"/>', 'intLink', 'top=100, left=150, width=600, height=260, scrollbars=no, resizable=no, status=yes');return false;</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_browse_page.gif" class="ocp_kontrola">
						<xsl:attribute name="title"><xsl:value-of select="$labSelect"/></xsl:attribute>
					</img>
				</a>
			</td>
		</tr>
	</table>

	<script src="/ocp/jscript/prototype.js" type="text/javascript"><img src="/ocp/img/blank.gif"/></script>

	<script type="text/javascript">
		function update<xsl:value-of select="$name"/>_label(fieldValue){
			var targetDiv = '<xsl:value-of select="$name"/>_label';
			var element = $(targetDiv);

			var url = '/ocp/admin/siteManager/smobject_label.php';	

			var ajax = new Ajax.Updater(
				{success: targetDiv},
				url,
				{	method: 'get', parameters: 'type=<xsl:value-of select="$type"/>&amp;fieldValue='+fieldValue, asynchronous:false, evalScripts:false,
					onLoading:function(request, json){}}
			);
		}
		
		update<xsl:value-of select="$name"/>_label(<xsl:value-of select="$value"/>);

	</script>

 </xsl:template>

 </xsl:stylesheet> 