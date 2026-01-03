<?php
	require_once("../../include/connect.php");
	require_once("../../include/session.php");
	require_once("../../include/users.php");

	users_saveSettings(getSVar("ocpUserGroup"), getSVar("ocpUserId"), utils_requestInt(getGVar("width")), NULL, getSVar("ocpUserLanguage"));
?><script>
	var d = new Date();
	location="/ocp/login.php?random="+Date.parse(d);
</script>