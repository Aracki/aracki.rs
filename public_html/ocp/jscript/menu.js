	var basepath	= "/ocp/img/gornji_2/tabovi/";
	var bg_s_img		= basepath + "bg_s.gif";
	var bg_n_img		= basepath + "bg_n.gif";
	var desni_s		= basepath + "desni_s.gif";
	var desni_n		= basepath + "desni_n.gif";
	var izm_nn		= basepath + "izm_nn.gif";
	var izm_sn		= basepath + "izm_sn.gif";
	var izm_ns		= basepath + "izm_ns.gif";
	var levi_n		= basepath + "levi_n.gif";
	var levi_s		= basepath + "levi_s.gif";
	
	function switchMenuTabs(current, previous, action, displayType){
		//if (current == selected) return;

		if (displayType == "HTML-editor"){
			var retVal = true;
			switch (current){
				case "normal": retVal = eval(menuArray["normal"][1]); break;
				case "html":  retVal = eval(menuArray["html"][1]); break;
			}
			if (!retVal) return;
		}

		if (current != "preview"){//preview postoji samo u siteManageru
			//background
			document.getElementById(selected+'_background').style.backgroundImage = "url(\'"+bg_n_img+"\')";
			document.getElementById(current+'_background').style.backgroundImage = "url(\'"+bg_s_img+"\')";
			
			//desni listic
			if (document.getElementById(selected+'_desni').src.indexOf(desni_s) > 0)
				document.getElementById(selected+'_desni').src = desni_n;
			else document.getElementById(selected+'_desni').src = izm_nn;

			if (document.getElementById(current+'_desni').src.indexOf(desni_n) > 0)
				document.getElementById(current+'_desni').src = desni_s;
			else document.getElementById(current+'_desni').src = izm_sn;
			
			//levi listic
			if (document.getElementById(selected+'_levi') != null)
				document.getElementById(selected+'_levi').src = levi_n;
			else {
				if (selected_previous != null){
					if (document.getElementById(selected_previous+'_desni').src.indexOf(desni_s) > 0)
						document.getElementById(selected_previous+'_desni').src = desni_n;
					else{
						if (document.getElementById(selected_previous+'_desni').src.indexOf(izm_ns) > 0)
							document.getElementById(selected_previous+'_desni').src = izm_nn;
					}
				}
			}
				
			if (document.getElementById(current+'_levi') != null) document.getElementById(current+'_levi').src = levi_s;
			else{
				if (previous != null){
					if (document.getElementById(previous+'_desni').src.indexOf(desni_n) > 0)
						document.getElementById(previous+'_desni').src = desni_s;
					else document.getElementById(previous+'_desni').src = izm_ns;
				}
			}
			//text
			if (selected != current){
				if (document.getElementById(selected+'_text') != null){
					document.getElementById(selected+'_text').style.fontWeight = "normal";
					document.getElementById(selected+'_text').style.color = "#000000";
					document.getElementById(selected+'_text').style.cursor = "pointer";
				}	
			}

			if (document.getElementById(current+'_text') != null){
				document.getElementById(current+'_text').style.fontWeight = "bold";
				document.getElementById(current+'_text').style.color = "#363636";
				document.getElementById(current+'_text').style.cursor = "text";
			}
		}

		if (displayType == "ADMIN" || displayType == "REPORTS"){
			if (action == null){
				switch (current){
					case "lista_objekata":
					case "izvestaj":
					case "brisanje_objekata":
					case "nadji_objekat":
						showSubmenuClose(true, true);
						submenuArray = new Array();	oldSubmenuTab = '';
						eval(menuArray[current][1]);
						break;
					case "admin_novi":
						eval(menuArray[current][1]);
						break;
				}
			}	
		} else if (displayType == "OBJECT MANAGER"){
			if (action == null){
				switch (current){
					case "lista_objekata":
					case "nadji_objekat":
						showSubmenuClose(true, true);
						submenuArray = new Array();	oldSubmenuTab = '';
						eval(menuArray[current][1]);
						break;
					case "novi_objekat":
						eval(menuArray[current][1]);
						break;
				}
			}		
		} else if (displayType == "SITE MANAGER"){

			switch (current){
				case "edit":
					showSubmenuClose(false, null);
					eval(menuArray["edit"][1]);
					if (document.getElementById('novi_blok')){
						document.getElementById('novi_blok').style.display =  (IE) ? 'block' : "table-cell";
						document.getElementById('uredi_blokove').style.display = (IE) ? 'block' : "table-cell";
						document.getElementById('crtka').style.display = (IE) ? 'block' : "table-cell";
					}
					switchSubmenuTab('');
					break;
				case "setup":
					showSubmenuClose(false, null);
					eval(menuArray["setup"][1]);
				
					switchSubmenuTab(submenuDefaultTab);
					
					if (document.getElementById('novi_blok')){
						document.getElementById('novi_blok').style.display = "none";
						document.getElementById('uredi_blokove').style.display = "none";
						document.getElementById('crtka').style.display = "none";
					}
					break;
				case "preview"://preview postoji samo u siteManageru
					var preview = eval(menuArray["preview"][1]);
					preview.focus();
					switchSubmenuTab('');
					break;
			}
		} else if (displayType == "FILE MANAGER"){
			if (action == null){
				switch (current){
					case "lista_objekata":
					case "thumbs":
						showSubmenuClose(true, true);
						submenuArray = new Array();	oldSubmenuTab = '';
						eval(menuArray[current][1]);
						break;
					case "novi_fajl":
					case "novi_folder":
						eval(menuArray[current][1]);
						break;			
				}
			}		
		}

		if (current != "preview"){//preview postoji samo u siteManageru
			//novi selektovani
			selected = current;
			selected_previous = previous;
		}
	}

	function switchSubmenuTabHE(idTaba){
		if (idTaba != ''){
			var table = '<table class="ocp_gornji_2_table_dugmici"><tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_levo.gif" width="2" height="28"></td> ';
			table += '<td background="/ocp/img/gornji_2/dugmici/plavo_bg.gif">';
			table += '<table class="ocp_gornji_2_table_dugmici"> ';
			table += '<tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="'+submenuArray[idTaba][2]+'" class="ocp_napred_edit_dugme" title="'+submenuArray[idTaba][0]+'"></td> ';
			table += '</tr> ';
			table += '</table></td> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_desno.gif" width="10" height="28"></td> ';
			table += '</tr></table>';

			var tableTaba = document.getElementById(idTaba);
			tableTaba.className="ocp_gornji_2_td_dugmici";
			tableTaba.innerHTML = table;
		}

		if ((oldSubmenuTab != '') && (oldSubmenuTab != idTaba)){
			var oldTable = '<table class="ocp_gornji_2_table_dugmici" style="cursor:pointer;"  onclick="';
			if (submenuArray[oldSubmenuTab][3])
				oldTable += 'if ('+submenuArray[oldSubmenuTab][1]+') switchSubmenuTab(\''+oldSubmenuTab+'\');"><tr>';
			else
				oldTable += submenuArray[oldSubmenuTab][1]+'"><tr>';
			oldTable += '<td class="ocp_gornji_2_td_dugmici"><img src="'+submenuArray[oldSubmenuTab][2]+'" class="ocp_napred_edit_dugme" title="'+submenuArray[oldSubmenuTab][0]+'"></td>';
			oldTable += '</tr></table>';

			var oldTableTaba = document.getElementById(oldSubmenuTab);
			oldTableTaba.innerHTML = oldTable;
		}
	
		oldSubmenuTab = idTaba;
		
	}

	function switchSubmenuTabSM(idTaba){
		if (idTaba != ''){
			var table = '<table class="ocp_gornji_2_table_dugmici"><tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_levo.gif" width="2" height="28"></td> ';
			table += '<td background="/ocp/img/gornji_2/dugmici/plavo_bg.gif">';
			table += '<table class="ocp_gornji_2_table_dugmici"> ';
			table += '<tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/ikona_'+idTaba+'.gif" class="ocp_gornji_2_dugme_ikona" title="'+submenuArray[idTaba][0]+'"></td> ';
			table += '<td nowrap class="ocp_gornji_2_dugme">'+submenuArray[idTaba][0]+'</td> ';
			table += '</tr> ';
			table += '</table></td> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_desno.gif" width="10" height="28"></td> ';
			table += '</tr></table>';

			var tableTaba = document.getElementById(idTaba);
			tableTaba.className="ocp_gornji_2_td_dugmici";
			tableTaba.innerHTML = table;
		}

		if (oldSubmenuTab != '' && (oldSubmenuTab != idTaba)){
			var oldTable = '<table class="ocp_gornji_2_table_dugmici" style="cursor:pointer;"  onclick="switchSubmenuTab(\''+oldSubmenuTab+'\');'+submenuArray[oldSubmenuTab][1]+'return false;"><tr>';
			oldTable += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/ikona_'+oldSubmenuTab+'.gif" class="ocp_gornji_2_dugme_ikona" title="'+submenuArray[oldSubmenuTab][0]+'"></td>';
			oldTable += '<td nowrap class="ocp_gornji_2_dugme" id="'+submenuArray[oldSubmenuTab][0]+'Id">'+submenuArray[oldSubmenuTab][0]+'</td>';
			oldTable += '</tr></table>';

			var oldTableTaba = document.getElementById(oldSubmenuTab);
			oldTableTaba.innerHTML = oldTable;
		}
	
		oldSubmenuTab = idTaba;
	}

	function switchSubmenuTabAdmin(idTaba){
		idTaba += "";
		if (idTaba != ''){
			var table = '<table class="ocp_gornji_2_table_dugmici"><tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_levo.gif" width="2" height="28"></td> ';
			table += '<td background="/ocp/img/gornji_2/dugmici/plavo_bg.gif">';
			table += '<table class="ocp_gornji_2_table_dugmici"> ';
			table += '<tr> ';
			if (!isNaN(idTaba))
				table += '<td nowrap class="ocp_gornji_2_dugme">'+(parseInt(idTaba) + 1)+'</td> ';
			else 
				table += '<td nowrap class="ocp_gornji_2_dugme">'+idTaba+'</td> ';
			table += '</tr> ';
			table += '</table></td> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_desno.gif" width="10" height="28"></td> ';
			table += '</tr></table>';
			var newSubmenu = document.getElementById(idTaba);
			if (newSubmenu.style && newSubmenu.style.cursor) newSubmenu.style.cursor = "auto";
			newSubmenu.innerHTML = table;
		}

		if (oldSubmenuTab != '' && (oldSubmenuTab != idTaba)){
			var oldSubmenu = document.getElementById(oldSubmenuTab);
			if (oldSubmenu.style && oldSubmenu.style.cursor) oldSubmenu.style.cursor = "pointer";
			oldSubmenu.className = "ocp_gornji_2_dugme";
			if (!isNaN(idTaba))
				oldSubmenu.innerHTML = parseInt(oldSubmenuTab) + 1;
			else 
				oldSubmenu.innerHTML = oldSubmenuTab;
		}

		oldSubmenuTab = idTaba;
	}

	function switchSubmenuTabOM(idTaba){
		idTaba += "";
		if (idTaba != ''){
			var table = '<table class="ocp_gornji_2_table_dugmici"><tr> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_levo.gif" width="2" height="28"></td> ';
			table += '<td background="/ocp/img/gornji_2/dugmici/plavo_bg.gif">';
			table += '<table class="ocp_gornji_2_table_dugmici"> ';
			table += '<tr> ';
			if (!isNaN(idTaba))
				table += '<td nowrap class="ocp_gornji_2_dugme">'+(parseInt(idTaba) + 1)+'</td> ';
			else 
				table += '<td nowrap class="ocp_gornji_2_dugme">'+idTaba+'</td> ';
			table += '</tr> ';
			table += '</table></td> ';
			table += '<td class="ocp_gornji_2_td_dugmici"><img src="/ocp/img/gornji_2/dugmici/plavo_desno.gif" width="10" height="28"></td> ';
			table += '</tr></table>';
			var newSubmenu = document.getElementById(idTaba);
			if (newSubmenu.style && newSubmenu.style.cursor) newSubmenu.style.cursor = "auto";
			newSubmenu.innerHTML = table;
			showSubmenuClose(true, true);
		}

		if (oldSubmenuTab != '' && (oldSubmenuTab != idTaba)){
			var oldSubmenu = document.getElementById(oldSubmenuTab);
			if (oldSubmenu.style && oldSubmenu.style.cursor) oldSubmenu.style.cursor = "pointer";
			oldSubmenu.className = "ocp_gornji_2_dugme";
			oldSubmenu.innerHTML = parseInt(oldSubmenuTab) + 1;
		}

		oldSubmenuTab = idTaba;
	}