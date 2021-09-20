<?php 
	
	require_once("../../include/session.php");
	

	$url = utils_requestStr(getGVar("url"));
	$width = utils_requestStr(getGVar("sirina"));
	$height = utils_requestStr(getGVar("visina"));

?><html>
<head>
<title>OCP</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/ocp/css/opsti.css">
<link rel="stylesheet" href="/ocp/css/opcije.css">
<script src="/ocp/jscript/swfobject.js"></script>
<style type="text/css">
img { margin:0; padding:0; }
a, a:visited {margin:0; padding:0;}
</style>
</head>
<BODY class="ocp_body" onload="this.focus();">
<div align="center" class="ocp_opcije_tekst1" style="padding: 5px 0px 0px 0px"><?php	

	$realPath = realpath("../../..") . $url;
	$info = pathinfo($realPath);
	$extension = strtolower($info['extension']);

	if ($extension == "swf") {//ako je swf film
		if ($width == 0) $width="320";
		if ($height == 0) $height = "240";
		?><div id="flash"></div>
		<script type="text/javascript">
		   var so = new SWFObject("<?php echo $url?>", "flash1", "<?php echo $width?>", "<?php echo $height?>", "6", "#ffffff", "");
		   so.write("flash");
		</script><?php 
	} else if ($extension == "flv"){
		?><div id="flashplayer"></div>
		<script type="text/javascript">
		   var so = new SWFObject("/images/flash/player.swf?movurl=<?php echo $url?>", "flashfile", "320", "240", "6", "#ffffff");
			so.write("flashplayer");
		</script><?php
	} else if ($extension == "jpg" || $extension == "jpeg" || $extension == "gif"){
		?><img src="<?php echo $url?>"><?php
	} else {
		$innerHTML = '<OBJECT id="mediaplayer" name="mediaplayer" width="320" height="240" classid="CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95" 	codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject">';
		$innerHTML .= '<param name="fileName" value="'.$url.'">';
		$innerHTML .= '<param name="ShowControls" value="1">';
		$innerHTML .= '<param name="showstatusbar" value="1">';
		$innerHTML .= '<param name="enableContextMenu" value="1">';
		$innerHTML .= '<param name="loop" value="0">';
		$innerHTML .= '<param name="autostart" value="1">';
		$innerHTML .= '<EMBED type="application/x-mplayer2" pluginspage="http://microsoft.com/windows/mediaplayer/en/download/" ';
		$innerHTML .= ' id="mediaplayerNS" name="mediaplayerNS" displaysize="4" autosize="0" ';
		$innerHTML .= ' bgcolor="darkblue" showcontrols="1" showtracker="1" ';
		$innerHTML .= ' showdisplay="0" showstatusbar="1" videoborder3d="0" width="320" height="240" ';
		$innerHTML .= ' src="'.$url.'" autostart="1" designtimesp="5311" loop="1" enableContextMenu="false" allowScriptAccess="1">';
		$innerHTML .= '</EMBED>';
		$innerHTML .= '</OBJECT>';

		echo $innerHTML;

	}	

	?><br clear="all"><br/>
</div>
</body>
</html>

