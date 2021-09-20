	function alternateAdvanceDiv(aElementId, openLabel, closeLabel){
		var aElement = document.getElementById("advancedDivId");
		var ocpAdvancedDiv = document.getElementById("ocpAdvancedDiv");
		if (ocpAdvancedDiv){
			if (ocpAdvancedDiv.style.visibility == "visible"){
				ocpAdvancedDiv.style.visibility = "hidden";
				ocpAdvancedDiv.style.display = "none";
				aElement.innerHTML = '<a href="#" class="ocp_grupa_zatvori" onclick="alternateAdvanceDiv(\'advancedDivId\', \''+openLabel+'\', \''+closeLabel+'\');return false;">'+openLabel+'<img src="/ocp/img/opsti/kontrole/strelica_nadole.gif" hspace="5" border="0"/></a>';
			} else {
				ocpAdvancedDiv.style.visibility = "visible";
				ocpAdvancedDiv.style.display = "block";
				aElement.innerHTML = '<a href="#" class="ocp_grupa_zatvori" onclick="alternateAdvanceDiv(\'advancedDivId\', \''+openLabel+'\', \''+closeLabel+'\');return false;">'+closeLabel+'<img src="/ocp/img/opsti/kontrole/strelica_nagore.gif" hspace="5" border="0"/></a>';
			}
		}
	}