<?php 
require_once("../../include/connect.php");
require_once("../../include/session.php");
?><!DOCTYPE HTML><html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="/ocp/css/opsti.css">
        <link rel="stylesheet" href="/ocp/css/opcije.css">
    </body>
<body class="ocp_body">
    <?php
    $root = utils_requestStr(getGVar("root"));
    $field= utils_requestStr(getGVar("field"));
    $sirina = utils_requestStr(getGVar("sirina"));
    $type = utils_requestStr(getGVar("objType"));

    $visina = utils_requestStr(getGVar("visina"));
    $max = utils_requestInt(getGVar("max"));
    $putanja = utils_requestStr(getGVar("putanja"));
    $extensionFilter = utils_requestStr(getGVar("extensionFilter"));
    $sortField = utils_requestStr(getGVar("sortField"));
    if (!utils_valid($sortField)) $sortField = "names";
    $sortType = utils_requestStr(getGVar("sortType"));
    if (!utils_valid($sortType)) $sortType="A";


    //parametri za preview prozor
    $parameters = "root=".$root."&field=".$field."&sirina=".$sirina."&visina=".$visina."&max=".$max."&objType=".$type;

    $broj = 20;
    $listType = utils_requestStr(getGVar("listType"));
    $brojac = utils_requestInt(getGVar("ocp_brojac"));
    if (!utils_valid($brojac)) $brojac = 0;
    else $brojac = intval($brojac);

    require_once("extensionGroups.php");
    $extensionTypes = array(ocpLabels("All files"), ocpLabels("Images"), ocpLabels("Multimedia"), ocpLabels("Documents"));

    $fileNames = array();
    $fileNamesShort = array();
    $fileLowerNames = array();
    $fileExtensions = array();
    $fileSizes = array();
    $fileDatesModified = array();
    $fileIndex = array();

    if ($root != "/") $putanja = $putanja;
    //else $putanja = substr($putanja, 1);

    $i = 0;
    $mappedPutanja = realpath("../../..") . $putanja;
    if ($handle = @opendir($mappedPutanja)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_file("$mappedPutanja$file")) {
//echo("$mappedPutanja$file"."<br />");
                    $temp_path = $file;
                    if (!is_integer(strrpos($temp_path, "."))) continue;
                    $temp_ext = substr($temp_path, strrpos($temp_path, ".")+1);
                    $temp_name = substr($temp_path, 0, strrpos($temp_path, "."));

                    if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $temp_path) && utils_valid($temp_ext) && utils_valid($temp_name)) {
                        $fileNames[] = $temp_name;
                        $fileLowerNames[] = strtolower($temp_name);
                        if (strlen($temp_name) > 15) $temp_name = substr($temp_name, 0, 13) . "~";
                        $fileNamesShort[] = $temp_name;
                        $fileExtensions[] = $temp_ext;
                        $fileSizes[] = round(filesize("$mappedPutanja$file")/1024);
                        $fileDatesModified[] = date("m/d/Y", filemtime("$mappedPutanja$file"));
                        $fileIndex[] = $i;
                        $i++;
                    }
                }
            }
        }
        closedir($handle);
    }
    $recordCount = count($fileNames);

    // prvo sortiran medan niz i pamtim u Index nizu

    $sortArray = array();
    $numbersort = SORT_STRING;

    if ($sortField == "extensions") {
        $sortArray = $fileExtensions;
    } else if ($sortField == "sizes") {
        $numbersort = SORT_NUMERIC;
        $sortArray = $fileSizes;
    } else if ($sortField == "dates") {
        $sortArray = $fileDatesModified;
    } else {
        $sortField = "names";
        $sortArray = $fileLowerNames;
    }

    if ($sortType == "A") asort($sortArray, $numbersort);
    else arsort($sortArray, $numbersort);

    // pa sortiram ostale
    $fileNamesTemp = array();
    $fileNamesShortTemp = array();
    $fileExtensionsTemp = array();
    $fileSizesTemp = array();
    $fileDatesModifiedTemp = array();

    foreach ($sortArray as $key => $val) {
        $fileNamesShortTemp[] = $fileNamesShort[$key];
        $fileNamesTemp[] = $fileNames[$key];
        $fileExtensionsTemp[] = $fileExtensions[$key];
        $fileDatesModifiedTemp[] = $fileDatesModified[$key];
        $fileSizesTemp[] = $fileSizes[$key];
    }

    // pa ih vratim u pocetni niz
    $fileNamesShort = $fileNamesShortTemp;
    $fileNames = $fileNamesTemp;
    $fileExtensions = $fileExtensionsTemp;
    $fileDatesModified = $fileDatesModifiedTemp;
    $fileSizes = $fileSizesTemp;

    ?>
    <form action="list.php?<?php echo utils_randomQS();?>" method="get" name="reconstructForm" id="reconstructForm">
        <input type="hidden" name="root" value="<?php echo $root?>">
        <input type="hidden" name="field" value="<?php echo $field?>">
        <input type="hidden" name="sirina" value="<?php echo $sirina?>">
        <input type="hidden" name="visina" value="<?php echo $visina?>">
        <input type="hidden" name="max" value="<?php echo $max?>">
        <input type="hidden" name="putanja" value="<?php echo $putanja?>">
        <input type="hidden" name="listType" value="<?php echo $listType?>">
        <input type="hidden" name="ocp_brojac" value="<?php echo $brojac?>">
        <input type="hidden" name="ocp_broj" value="<?php echo $broj?>">
        <input type="hidden" name="extensionFilter" value="<?php echo $extensionFilter?>">
        <input type="hidden" name="sortType" value="<?php echo $sortType?>">
        <input type="hidden" name="sortField" value="<?php echo $sortField?>">
    </form>
    <script type="text/javascript">
        window.onload = function(){
            parent.menuFrame.populateNavigationSubmenu(<?php echo $broj?>, <?php echo $brojac?>, <?php echo $recordCount?>);
        }
        function preview(fileName){
            window.open("/ocp/controls/imageControl/preview.php?<?php echo utils_randomQS();?>&<?php echo $parameters?>&fileName="+fileName, "previewFrame");
        }
        function newOffset(offset){
            document.reconstructForm.ocp_brojac.value = offset;
            document.reconstructForm.submit();
        }
    </script>
    <div id="ocp_blok_menu_1">
        <table class="ocp_blokovi_table">
            <tr>
                <td class="ocp_blokovi_td" style="padding-left: 6px;"><?php
                    $strNav = ($recordCount > 0) ? "(".($broj*$brojac + 1)."-".min(($brojac+1)*$broj, $recordCount)."/".$recordCount.")" : "(0-0/0)";
                    echo ocpLabels("Found files list")?>: <?php echo $strNav?></td>
                <td class="ocp_blokovi_td" align="right">
                    <form name="extensionFilterForm">
                        <select name="extensionFilter" class="ocp_forma" width="25" onChange="window.open('/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters."&listType=".$listType."&putanja=".$putanja . "&sortType=". $sortType. "&sortField=".$sortField. "&extensionFilter="?>'+this.value, '_self')">
                            <?php
                            for ($i=0; $i<count($extensionGroups); $i++) { ?>
                            <option value="<?php echo $extensionGroups[$i]?>" <?php if ($extensionFilter == $extensionGroups[$i]) echo("selected");?>><?php echo $extensionTypes[$i]?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <div id="stickyHeaderDiv" style="overflow:auto;"><?php
        if (count($fileNames) > 0) {
            if ($listType == "list") {//lista file-ova
                ?><table class="ocp_opcije_table" id="listTable" name="listTable" style="width:100%">
            <tr id="trHeader" style="position:relative; top:0px">
                <td style="white-space: nowrap; width:30px; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3">NO</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="white-space: nowrap; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap; <?php if ($sortField == "names") {
                                        echo("border-bottom: 2px solid #C42E00;");
                                        }?>"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("TITLE")?></span><img src="/ocp/img/blank.gif" width="3" border="0">
                                <a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=D&sortField=names"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole<?php if ($sortField == "names" && $sortType == "D") {
                                                echo("_select");
                                                                                                                                                                                                                                                                          }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a><a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=A&sortField=names"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore<?php if ($sortField == "names" && $sortType == "A") {
                                                    echo("_select");
                                                                                                                                                                                                                                                                              }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a></td>
                        </tr>
                    </table>
                </td>
                <td style="white-space: nowrap; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap;<?php if ($sortField == "extensions") {
                                        echo("border-bottom: 2px solid #C42E00;");
                                        }?>"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("EXTENSION")?></span><img src="/ocp/img/blank.gif" width="3" border="0">
                                <a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=D&sortField=extensions"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole<?php if ($sortField == "extensions" && $sortType == "D") {
                                                echo("_select");
                                                                                                                                                                                                                                                                               }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a><a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=A&sortField=extensions"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore<?php if ($sortField == "extensions" && $sortType == "A") {
                                                    echo("_select");
                                                                                                                                                                                                                                                                                   }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a></td>
                        </tr>
                    </table>
                </td>
                <td style="white-space: nowrap; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap;<?php if ($sortField == "sizes") {
                                        echo("border-bottom: 2px solid #C42E00;");
                                        }?>"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("SIZE")?></span><img src="/ocp/img/blank.gif" width="3" border="0">
                                <a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=D&sortField=sizes"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole<?php if ($sortField == "sizes" && $sortType == "D") {
                                                echo("_select");
                                                                                                                                                                                                                                                                          }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a><a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=A&sortField=sizes"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore<?php if ($sortField == "sizes" && $sortType == "A") {
                                                    echo("_select");
                                                                                                                                                                                                                                                                              }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="white-space: nowrap; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap;<?php if ($sortField == "dates") {
                                        echo("border-bottom: 2px solid #C42E00;");
                                        }?>"><span class="ocp_opcije_tekst3"><?php echo ocpLabels("DATE MODIFIED")?></span><img src="/ocp/img/blank.gif" width="3" border="0">
                                <a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=D&sortField=dates"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole<?php if ($sortField == "dates" && $sortType == "D") {
                                                echo("_select");
                                                                                                                                                                                                                                                                          }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a><a href="/ocp/controls/imageControl/list.php?<?php echo utils_randomQS();?>&<?php echo $parameters . "&listType=" . $listType . "&putanja=" . $putanja  . "&extensionFilter=".$extensionFilter?>&sortType=A&sortField=dates"><img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore<?php if ($sortField == "dates" && $sortType == "A") {
                                                    echo("_select");
                                                                                                                                                                                                                                                                              }?>.gif" title="<?php echo ocpLabels("Sort")?>"></a>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="white-space: nowrap; margin: 0px; padding: 0px;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="ocp_opcije_td_header" style="white-space: nowrap;"><span class="ocp_opcije_tekst3">&nbsp;</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
                    <?php		for ($i=($broj*$brojac); $i<min(($brojac+1)*$broj, $recordCount); $i++) {	?>
            <tr style="cursor:pointer;" onclick="preview('<?php echo $putanja.$fileNames[$i].".".$fileExtensions[$i]?>');">
                <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo ($i+1)?>.</span></td>
                <td class="ocp_opcije_td"><span class="ocp_opcije_tekst2"><b><?php echo $fileNames[$i]?></b></span></td>
                <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $fileExtensions[$i]?></span></td>
                <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $fileSizes[$i]?>KB</span></td>
                <td class="ocp_opcije_td"><span class="ocp_opcije_tekst1"><?php echo $fileDatesModified[$i]?></span></td>
                <td class="ocp_opcije_td_forma" style="text-align: center;"><img src="/ocp/img/opsti/kontrole/kontrola_preview.gif" width="20" height="21" border="0" title="<?php echo ocpLabels("Preview")?>"></td>
            </tr>
                        <?php		}	?>
        </table>
                <?php
            } else {//lista thumbs
                ?>
        <table class="ocp_opcije_table" id="listTable" name="listTable" style="width:100%">
                    <?php
                    for ($i=($broj*$brojac); $i<min(($brojac+1)*$broj, $recordCount); $i++) {
                        if ($i%4 == 0) {
                            if ($i != 0) {?>
            </tr>
                                <?php
                            }
                            ?>
            <tr>
                                <?php
                            }
                            ?>

                <td class="ocp_opcije_td" width="20%" style="cursor:pointer;" onclick="preview('<?php echo $putanja.$fileNames[$i].".".$fileExtensions[$i]?>');"><table width="100%">
                        <tr>
                            <td align="center">

                                            <?php
                                            if (ereg($fileExtensions[$i], $extensionGroups[1])) {
                                                ?>
                                <img src="<?php echo $putanja.$fileNames[$i].".".$fileExtensions[$i]?>"  width="100" class="ocp_blokovi_ikona_velika" alt="" />

                                                <?php
                                            } else if (ereg($fileExtensions[$i], $extensionGroups[2])) {
                                                ?>
                                <img src="/ocp/img/kontrole/file_kontrola/<?php echo $fileExtensions[$i]?>.gif" class="ocp_blokovi_ikona_velika" alt="" />
                                                <?php
                                            } else if (ereg($fileExtensions[$i], $extensionGroups[3])) {
                                                ?>
                                <img src="/ocp/img/kontrole/file_kontrola/<?php echo $fileExtensions[$i]?>.gif" class="ocp_blokovi_ikona_velika" alt="" />
                                                <?php
                                            } else {
                                                ?>
                                <img src="/ocp/img/kontrole/file_kontrola/undefined.gif" class="ocp_blokovi_ikona_velika">
                                                <?php
                                            }
                                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="ocp_blokovi_td_tekst_3" align="center">
                                <strong><?php echo $fileNamesShort[$i].".".$fileExtensions[$i]?></strong>
                                <br />
                                            <?php echo $fileSizes[$i]?>KB</td></tr>
                    </table>
                </td>
                            <?php

                            if (($i+1) == min(($brojac+1)*$broj, $recordCount)) {
                                if (($i+1) % 4 != 0)
                                    for ($j=0; $j < (4 - (($i+1) % 4)) ; $j++) {
                                        ?><td class="ocp_opcije_td_forma" width="20%" >&nbsp;</td><?php
                                    }
                                ?>
            </tr>
                            <?php
                        }
                    }
                    ?>
        </table>
                <?php
            }
        }
        ?>
    </div>
</body>
</html><?php

/*Fje*/
// vraca true ako je ekstenzija dozvoljena u filteru ili ako je filter prazan
function checkExtension ($extensionFilter, $ext) {
    $rezultat = 0;
    $extensionFilterArray = array();
    $extensionFilterArray = split(",", $extensionFilter);
    if ((count($extensionFilterArray) > 0) && utils_valid($extensionFilter)) {
        for ($i=0; $i<count($extensionFilterArray); $i++) {
            $rezultat = 1;
            if (strtolower($ext) == strtolower($extensionFilterArray[$i])) {
                $rezultat = 0;
                break;
            }
        }
    }
    return !$rezultat;
}

function sortFiles ($sortCol) {

}




?>