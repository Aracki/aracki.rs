function pp(s) {
    window.console ? console.log(s) : alert(s)
}

$(function() {
    GCUI.events.init();
});
var IE = (navigator.userAgent.indexOf("Firefox") == -1) ? true : false;
var ROWS = 0;
var COLS = 1;
var GCUI = {}
GCUI.cache = {}

GCUI.events = {
    init: function() {
        $("#tab img").unbind();
        $(".rowColor, .colColor, .optColor, .titleColor").click(function() {
            GCUI.colorPicker.show($(this).offset(), $(this))
        });
        $(".colInsert").click(function() {
            GCUI.table.insertCol($(this))
        })
        $(".colDelete").click(function() {
            GCUI.table.deleteCol($(this))
        })
        $(".rowInsert").click(function() {
            GCUI.table.insertRow($(this))
        })
        $(".rowDelete").click(function() {
            GCUI.table.deleteRow($(this))
        })
        $("#updateBtn").click(function() {
            GCUI.events.update(true);
        });
        $("#chart").click(function() {
            GCUI.events.update(true);
        });
        $("#fontSize").change(function() {
            GCUI.events.update(true);
        });
        $("#urlText input").focus(function() {
            this.select()
        });
        $("#tab input").keydown(function(e) {
            if(e.keyCode == 13)
                GCUI.events.update(true);
        });
        $('#snimi').click(function(e){
            e.preventDefault();
            $.ajax({
                url: 'getChart.php?' + GCUI.url.buildParams().join('&'),
                cache: false,
                success : function(response, obj){
                    if (response){
                        var el = window.opener.document.getElementById(formField.split(".")[0]).elements[formField.split(".")[1]];
                        var src= response;
                        if(src==''){
                            window.close();
                            return;
                        }
                        el.value = src;
                        window.close();
                    }
                }
            });
        });
	
        setInterval("GCUI.events.autoUpdate()", 3000);
        GCUI.events.update();
        $("#chtt").focus();
    },
    autoUpdate: function() {
        if($("#autoUpdate")[0].checked)
            GCUI.events.update();
    },
    update: function(force) {
        var url = GCUI.url.build();
        if(force || GCUI.cache['url'] != url) {
            GCUI.cache['url'] = url;
            $("#urlText input").val(url);
            $("#chart img").
            attr("width", $("#chs_w").val()).
            attr("height", $("#chs_h").val()).
            attr("src", url);
        }
    }
}

GCUI.utils = {
    dupNode: function(t) {
        return t.after(t.clone(true));
    },
    encColor: function(r, g, b) {
        r = (r | 0x100).toString(16).substr(1);
        g = (g | 0x100).toString(16).substr(1);
        b = (b | 0x100).toString(16).substr(1);
        return r + g + b;
    },
    color2hex: function(color) {
        var m;
        if(m = color.match(/^#([0-9a-f]+)$/i))
            return m[1];
        if(m = color.match(/^rgb\D+(\d+)\D+(\d+)\D+(\d+)/i))
            return GCUI.utils.encColor(m[1], m[2], m[3]);
        return "000000";
    },
    objColor: function(t) {
        return GCUI.utils.color2hex(t.css("background-color"));
    },
    minmax: function(a) {
        var mm = [0, 0];
        for(var i = 0; i < a.length; i++) {
            if(!isNaN(a[i]))
                mm = [Math.min(mm[0], a[i]), Math.max(mm[1], a[i])];
        }
        return mm;
    }
}

GCUI.table = {
    size: function() {
        return [ $("#tab tbody tr").size(), $("#tab thead th").size() - 1 ];
    },
    insertCol: function(btn) {
        var t = btn.parent();
        var n = t[0].cellIndex - 1;
        GCUI.utils.dupNode(t);
        $("#tab tbody tr").each(function() {
            GCUI.utils.dupNode($("td:eq(" + n + ")", $(this)));
        });
    },
    deleteCol: function(btn) {
        if(GCUI.table.size()[COLS] > 1) {
            var t = btn.parent();
            var n = t[0].cellIndex - 1;
            t.remove();
            $("#tab tbody tr").each(function() {
                $("td:eq(" + n + ")", $(this)).remove();
            });
        }
    },
    insertRow: function(btn) {
        GCUI.utils.dupNode(btn.parent().parent());
    },
    deleteRow: function(btn) {
        if(GCUI.table.size()[ROWS] > 1) {
            btn.parent().parent().remove();
        }
    }
}

GCUI.url = {
    build: function() {
        var url = GCUI.url.buildParams();
        $('#debug').html('');
        $(url).each(function(i, str){
            $('#debug').append(str + '<br />');
        });
        return "http://chart.apis.google.com/chart?" + url.join("&");
    },
    buildParams : function(){
        var url = [];

        var type = $("#cht").val();
        var size = GCUI.table.size();
        var data = GCUI.url.collectData();
        var labels = GCUI.url.collectLabels();
        var colors = GCUI.url.collectColors();

        var mode = $("#seriesDir").val() == "rows" ? ROWS : COLS;
        var isPie = type.match(/^(p3?|v)/) ? 1 : 0;

        var bar_width = Math.floor(($("#chs_w").val()-150)/(size[1 - mode]*size[mode]));

        //alert (bar_width);

        url.push("cht=" + type);
        url.push("chd=" + GCUI.url.encodeData($("#chd_encoding").val(), data[mode], size[1 - mode]));
        url.push("chs=" + $("#chs_w").val() + "x" + $("#chs_h").val());
        url.push("chco=" + colors[isPie ? 1 - mode : mode]);
        url.push("chf=" + GCUI.url.encodeBackground());
        url.push("chtt=" + GCUI.url.encodeText($("#chtt").val()));
        url.push("chts=" + GCUI.url.encodeText(GCUI.utils.objColor($('.titleColor')) + "," + $("#fontSize").val()));
        url.push("chdl=" + labels[isPie ? 1 - mode : mode]);
        url.push("chl=" + labels[1 - mode]);
        url.push("chbh="+bar_width+",6");
        return url;
    },
    collectData: function() {
        var siz = GCUI.table.size();
        var r = [], c = [];

        $("#tab tbody td input").each(function() {
            r.push($(this).val());
        });
        for(var x = 0; x < siz[COLS]; x++)
            for(var y = 0; y < siz[ROWS]; y++)
                c.push(r[y * siz[COLS] + x]);
        return [r, c];
    },
    collectColors: function() {
        var r = [], c = [];

        $(".rowColor").each(function() {
            r.push(GCUI.utils.objColor($(this)));
        });
        $(".colColor").each(function() {
            c.push(GCUI.utils.objColor($(this)));
        });
        return [ r.join(","), c.join(",") ];
    },
    collectLabels: function() {
        var r = [], c = [];

        $("#tab tbody th input").each(function() {
            r.push(GCUI.url.encodeText($(this).val()));
        });
        $("#tab thead th input").each(function() {
            c.push(GCUI.url.encodeText($(this).val()));
        });
        return [ r.join("|"), c.join("|") ];
    },
    selectEncoding: function(data, len) {
        return 's'; // TODO: other types
    },
    encode_s: function(data, len) {
        var a = [];
        var e = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var mm = GCUI.utils.minmax(data);
        var q = mm[1] == mm[0] ? 1 : mm[1] - mm[0];
		
        for(var k = 0; k < data.length / len; k++) {
            var s = [];
            for(var i = 0; i < len; i++) {
                var d = data[k * len + i];
                s.push(isNaN(d) ? "_" : e.charAt(Math.floor(61 * (d - mm[0]) / q)));
            }
            a.push(s.join(""));
        }
        return a.join(",");
    },
    encodeData: function(type, data, len) {
        if(!type.match(/^[ste]$/))
            type = GCUI.url.selectEncoding(data, len);
        return type + ":" + GCUI.url["encode_" + type](data, len);
    },
    encodeText: function(s) {
        s = $.trim(s).replace(/[\r\n]+/, '|');
        return encodeURIComponent(s).replace(/%20/g, "+");
    },
    encodeBackground: function() {
        var a = [
        "bg",
        "s",
        GCUI.utils.objColor($("#fillColor"))
        ];
        return a.join(",");
    }
}

GCUI.colorPicker = { 
    inited: false,
    show: function(offset, target) {
        if(!GCUI.colorPicker.inited)
            GCUI.colorPicker.init();
        GCUI.colorPicker.target = target;
        $("#colorPicker").
        css("top", offset.top + 10).
        css("left", offset.left).
        show();
    },
    init: function() {
        var h = [];
        for(var r = 0; r <= 0xff; r += 0x33)
            for(var b = 0; b <= 0xff; b += 0x33)
                for(var g = 0; g <= 0xff; g += 0x33)
                    h.push(
                        "<p style='background-color:#" +
                        GCUI.utils.encColor(r, g, b) +
                        "'>&nbsp;</p>");

        $("#colorPicker div").html(h.join("\n"));
        $("#colorPicker").mouseover(function(e) {
            try {
                GCUI.colorPicker.target.css("backgroundColor", e.target.style.backgroundColor);
                
            } catch(z) { }
        });
        $("#colorPicker").click(function() {
            $("#colorPicker").hide()
        });
        GCUI.colorPicker.inited = true;
    }
}