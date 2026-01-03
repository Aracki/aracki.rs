<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:import href="../../config/forms_config_sm.xsl"/>
    <xsl:import href="templates_named.xsl"/>
    <xsl:import href="slika.xsl"/>
    <xsl:import href="link.xsl"/>
    <!--IMPORT correct.xsl JE NEOPHODAN, U SUPROTNOM STVAR SA IMPORTOVANIM XSL-OVIMA NE RADI-->
    <xsl:import href="correct.xsl"/>

    <xsl:output method="html"/>
    <xsl:template match="blok">
        <xsl:variable name="deca"><xsl:value-of select="count(./*)"/></xsl:variable>
        <xsl:variable name="brojDeceBezImporta"><xsl:value-of select="count(*[name() != 'import'])"/></xsl:variable>
        <xsl:variable name="brojDeceImporta"><xsl:value-of select="count(*[name() = 'import'])"/></xsl:variable>

        <div id="ocp_main_edit_bloka_table">
            <form id="formObject" name="formObject" method="post" class="ocp_tekst1" onSubmit="return validate();">
                <xsl:attribute name="action"><xsl:value-of select="@action"/>?random=<xsl:value-of select="./@random"/></xsl:attribute>
                <xsl:call-template name="blockInfo"/>
                <xsl:choose>
                    <xsl:when test="((@dinamic != '1') or (@dinamic='1' and @tip = 'Include') or (@dinamic='1' and $deca > '1'))">
                        <xsl:if test="($brojDeceBezImporta+$brojDeceImporta) != 0">
                            <table class="ocp_naslovgrupe_table">
                                <tr>
                                    <td class="ocp_naslovgrupe_td_a"><xsl:value-of select="@labGeneralOptions"/></td>
                                </tr>
                            </table>
                        </xsl:if>
                        <xsl:if test="$brojDeceBezImporta != 0">
                            <table class="ocp_opcije_table" border="0">
                                <tr>
                                    <td class="ocp_opcije_td_ikona">
                                        <xsl:attribute name="rowspan"><xsl:value-of select="$brojDeceBezImporta+1"/></xsl:attribute>
                                        <img src="/ocp/img/opsti/opcije/ikone/ikona_tekst.gif">
                                            <xsl:attribute name="title"><xsl:value-of select="@labText"/></xsl:attribute>
                                        </img>
                                    </td>
                                    <td colspan="2" class="ocp_opcije_td_naslov"><xsl:value-of select="@labText"/></td>
                                </tr>
                                <xsl:for-each select="*[name() != 'import']">
                                    <xsl:variable name="namePolja">
                                        <xsl:if test="name() = 'param'"><xsl:value-of select="@name"/></xsl:if>
                                        <xsl:if test="name() != 'param'"><xsl:value-of select="name()"/></xsl:if>
                                    </xsl:variable>
                                    <xsl:variable name="necessary">
                                        <xsl:choose>
                                            <xsl:when test="contains(@validate, 'is_necessary')">true</xsl:when>
                                            <xsl:otherwise>false</xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:variable>
                                    <tr>
                                        <xsl:if test="@inputType != 'html-editor'">
                                            <td align="left" class="ocp_opcije_td" style="width:22%">
                                                <span class="ocp_opcije_tekst1"><xsl:value-of select="@label"/></span>
                                                <xsl:if test="$necessary = 'true'">
                                                    <span class="ocp_opcije_obavezno">*</span>
                                                </xsl:if>
                                            </td>
                                        </xsl:if>
                                        <td align="left" class="ocp_opcije_td">
                                            <xsl:if test="@inputType = 'html-editor'">
                                                <xsl:attribute name="colspan">2</xsl:attribute>
                                            </xsl:if>
                                            <xsl:call-template name="polje">
                                                <xsl:with-param name="imePolja" select="$namePolja"/>
                                                <xsl:with-param name="tipPolja" select="@inputType"/>
                                                <xsl:with-param name="labelaPolja" select="@label"/>
                                                <xsl:with-param name="vrednostPolja" select="."/>
                                                <xsl:with-param name="value_label" select="@value_label"/>
                                                <xsl:with-param name="dirPretrage" select="@root"/>
                                                <xsl:with-param name="sirinaPolja" select="@width"/>
                                                <xsl:with-param name="visinaPolja" select="@height"/>
                                                <xsl:with-param name="sveVrednosti" select="@allvalues"/>
                                                <xsl:with-param name="sveLabele" select="@alllabels"/>
                                                <xsl:with-param name="straId" select="../@Stra_Id"/>
                                                <xsl:with-param name="podtip" select="@podtip"/>
                                                <xsl:with-param name="restrict" select="@where"/>
                                                <xsl:with-param name="startIndex" select="@startIndex"/>
                                                <xsl:with-param name="labCalendar" select="../@labCalendar"/>
                                                <xsl:with-param name="labCreateLinkOnPage" select="../@labCreateLinkOnPage"/>
                                                <xsl:with-param name="labCreateLinkOnBlock" select="../@labCreateLinkOnBlock"/>
                                                <xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
                                                <xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
                                                <xsl:with-param name="labSelectedLinkPreview" select="../@labSelectedLinkPreview"/>
                                                <xsl:with-param name="labRichTextFormat" select="../@labRichTextFormat"/>
                                                <xsl:with-param name="labColorPallete" select="../@labColorPallete"/>
                                                <xsl:with-param name="labSelect" select="../@labSelect"/>
                                            </xsl:call-template>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </table>
                        </xsl:if>

                        <!--UBACUJEMO IMPORT XSL-OVA-->
                        <xsl:apply-templates/>


                        <!--NAPREDNE OPCIJE-->
                        <table class="ocp_naslovgrupe_table">
                            <tr>
                                <td class="ocp_naslovgrupe_td_a"  style="cursor:pointer">
                                    <xsl:attribute name="onClick">alternateAdvanceDiv('advancedDivId', '<xsl:value-of select="@labOpen"/>', '<xsl:value-of select="@labClose"/>');</xsl:attribute>
                                    <xsl:value-of select="@labAdditionalOptions"/>
                                </td>
                                <td class="ocp_naslovgrupe_td_b" id="advancedDivId" style="cursor:pointer">
                                    <xsl:attribute name="onClick">alternateAdvanceDiv('advancedDivId', '<xsl:value-of select="@labOpen"/>', '<xsl:value-of select="@labClose"/>');</xsl:attribute>
                                    <a href="#" class="ocp_grupa_zatvori">
                                        <xsl:attribute name="onClick">alternateAdvanceDiv('advancedDivId', '<xsl:value-of select="@labOpen"/>', '<xsl:value-of select="@labClose"/>');return false;</xsl:attribute>
                                        <xsl:choose>
                                            <xsl:when test="($brojDeceBezImporta+$brojDeceImporta) = 0"><xsl:value-of select="@labClose"/><img src="/ocp/img/opsti/kontrole/strelica_nagore.gif" hspace="5" border="0"/></xsl:when>
                                            <xsl:otherwise><xsl:value-of select="@labOpen"/><img src="/ocp/img/opsti/kontrole/strelica_nadole.gif" hspace="5" border="0"/></xsl:otherwise>
                                        </xsl:choose>
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <div id="ocpAdvancedDiv">
                            <xsl:choose>
                                <xsl:when test="($brojDeceBezImporta+$brojDeceImporta) = 0"><xsl:attribute name="style">visibility:visible;display:block</xsl:attribute></xsl:when>
                                <xsl:otherwise><xsl:attribute name="style">visibility:hidden;display:none</xsl:attribute></xsl:otherwise>
                            </xsl:choose>
                            <!--deo ubacen za datum izdavanja i isticanja-->
                            <table class="ocp_opcije_table">
                                <tr>
                                    <td rowspan="5" class="ocp_opcije_td_ikona">
                                        <img src="/ocp/img/opsti/opcije/ikone/ikona_kalendar.gif">
                                            <xsl:attribute name="title"><xsl:value-of select="@labPeriodOfVisibility"/></xsl:attribute>
                                        </img>
                                    </td>
                                    <td colspan="2" class="ocp_opcije_td_naslov"><xsl:value-of select="@labPeriodOfVisibility"/></td>
                                </tr>
                                <tr>
                                    <td class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><xsl:value-of select="@labFrom"/> (dd/mm/yyyy hh:mm:ss)</span></td>
                                    <td class="ocp_opcije_td">
                                        <xsl:call-template name="datum">
                                            <xsl:with-param name="name">Blok_PublishDate</xsl:with-param>
                                            <xsl:with-param name="value" select="@Blok_PublishDate"/>
                                            <xsl:with-param name="labCalendar" select="@labCalendar"/>
                                            <xsl:with-param name="hasTime">textDatetime</xsl:with-param>
                                        </xsl:call-template>
                                    </td>
                                </tr>
                                <tr>
                                    <td  class="ocp_opcije_td" style="width:22%"><span class="ocp_opcije_tekst1"><xsl:value-of select="@labTo"/> (dd/mm/yyyy hh:mm:ss)</span></td>
                                    <td class="ocp_opcije_td">
                                        <xsl:call-template name="datum">
                                            <xsl:with-param name="name">Blok_ExpiryDate</xsl:with-param>
                                            <xsl:with-param name="value" select="@Blok_ExpiryDate"/>
                                            <xsl:with-param name="labCalendar" select="@labCalendar"/>
                                            <xsl:with-param name="hasTime">textDatetime</xsl:with-param>
                                        </xsl:call-template>
                                    </td>
                                </tr>
                            </table>

                            <!--deo ubacen za sharovanje-->
                            <xsl:if test="(@Blok_Id != '') and (@Blok_Id != 'undefined')">
                                <table class="ocp_opcije_table">
                                    <tr>
                                        <td rowspan="5" class="ocp_opcije_td_ikona">
                                            <img src="/ocp/img/opsti/opcije/ikone/ikona_deljeni_blok.gif">
                                                <xsl:attribute name="title"><xsl:value-of select="@labSharingWithAnother"/></xsl:attribute>
                                            </img>
                                        </td>
                                        <td colspan="2" class="ocp_opcije_td_naslov">
                                            <xsl:value-of select="@labSharingWithAnother"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  class="ocp_opcije_td" style="width:22%">
                                            <span class="ocp_opcije_tekst1"><xsl:value-of select="@labShared"/></span>
                                        </td>
                                        <td class="ocp_opcije_td">
                                            <span class="ocp_opcije_tekst2"><xsl:value-of select="@labYes"/></span>
                                            <input name="Blok_Share" type="radio" value="1">
                                                <xsl:if test="@Blok_Share = '1'">
                                                    <xsl:attribute name="checked"/>
                                                </xsl:if>
                                            </input>
                                            <span class="ocp_opcije_tekst2"><xsl:value-of select="@labNo"/></span>
                                            <input name="Blok_Share" type="radio" value="0">
                                                <xsl:if test="@Blok_Share != '1'">
                                                    <xsl:attribute name="checked"/>
                                                </xsl:if>
                                            </input>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  class="ocp_opcije_td" style="width:22%">
                                            <span class="ocp_opcije_tekst1"><xsl:value-of select="@labUnderName"/></span>
                                        </td>
                                        <td class="ocp_opcije_td">
                                            <input type="text" class="ocp_forma" name="Blok_MetaNaziv" style="width:100%">
                                                <xsl:attribute name="value">
                                                    <xsl:choose>
                                                        <xsl:when test="@Blok_MetaNaziv='null'"></xsl:when>
                                                        <xsl:otherwise>
                                                            <xsl:value-of select="@Blok_MetaNaziv"/>
                                                        </xsl:otherwise>
                                                    </xsl:choose>
                                                </xsl:attribute>
                                            </input>
                                        </td>
                                    </tr>
                                </table>
                            </xsl:if>
                        </div>
                    </xsl:when>
                    <xsl:otherwise>

                        <!--PRIKAZ KADA NEMA POLJA-->
                        <table width="100%">
                            <tr>
                                <td class="ocp_text"><xsl:value-of select="@labNoParams"/></td>
                            </tr>
                        </table>
                    </xsl:otherwise>
                </xsl:choose>
                <!--deo ubacen za dinamicke linkove sa linkom na uredjivanje objekata-->
<!--		<xsl:if test="@dinamic='1' and @type and @id">
                        <table>
                                <tr>
                                        <td colspan="3" class="ocp_tekst1">
                                                <a class="ocp_link"><xsl:attribute name="href">javascript:openPopup('<xsl:value-of select="@id"/>', '<xsl:value-of select="@type"/>')</xsl:attribute>
                                                <xsl:value-of select="@labEdit"/> <xsl:value-of select="@type"/></a>
                                        </td>
                                </tr>
                        </table>
                </xsl:if>-->
                <table width="100%">
                    <tr>
                        <td height="40" align="center" class="ocp_text">
                            <input type="submit" class="ocp_dugme">
                                <xsl:attribute name="value"><xsl:value-of select="@labSave"/></xsl:attribute>
                            </input>
                            <input type="button" class="ocp_dugme" style="margin-left: 3px">
                                <xsl:attribute name="value"><xsl:value-of select="@labCancel"/></xsl:attribute>
                                <xsl:attribute name="onclick">window.open('/ocp/siteManager/blokoviedit.php?random=<xsl:value-of select="./@random"/>&amp;Stra_Id=<xsl:value-of select="@Stra_Id"/>', '_self');</xsl:attribute>
                            </input>
                        </td>
                    </tr>
                </table>
                <xsl:call-template name="hidden">
                    <xsl:with-param name="value" select="@Stra_Id"/>
                    <xsl:with-param name="name" select="'Stra_Id'"/>
                </xsl:call-template>
                <xsl:call-template name="hidden">
                    <xsl:with-param name="value" select="@Blok_Id"/>
                    <xsl:with-param name="name" select="'Blok_Id'"/>
                </xsl:call-template>
                <xsl:call-template name="hidden">
                    <xsl:with-param name="value" select="@Blok_LastModify"/>
                    <xsl:with-param name="name" select="'Blok_LastModify'"/>
                </xsl:call-template>
                <xsl:call-template name="hidden">
                    <xsl:with-param name="value" select="@TipB_Id"/>
                    <xsl:with-param name="name" select="'TipB_Id'"/>
                </xsl:call-template>
                <xsl:if test="@StBl_Id">
                    <xsl:call-template name="hidden">
                        <xsl:with-param name="value" select="@StBl_Id"/>
                        <xsl:with-param name="name" select="'StBl_Id'"/>
                    </xsl:call-template>
                </xsl:if>
            </form>
        </div>
    </xsl:template>

    <xsl:template match="import">
        <xsl:apply-imports/>
    </xsl:template>

</xsl:stylesheet> 