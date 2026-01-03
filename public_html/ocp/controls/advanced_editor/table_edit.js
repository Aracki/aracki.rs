/*Funkcija koja kreira <TD>
===========================*/
	function insertCell(){
		if(!cellSelected()) return;
		var rowSelect= cellSelect.parentNode;
		var newCell= rowSelect.insertCell(cellSelect.cellIndex+1);
		newCell.innerHTML= cellSelect.innerHTML;
	}

/*Funkcija koja brise celiju
============================*/
	function deleteCell(){
		if(!cellSelected()) return; 
		var col= cellSelect.cellIndex;
		var rowSelect= cellSelect.parentNode;
		rowSelect.deleteCell(col);
		cellSelect = rowSelect.cells[col];
		if(!cellSelect) cellSelect = rowSelect.cells[col-1];
		if(cellSelect) currentCell(cellSelect);
	}

/*Funkcija koja kreira novi red
===============================*/
	function insertNewRow(){
		if(!cellSelected()) return;
		var rowSelect= cellSelect.parentNode;
		var tableSelect= rowSelect.parentNode;
		var ridx= rowSelect.rowIndex;
		var row= tableSelect.rows[ridx]; // first row
		var idx=0; 
		for(var j=0; j<row.cells.length; j++){// j= cellIndex
			if(!row.cells[j]) break;
			idx += row.cells[j].colSpan-1;
		}
		var colx= j+idx;

		var newRow=tableSelect.insertRow(ridx);
		var newCell;
		for(var i=0; i<colx; i++){ 
			newCell=newRow.insertCell(0,1);
			newCell.className = cellsArr[0];
			newCell.innerHTML=' ';
	
			if(!IE) newCell.addEventListener("click", clickTD, true) 
		}
		
		for(var i=0; i<=ridx; i++){
			row= tableSelect.rows[i]; 
			for(var j=0; j<row.cells.length; j++){// j= cellIndex
				if(row.cells[j].rowSpan>1 && i+row.cells[j].rowSpan>ridx)
					row.cells[j].rowSpan += 1
				}
			}
		}

/*Funkcija koja brise red
=========================*/
	function deleteThisRow(){
		if(!cellSelected()) return;
		var rowSelect= cellSelect.parentNode;
		var tableSelect= rowSelect.parentNode;
		var ridx= rowSelect.rowIndex;
		row= rowSelect; 
		var rlen=row.cells.length;

		for(var i=0; i<rlen; i++){
			if(row.cells[i].rowSpan>1){
				var newCell= tableSelect.rows[ridx+1].insertCell(i);
				newCell.rowSpan= row.cells[i].rowSpan - 1;
				newCell.innerHTML= row.cells[i].innerHTML;
				row.cells[i].rowSpan =1;
			}
		}

		while(row.cells.length) { row.deleteCell(0); }
		for(var i=0; i<=ridx; i++){
			row= tableSelect.rows[i]; 
			for(var j=0; j<row.cells.length; j++){
				if(row.cells[j].rowSpan>1 && i+row.cells[j].rowSpan>ridx)
					row.cells[j].rowSpan -= 1;
			}
		}

		if(row.cells.length==0) tableSelect.deleteRow(ridx);
	}

/*Funkcija koja vraca broj kolona
=================================*/
	function getColumnNo(){
		if(!cellSelected()) return;
		var cidx= cellSelect.cellIndex;
		var rowSelect= cellSelect.parentNode;
		var tableSelect= rowSelect.parentNode;
		var idx, row, colx ;
		var rspan = new Array();
		for(var i=0; i<rowSelect.rowIndex+1; i++){
			row= tableSelect.rows[i];
			idx=0; 
			for(var j=0; j<row.cells.length; j++){ // j= cellIndex
				if(!rspan[j+idx])rspan[j+idx]=0;
				if(!row.cells[j]) break;

				while(rspan[j+idx]>0) { rspan[j+idx]--; idx++ }
					rspan[j+idx]=row.cells[j].rowSpan-1;

				if(i==rowSelect.rowIndex && j==cidx){ colx=j+idx; break; }

				idx += row.cells[j].colSpan-1;
   			}
		}

		return colx;
	}

/*Vraca index u nizu celije
===========================*/
	function getCellIndex(colx, row){
		var tableSelect= row.parentNode
		var rowIdx= row.rowIndex

		var rspan= new Array();
		var newCell, cs , idx;
		for(var i=0; i<rowIdx+1; i++){
			row= tableSelect.rows[i]
			idx=0; 
			for(var j=0; j<=colx ; j++){ // j= cellIndex
				if(!rspan[j+idx])rspan[j+idx]=0;
				while(rspan[j+idx]){rspan[j+idx]--; idx++ }
				if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
				if(!row.cells[j] || (j+idx>=colx) ){
					if(i==rowIdx) return j;
					else break;
				}
				idx += row.cells[j].colSpan-1
			}
		}
	}

/*Ne znam sta radi (broji koliko ima kolona?)
=================*/
	function getMaxColumn(){
		var rowSelect= cellSelect.parentNode
		var tableSelect= rowSelect.parentNode
		var cell, colnum=0
		for(var i=0; i<tableSelect.rows[0].cells.length ; i++){ // i= cellIndex
			cell= tableSelect.rows[0].cells[i]
			colnum += cell.colSpan
		}
		return colnum
	}

/*Kreira kolonu
===============*/
	function insertCol(){
		if(!cellSelected()) return 
		var rowSelect= cellSelect.parentNode
		var tableSelect= rowSelect.parentNode
		var lines= tableSelect.rows

		var colx= getColumnNo()
		var rspan= new Array();
		var newCell, cs ;
		for(var i=0; i<lines.length; i++){
			row= tableSelect.rows[i]
			idx=0; 
			for(var j=0; j<=colx ; j++){ // j= cellIndex
				if(!rspan[j+idx])rspan[j+idx]=0;
				while(rspan[j+idx]){rspan[j+idx]--; idx++ }

				if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
				if(!row.cells[j] || (j+idx>=colx) ){
					if(row.cells[j-1]) cs=row.cells[j-1].colSpan
					else cs=1
					if(cs==1){ 
						newCell=row.insertCell(j); 
						newCell.className = cellsArr[0]; 
						newCell.innerHTML=' '

						if(!IE) newCell.addEventListener("click", clickTD, true);
						break; 
					} else{
						row.cells[j-1].colSpan += 1
						break ;
					}
		   		}
				idx += row.cells[j].colSpan-1
			}
		}
	}

/*Brise kolonu
===============*/
	function deleteCol(){
		if(! cellSelected()) return 
	 
		var rowSelect= cellSelect.parentNode
		var tableSelect= rowSelect.parentNode
		var lines= tableSelect.rows

		var colx= getColumnNo()

		var rspan= new Array();
		var newCell, cs ;
		for(var i=0; i<lines.length; i++){
			row= tableSelect.rows[i]
			idx=0; 
			for(var j=0; j<=colx ; j++){ // j= cellIndex
				if(!rspan[j+idx])rspan[j+idx]=0;
				while(rspan[j+idx]){rspan[j+idx]--; idx++ }
				if(row.cells[j]) rspan[j+idx]=row.cells[j].rowSpan-1
				if(!row.cells[j] || (j+idx>=colx) ){
					if((j > 1) && row.cells[j-1]) cs=row.cells[j-1].colSpan
					else cs=1
					if(cs==1) row.deleteCell(j)
					else row.cells[j-1].colSpan -= 1
					break ;
				}
				idx += row.cells[j].colSpan-1
			}
		}
	}
 
	function morecolSpan(){
	  if(! cellSelected()) return 

	  var maxcol= getMaxColumn()
	  var colx= getColumnNo() ; // current
	  if(colx+cellSelect.colSpan>=maxcol) return

	  var col= cellSelect.cellIndex
	  var row=cellSelect.parentNode;
	  if(row.cells[col+1])
	  {
	   cellSelect.innerHTML += row.cells[col+1].innerHTML
	   cellSelect.colSpan += row.cells[col+1].colSpan
	   row.deleteCell(col+1)
	  }
	}

	function lesscolSpan(){
	  if(! cellSelected()) return 
	  if(cellSelect.colSpan==1) return
	  var col= cellSelect.cellIndex
	  cellSelect.colSpan -= 1
	  cellSelect.parentNode.insertCell(col+1)
	}


	function morerowSpan(){
	  if(!cellSelected()) return 

	  var rowSpan= cellSelect.rowSpan
	  var rowSelect=cellSelect.parentNode
	  var tableSelect=rowSelect.parentNode
	  var rowNum= tableSelect.rows.length
	  var ridx= rowSelect.rowIndex+rowSpan; // next

	  if( ridx>=rowNum) return 

	  var colx= getColumnNo() ; // current
	  var rowNext= tableSelect.rows[ridx]

	  var cidx=getCellIndex(colx, rowNext); // Next

	  if(!rowNext.cells[cidx]) return;

	  cellSelect.rowSpan += rowNext.cells[cidx].rowSpan
	  cellSelect.innerHTML += rowNext.cells[cidx].innerHTML
	  rowNext.deleteCell(cidx)
	 
	}

	function lessrowSpan(){
	  if(! cellSelected()) return
	  if(cellSelect.rowSpan==1) return

	  var rowSpan= cellSelect.rowSpan
	  var rowSelect=cellSelect.parentNode
	  var tableSelect=rowSelect.parentNode
	  var rowNum= tableSelect.rows.length
	  var ridx= rowSelect.rowIndex+rowSpan-1; // next


	  var colx= getColumnNo() ; // current
	  var rowNext= tableSelect.rows[ridx]
	  var cidx=getCellIndex(colx, rowNext); // Next

	  cellSelect.rowSpan -= 1
	  rowNext.insertCell(cidx)
	  rowNext.cells[cidx].colSpan = cellSelect.colSpan
	}

// za mozillu addeventlistener for table-cell
	function addEventToTable(obj){
		var tdA= obj.document.getElementsByTagName('td')
		for(var i=0; i<tdA.length;i++)
			tdA[i].addEventListener("click", clickTD, true);
	}
