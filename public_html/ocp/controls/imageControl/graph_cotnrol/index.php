<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN" "http://www.w3.org/TR/REC-html40/strict.dtd">
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="gcui.css" />
        <script src="jquery.js" type="text/javascript"></script>
        <script type="text/javascript" src="gcui.js"></script>
        <title>Chart generator</title>
        <script type="text/javascript">
            var formField = '<?php echo $_GET["field"];?>';
        </script>
    </head>
    <body>
        <div id="opts">
            <table cellspacing="0" cellpadding="0" border="0"><tr valign="middle">
                    <td>
                        <b><span style="color:#ffcc00">Graph</span>;</b>
                    </td>
                    <td class="sep">|</td>
                    <td>

                        <select id="cht" title="Chart type">
                            <option value="lc">Line</option>
                            <!-- <option value="lxy">XY Line</option> -->
                            <option value="bhs">Bar: horizontalni, u nizu</option>
                            <option value="bhg">Bar: horizontalni, grupe</option>
                            <option value="bvs">Bar: vertikalni, u nizu</option>
                            <option value="bvg" selected="selected">Bar: vertikalni, grupe</option>

                            <option value="p">Pita</option>
                            <option value="p3">3D Pita</option>
                            <!-- <option value="v">Venn diagram</option> -->
                            <!-- <option value="s">Scatter</option> -->
                        </select>
                    </td>
                    <td class="sep">|</td>

                    <td>
                        <select id="seriesDir" title="Izvor podataka">
                            <option value="rows">Sa leva na desno</option>
                            <option value="cols">Odozgo na dole</option>
                        </select>
                    </td>
                    <td class="sep">|</td>

                    <td><input id="chs_w" value="620" title="Sirina"></td>
                    <td>x</td>
                    <td><input id="chs_h" value="310" title="Visina"></td>
                    <td class="sep">|</td>
                    <td>
                        <select id="chd_encoding" title="Data encoding" style="display:none;">
                            <option value="a">Auto</option>
                            <option value="s">Simple</option>
                        </select>
                    </td>
                    <td class="sep">|</td>
                    <td>Background:</td>
                    <td><img src="x.gif" id="fillColor" class="optColor" style="background-color:#ffffff" title="Background color"></td>

                    <td class="sep">|</td>
                    <td><button id="updateBtn">Generisi</button></td>
                    <td><input id="autoUpdate" type="checkbox" class="cb" value="1" checked title="Reload the chart automatically"></td>
                    <td>auto generisanje</td>
                    <td>&nbsp;</td>
                    <td><button id="snimi" type="button">Snimi sliku</button></td>
                </tr></table>
        </div>

        <div id="urlText"><input title="URL of current chart"></div>

        <div id="content">
            <h1>
                <input id="chtt" value="Naslov grafika" />
                <img src="x.gif" class="titleColor" style="background-color:#666666">
                <select name="fontSize" id="fontSize">
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="12">12</option>
                    <option value="14">14</option>
                    <option value="16">16</option>
                    <option value="18">18</option>
                    <option value="20">20</option>
                </select>
            </h1>

            <div id="sheet">
                <table id="tab" cellspacing="0" cellpadding="0" border="0">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>
                                <img src="x.gif" class="colColor" style="background-color:#0066ff">
                                <img src="bp.gif" class="colInsert">
                                <img src="bx.gif" class="colDelete">
                                <input value="1988">
                            </th>
                            <th>
                                <img src="x.gif" class="colColor" style="background-color:#cc6600">
                                <img src="bp.gif" class="colInsert">
                                <img src="bx.gif" class="colDelete">
                                <input value="1998">
                            </th>

                            <th>
                                <img src="x.gif" class="colColor" style="background-color:#66cc00">
                                <img src="bp.gif" class="colInsert">
                                <img src="bx.gif" class="colDelete">
                                <input value="2008">
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <th>
                                <img src="x.gif" class="rowColor" style="background-color:#ff0000">
                                <img src="bp.gif" class="rowInsert">
                                <img src="bx.gif" class="rowDelete">
                                <input value="Jabuke">
                            </th>
                            <td><input value="150"></td>
                            <td><input value="250"></td>
                            <td><input value="400"></td>
                        </tr>
                        <tr>
                            <th>
                                <img src="x.gif" class="rowColor" style="background-color:#ffcc00">
                                <img src="bp.gif" class="rowInsert">
                                <img src="bx.gif" class="rowDelete">
                                <input value="Kruške">
                            </th>
                            <td><input value="20"></td>
                            <td><input value="90"></td>
                            <td><input value="110"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p id="chart">
                <img title="Click to reload, right-click to save image">
            </p>
        </div>
        
        <div id="colorPicker"><div></div></div>
    </body>
</html>