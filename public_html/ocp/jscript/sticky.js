	function getExtraHeight () {
		var visak = 0;
		if (findFrame("uploadFrame")){
			visak = this.uploadFrame.document.body.clientHeight
		}
		else if (findFrame("detailFrame")){
			visak = this.detailFrame.document.body.clientHeight
		}
		return visak;
	}
	function findFrame(frameName) {
		for (var i=0;i<this.frames.length;i++) {
			if (this.frames[i].name == frameName) {
				return true;
			}
		}
		return false;
	}
	function stickyOnLoad(delta) {
		var extraHeight = getExtraHeight();
		var newHeight = this.document.body.clientHeight - delta - extraHeight;
		// kombinacije frejmova su uploadFrame-listFrame
		if (findFrame("uploadFrame") && findFrame("listFrame") && listFrame.document.getElementById('stickyHeaderDiv') && (newHeight > 0)) {
			listFrame.document.getElementById('stickyHeaderDiv').style.height = newHeight;
		} 
		//  i detailFrame-subMenuFrame
		else if (findFrame("detailFrame") && findFrame("subMenuFrame") && subMenuFrame.document.getElementById('stickyHeaderDiv') && (newHeight > 0)) {
			subMenuFrame.document.getElementById('stickyHeaderDiv').style.height = newHeight;
		}
	}
	function stickyOnResize(delta) {
		if (!ocpfirstLoad){
			var extraHeight = getExtraHeight();
			var newHeight = this.document.body.clientHeight - delta - extraHeight;
			// kombinacije frejmova su uploadFrame-listFrame
			if (findFrame("uploadFrame") && findFrame("listFrame") && listFrame.document.getElementById('stickyHeaderDiv') && (newHeight > 0)){
				listFrame.document.getElementById('stickyHeaderDiv').style.height = newHeight;
			} 
			//  i detailFrame-subMenuFrame
			else if (findFrame("detailFrame") && findFrame("subMenuFrame") && subMenuFrame.document.getElementById('stickyHeaderDiv') && (newHeight > 0)){
				subMenuFrame.document.getElementById('stickyHeaderDiv').style.height = newHeight;
			}
		} else {
			ocpfirstLoad = false;
		}	
	}
