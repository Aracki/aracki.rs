<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:import href="templates_named.xsl"/>
    <xsl:import href="../../config/forms_config.xsl"/>
    <xsl:output method="html"/>

    <xsl:template match="asset/fields">

        <xsl:for-each select="../actions/action[contains(@place,'formheader') or contains(@place,'button')]">
            <script src="/ocp/jscript/actions/{@url}.js" type="text/javascript"><img src="/ocp/img/blank.gif" width="0"/></script>
        </xsl:for-each>

        <xsl:variable name="divId">ocp_main_table</xsl:variable>

        <div id="{$divId}">
            <xsl:if test="./@action = 'form.php'">
                <form name="formObject" id="formObject" method="post" onSubmit="return validate();" action="{@action}">
                    <input type="hidden" name="ocpType" value="{@type}"/>
                    <input type="hidden" name="EditGroup" value="{@editGroup}"/>
                    <table class="ocp_naslov_table">
                        <tr>
                            <td class="ocp_naslov_td">
                                <xsl:value-of select="./@labHeader"/>
                            </td>
                            <xsl:for-each select="../actions/action[contains(@place,'formheader')]">
                                <td class="ocp_naslov_td" align="right">
                                    <xsl:call-template name="action">
                                        <xsl:with-param name="label" select="@label"/>
                                        <xsl:with-param name="url" select="@url"/>
                                        <xsl:with-param name="image" select="@image"/>
                                        <xsl:with-param name="place" select="'formheader'"/>
                                    </xsl:call-template>
                                </td>
                            </xsl:for-each>
                        </tr>
                    </table>
                    <xsl:choose>
                        <xsl:when test="@editGroups != ''">
                            <table class="ocp_opcije_table">
                                <tr>
                                    <td class="ocp_opcije_td_naslov" align="right">
                                        <span class="ocp_opcije_tekst1"><strong><xsl:value-of select="./@labGroups"/>:</strong></span>
                                        <select name="editGroupMenu" class="ocp_forma" style="margin-left:5px; width:128px" onChange="openEditGroup(value)">
                                            <option value=""><xsl:value-of select="./@labGeneral"/></option>
                                            <xsl:call-template name="editGroups">
                                                <xsl:with-param name="editGroups" select="@editGroups"/>
                                                <xsl:with-param name="editGroupsLabels" select="@editGroupsLabels"/>
                                                <xsl:with-param name="selected" select="./@editGroup"/>
                                            </xsl:call-template>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </xsl:when>
                    </xsl:choose>
                    <table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
                        <xsl:apply-templates/>
                    </table>

                    <xsl:if test="./@right = '2' or ./@right = '3' or ./@right = '4'">
                        <table width="100%">
                            <tr>
                                <td height="40" align="center" class="ocp_text">
                                    <input type="submit" name="submitSave" style="visibility:visible" class="ocp_dugme" value="{@labSave}"/>
                                    <img src="/ocp/img/blank.gif" width="3"/>
                                    <input type="button" name="submitCancel" class="ocp_dugme" value="{@labCancel}"  onclick="parent.menuFrame.showSubmenuClose(true, true);"  style="visibility:visible"/>

                                    <xsl:for-each select="../actions/action[contains(@place,'button')]">
                                        <img src="/ocp/img/blank.gif" width="3"/>
                                        <xsl:call-template name="action">
                                            <xsl:with-param name="label" select="@label"/>
                                            <xsl:with-param name="url" select="@url"/>
                                            <xsl:with-param name="image" select="@image"/>
                                            <xsl:with-param name="place" select="'button'"/>
                                        </xsl:call-template>
                                    </xsl:for-each>
                                </td>
                            </tr>
                        </table>
                    </xsl:if>
                </form>
            </xsl:if>

            <!--ovo je iz query.php-->

            <xsl:if test="./@action = 'objects.php'">
                <form name="formObject" id="formObject" method="post" action="{@action}" onSubmit="validate_double_quotes(document.formObject); return true;">
                    <input type="hidden" name="ocp_brojac" value="0"/>
                    <input type="hidden" name="typeId" value="{@typeId}"/>
                    <input name="objId" type="hidden" value="{@objId}"/>
                    <input name="ocpDefaultValues" type="hidden" value="{@ocpDefaultValues}"/>

                    <table class="ocp_blokovi_table">
                        <tr>
                            <td class="ocp_blokovi_td" style="padding:4px 0px 4px 6px;">
                                <xsl:value-of select="./@labHeader"/>
                            </td>
                            <td class="ocp_blokovi_td" style="width:70px;text-align:center;">
                                <span style="color: #C42E00;"><xsl:value-of select="./@labSort"/></span>
                            </td>
                        </tr>
                    </table>
                    <table class="ocp_opcije_table">
                        <xsl:apply-templates/>
                        <tr>
                            <td class="ocp_opcije_td_forma" style="width:22%">
                                <span class="ocp_opcije_tekst1"><xsl:value-of select="./@labDisplay"/></span>
                            </td>
                            <td class="ocp_opcije_td_forma"><span class="ocp_opcije_tekst2">
                                    <input type="text" value="50" maxlength="3" size="3" name="ocp_broj" class="ocp_forma" style="width:30px"/>
                                    <img src="/ocp/img/blank.gif" width="3"/>
                                <xsl:value-of select="./@labObjectsPerPage"/></span>
                            </td>
                            <td class="ocp_opcije_td_forma" style="text-align:center;"><img src="/ocp/img/blank.gif" width="3"/></td>
                        </tr>
                    </table>
                    <table width="100%">
                        <tr>
                            <td height="40" align="center" class="ocp_text">
                                <input type="submit" name="submit2" class="ocp_dugme" value="{@labSearch}"/>
                                <img src="/ocp/img/blank.gif" width="3"/>
                                <input type="button" name="button" class="ocp_dugme"  value="{@labCancel}" onclick="parent.menuFrame.defaultPage();"/>
                            </td>
                        </tr>
                    </table>
                </form>
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template match="field">
        <!--Start section varijable-->
        <xsl:variable name="show">
            <xsl:choose>
                <xsl:when test="((../@action = 'form.php') or (../@action != 'form.php' and @iden = '1'))">true</xsl:when>
                <xsl:otherwise>false</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="necessary">
            <xsl:choose>
                <xsl:when test="(../@action = 'form.php') and contains(validate, 'is_necessary')">true</xsl:when>
                <xsl:otherwise>false</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <!--End section varijable-->

        <xsl:if test="@max != '' and (inputType != 'hidden') and (../@action = 'form.php' or ../@action = 'relform.php')">
            <tr>
                <td class="ocp_opcije_td_header ocp_opcije_tekst4" colspan="2">
                    <span class="ocp_opcije_tekst1"><b><xsl:value-of select="@max"/></b></span>
                </td>
            </tr>
        </xsl:if>

        <xsl:choose>

            <xsl:when test="$show='true' and (@import != '') and (../@action = 'form.php')">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <input type="hidden" name="{name}" value="{value}"/>
                        <iframe width="100%" height="18" frameborder="0" scrolling="no" src="{@import}?id={../field[name='Id']/value}&amp;type={../@type}" id="import_{name}">
                            <img src="/ocp/img/blank.gif"/>
                        </iframe>
                    </td>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='textBox' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:variable name="style">width:<xsl:value-of select="$text-field-width"/>px;</xsl:variable>
                        <xsl:call-template name="textBox">
                            <xsl:with-param name="style" select="$style"/>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="labAdvancedSearch" select="../@labAdvancedSearch"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='textarea' and $show='true'">

                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <xsl:if test="../@action!='objects.php'">
                        <td class="ocp_opcije_td">
                            <xsl:variable name="style">width:<xsl:value-of select="$text-area-width"/>px;height: <xsl:value-of select="$text-area-height"/>px;</xsl:variable>
                            <xsl:call-template name="textArea">
                                <xsl:with-param name="style" select="$style"/>
                                <xsl:with-param name="value" select="value"/>
                                <xsl:with-param name="name" select="name"/>
                            </xsl:call-template>
                        </td>
                    </xsl:if>
                    <xsl:if test="../@action='objects.php'">
                        <td class="ocp_opcije_td">
                            <xsl:variable name="style">width:<xsl:value-of select="$text-field-width"/>px;</xsl:variable>
                            <xsl:call-template name="textBox">
                                <xsl:with-param name="style" select="$style"/>
                                <xsl:with-param name="value" select="value"/>
                                <xsl:with-param name="name" select="name"/>
                                <xsl:with-param name="labAdvancedSearch" select="../@labAdvancedSearch"/>
                            </xsl:call-template>
                        </td>
                    </xsl:if>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="((inputType='textDate') or (inputType='textDatetime')) and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:if test="(../@action != 'form.php')">
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td nowrap="" valign="top">
                                        <span class="ocp_opcije_tekst2"><xsl:value-of select="../@labFrom"/></span><img src="/ocp/img/blank.gif" width="3"/>
                                        <xsl:call-template name="datum">
                                            <xsl:with-param name="name" select="concat(name,'_od')"/>
                                            <xsl:with-param name="value" select="value"/>
                                            <xsl:with-param name="labCalendar" select="../@labCalendar"/>
                                            <xsl:with-param name="hasTime"><xsl:value-of select="inputType"/></xsl:with-param>
                                        </xsl:call-template><img src="/ocp/img/blank.gif" width="7"/>
                                    </td>
                                    <td nowrap="" valign="top">
                                        <span class="ocp_opcije_tekst2"><xsl:value-of select="../@labTo"/></span>
                                        <img src="/ocp/img/blank.gif" width="3"/>
                                        <xsl:call-template name="datum">
                                            <xsl:with-param name="name" select="concat(name,'_do')"/>
                                            <xsl:with-param name="value" select="value"/>
                                            <xsl:with-param name="labCalendar" select="../@labCalendar"/>
                                            <xsl:with-param name="hasTime"><xsl:value-of select="inputType"/></xsl:with-param>
                                        </xsl:call-template>
                                    </td>
                                </tr>
                            </table>
                        </xsl:if>
                        <xsl:if test="(../@action = 'form.php')">
                            <xsl:call-template name="datum">
                                <xsl:with-param name="name" select="name"/>
                                <xsl:with-param name="value" select="value"/>
                                <xsl:with-param name="labCalendar" select="../@labCalendar"/>
                                <xsl:with-param name="hasTime" select="inputType"/>
                            </xsl:call-template>
                        </xsl:if>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='check' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="checkBox">
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="value" select="value"/>
                        </xsl:call-template>
                        <img src="/ocp/img/blank.gif" width="3" border="0"/>
                        <xsl:if test="( ../@action = 'objects.php')">
                            <span class="ocp_opcije_tekst2"><xsl:value-of select="../@labIncludeInSearch"/></span>
                            <xsl:call-template name="checkBox">
                                <xsl:with-param name="name"><xsl:value-of select="name"/>_include</xsl:with-param>
                                <xsl:with-param name="value">0</xsl:with-param>
                            </xsl:call-template>
                        </xsl:if>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='color' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:variable name="style">width:<xsl:value-of select="$color-field-width"/>px;</xsl:variable>
                        <xsl:call-template name="color">
                            <xsl:with-param name="style" select="$style"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="labColorPallete" select="../@labColorPallete"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>


            <xsl:when test="inputType='labela' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <span class="ocp_opcije_tekst2"><xsl:value-of select="value"/></span>
                        <xsl:choose>
                            <xsl:when test="value/@hiddenId != ''">
                                <xsl:call-template name="hidden">
                                    <xsl:with-param name="value" select="value/@hiddenId"/>
                                    <xsl:with-param name="name" select="name"/>
                                </xsl:call-template>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:call-template name="hidden">
                                    <xsl:with-param name="value" select="value"/>
                                    <xsl:with-param name="name" select="name"/>
                                </xsl:call-template>
                            </xsl:otherwise>
                        </xsl:choose>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="(inputType = 'file' or inputType= 'image') and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="file">
                            <xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="width" select="./@width"/>
                            <xsl:with-param name="height" select="./@height"/>
                            <xsl:with-param name="max" select="./@max"/>
                            <xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
                            <xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
                            <xsl:with-param name="dirPretrage" select="./@root"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="(inputType = 'fileImage') and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="fileImage">
                            <xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="type" select="../@type"/>
                            <xsl:with-param name="width" select="./@width"/>
                            <xsl:with-param name="height" select="./@height"/>
                            <xsl:with-param name="max" select="./@max"/>
                            <xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
                            <xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
                            <xsl:with-param name="dirPretrage" select="./@root"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType = 'folder' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="folder">
                            <xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="width" select="./@width"/>
                            <xsl:with-param name="height" select="./@height"/>
                            <xsl:with-param name="max" select="./@max"/>
                            <xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
                            <xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
                            <xsl:with-param name="dirPretrage" select="./@root"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="(inputType='link' or inputType='intLink') and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="intLink">
                            <xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="width" select="./@width"/>
                            <xsl:with-param name="height" select="./@height"/>
                            <xsl:with-param name="max" select="./@max"/>
                            <xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
                            <xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
                            <xsl:with-param name="dirPretrage" select="./@root"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="(inputType='versionList' or inputType='sectionList' or inputType='pageList') and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:variable name="type"><xsl:choose>
                                <xsl:when test="inputType= 'versionList'">verzija</xsl:when>
                                <xsl:when test="inputType= 'sectionList'">sekcija</xsl:when>
                                <xsl:otherwise>stranica</xsl:otherwise>
                        </xsl:choose></xsl:variable>
                        <xsl:call-template name="SMObjectList">
                            <xsl:with-param name="style">width:<xsl:value-of select="$link-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="type" select="$type"/>
                            <xsl:with-param name="labSelect" select="../@labSelect"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='html-editor' and $show='true'">
                <tr>
                    <xsl:if test="../@action!='objects.php'">
                        <td class="ocp_opcije_td" colspan="2">
                            <xsl:call-template name="htmlEditor">
                                <xsl:with-param name="width" select="$html-editor-width"/>
                                <xsl:with-param name="height" select="$html-editor-height"/>
                                <xsl:with-param name="value" select="value"/>
                                <xsl:with-param name="name" select="name"/>
                                <xsl:with-param name="label" select="@label"/>
                                <xsl:with-param name="showSimple" select="$show-simple-html-editor"/>
                                <xsl:with-param name="labRichTextFormat" select="../@labRichTextFormat"/>
                            </xsl:call-template>
                        </td>
                    </xsl:if>
                    <xsl:if test="../@action='objects.php'">
                        <xsl:call-template name="firstCell">
                            <xsl:with-param name="label" select="@label"/>
                            <xsl:with-param name="necessary" select="$necessary"/>
                        </xsl:call-template>
                        <td class="ocp_opcije_td">
                            <xsl:variable name="style">width:<xsl:value-of select="$text-field-width"/>px;</xsl:variable>
                            <xsl:call-template name="textBox">
                                <xsl:with-param name="style" select="$style"/>
                                <xsl:with-param name="value" select="value"/>
                                <xsl:with-param name="name" select="name"/>
                                <xsl:with-param name="labAdvancedSearch" select="../@labAdvancedSearch"/>
                            </xsl:call-template>
                        </td>
                    </xsl:if>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='radio' and $show='true'">
                <xsl:variable name="selected"><xsl:choose>
                        <xsl:when test="(value = '') and (@default != '')"><xsl:value-of select="@default"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="value"/></xsl:otherwise>
                </xsl:choose></xsl:variable>
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="radio">
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="to-be-divided" select="allvalues"/>
                            <xsl:with-param name="labels" select="alllabels"/>
                            <xsl:with-param name="selected" select="$selected"/>
                            <xsl:with-param name="delimiter" select="'|@$'"/>
                            <xsl:with-param name="editLink" select="@link"/>
                            <xsl:with-param name="showEditLink" select="'true'"/>
                            <xsl:with-param name="labUpdateListOfValue" select="../@labUpdateListOfValue"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='select' and $show='true'">
                <xsl:variable name="selected"><xsl:choose>
                        <xsl:when test="(value = '') and (@default != '')"><xsl:value-of select="@default"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="value"/></xsl:otherwise>
                </xsl:choose></xsl:variable>
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="select">
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="style">width:<xsl:value-of select="$select-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="to-be-divided" select="allvalues"/>
                            <xsl:with-param name="labels" select="alllabels"/>
                            <xsl:with-param name="selected" select="$selected"/>
                            <xsl:with-param name="delimiter" select="'|@$'"/>
                            <xsl:with-param name="editLink" select="@link"/>
                            <xsl:with-param name="showEditLink" select="'true'"/>
                            <xsl:with-param name="labUpdateListOfValue" select="../@labUpdateListOfValue"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="(inputType='complex'or inputType='upload') and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="foreignKey">
                            <xsl:with-param name="to-be-divided" select="chooseLabels"/>
                            <xsl:with-param name="delimiter" select="'|@$'"/>
                            <xsl:with-param name="start" select="startIndex"/>
                            <xsl:with-param name="selName" select="name"/>
                            <xsl:with-param name="counter" select="1"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="type" select="type"/>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="restrict" select="restrict"/>
                            <xsl:with-param name="chooseLabels" select="chooseLabels"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <xsl:when test="inputType='fkMultiple' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="complex">
                            <xsl:with-param name="to-be-divided" select="value"/>
                            <xsl:with-param name="selected" select="selected"/>
                            <xsl:with-param name="style">width:<xsl:value-of select="$complex-field-width"/>px;</xsl:with-param>
                            <xsl:with-param name="delimiter" select="'|@$'"/>
                            <xsl:with-param name="start" select="startIndex"/>
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="type" select="type"/>
                            <xsl:with-param name="labels" select="chooseLabels"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>



            <xsl:when test="inputType='fkAutoComplete' and $show='true'">
                <tr>
                    <xsl:call-template name="firstCell">
                        <xsl:with-param name="label" select="@label"/>
                        <xsl:with-param name="necessary" select="$necessary"/>
                    </xsl:call-template>
                    <td class="ocp_opcije_td">
                        <xsl:call-template name="fkAutoComplete">
                            <xsl:with-param name="name" select="name"/>
                            <xsl:with-param name="type" select="type"/>
                            <xsl:with-param name="value" select="value"/>
                            <xsl:with-param name="label" select="value_label"/>
                            <xsl:with-param name="restrict" select="where"/>
                        </xsl:call-template>
                    </td>
                    <xsl:call-template name="thirdCell">
                        <xsl:with-param name="action" select="../@action"/>
                        <xsl:with-param name="name" select="name"/>
                    </xsl:call-template>
                </tr>
            </xsl:when>

            <!--hidden nodovi (ovde su obradjene i subforme i include nodovi)-->

            <xsl:when test="inputType='hidden' and ../@action = 'form.php'">
                <!--ako ima subforms nodove-->
                <xsl:if test="../../*[name() = 'subforms'] and name='Id'">
                    <xsl:for-each select="../../*[name() = 'subforms']/*">
                        <tr>
                            <td style="padding:0px;" colspan="3">
                                <table class="ocp_subforma_naslov_table">
                                    <tr>
                                        <td class="ocp_subforma_naslov_td"><xsl:value-of select="@SubTypeLabel"/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="ocp_opcije_td" style="padding:0px;" colspan="2">
                                <iframe frameborder="0" width="100%" scrolling="no">
                                    <xsl:attribute name="src">/ocp/objectManager/style/subform/subforms.php?random=<xsl:value-of select="../@random"/>&amp;<xsl:value-of select="@Url"/>&amp;Type=<xsl:value-of select="@SubType"/>&amp;TypeField=<xsl:value-of select="@SubTypeField"/>&amp;Editable=<xsl:value-of select="@Editable"/>&amp;
                                    ocp_brojac=0</xsl:attribute>
                                    <xsl:attribute name="id">subForm_<xsl:value-of select="@SubType"/>_<xsl:value-of select="@SubTypeField"/></xsl:attribute>
                                    <img src="/ocp/img/blank.gif"/>
                                </iframe>
                            </td>
                        </tr>
                    </xsl:for-each>
                </xsl:if>

                <!--ako ima includes nodove-->
                <xsl:if test="../../*[name() = 'includes'] and name='Id'">
                    <xsl:for-each select="../../*[name() = 'includes']/*">
                        <tr>
                            <td class="ocp_opcije_td" style="padding:0px;" colspan="2">
                                <iframe frameborder="0" width="100%" scrolling="no">
                                    <xsl:attribute name="src"><xsl:value-of select="@url"/>&amp;random=<xsl:value-of select="../@random"/>&amp;label=<xsl:value-of select="@label"/></xsl:attribute>
                                    <xsl:attribute name="id">include_<xsl:value-of select="@label"/></xsl:attribute>
                                    <img src="/ocp/img/blank.gif"/>
                                </iframe>
                            </td>
                        </tr>
                    </xsl:for-each>
                </xsl:if>

                <xsl:call-template name="hidden">
                    <xsl:with-param name="value" select="value"/>
                    <xsl:with-param name="name" select="name"/>
                </xsl:call-template>
            </xsl:when>

            <xsl:otherwise/>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet> 