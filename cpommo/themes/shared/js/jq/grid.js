(function ($) {
/**
 * jqGrid 3.0 rc - jQuery Grid plugin 29/10/2007
 * rev. 27 05/02/2008
 * http://trirand.com/blog/
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 */
$.fn.jqGrid = function( p ) {
	p = $.extend({
		url: '',
		height: 150,
		page: 1,
		rowNum: 20,
		records: 0,
		pager: "",
		colModel: [],
		rowList: [],
		colNames: [],			
		sortorder: "asc",
		sortname: "",
		imgpath: "",
		sortascimg: "sort_asc.gif",
		sortdescimg: "sort_desc.gif",
		firstimg: "first.gif",
		previmg: "prev.gif",
		nextimg: "next.gif",
		lastimg: "last.gif",
		altRows: true,
		subGrid: false,
		subGridModel :[],
		lastpage: 0,
		lastsort: 0,//?
		selrow: null,
		onSelectRow: null,
		onSortCol: null,
		ondblClickRow: null,
		onRightClickRow: null,
		datatype: "xml",
		mtype: "GET",
		viewrecords: false,
		recordtext: "Record(s)",
		loadtext: "Loading...",
		loadonce: false,
		multiselect: false,
		multikey: null,
		selarrrow: [],
		rowheight: null,
		loadComplete: null, //Joe Tataro
		editurl: null,
		savedRow: [],
		shrinkToFit: true, //Brice Burgess
		xReader: {
			root: "rows",
			row: "row",
			page: "rows>page",
			total: "rows>total",
			records : "rows>records",
			repeatitems: true,
			cell: "cell",
			id: "[id]", 
			subgrid: {root:"rows", row: "row", repeatitems: true, cell:"cell"}
		},
		xmlReader: {},
		jReader: {
			root: "rows",
			page: "page",
			total: "total",
			records: "records",
			repeatitems: true,
			cell: "cell",
			id: "id",
			subgrid: {root:"rows", repeatitems: true, cell:"cell"}
			},
		jsonReader: {}
	}, p || {});

	var grid={         
		headers:[],
		cols:[],
		dragStart: function(i,x) {
			this.resizing = { idx: i, startX: x};
			this.hDiv.style.cursor = "e-resize";
		},
		dragMove: function(x) {
			if(this.resizing) {
				var diff = x-this.resizing.startX;
				var h = this.headers[this.resizing.idx];
				var newWidth = h.width + diff;
				if(newWidth > 30) { 
					h.el.style.width = newWidth+"px";
					h.newWidth = newWidth; 
					this.cols[this.resizing.idx].style.width = newWidth+"px";
					this.newWidth = this.width+diff;
					$('table',this.bDiv).css("width",this.newWidth + "px");
					this.hTable.style.width = this.newWidth + "px";
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				}
			}
		},
		dragEnd: function() {
			this.hDiv.style.cursor = "default";
			if(this.resizing) {
				var idx = this.resizing.idx;
				this.headers[idx].width = this.headers[idx].newWidth;
				this.width = this.newWidth;
				this.resizing = false;
			}
		},
		scroll: function() {
			this.hDiv.scrollLeft = this.bDiv.scrollLeft;
	  	}
	}
	$.fn.getUrl = function() {return this[0].p.url;};           
	$.fn.getSortName = function() {return this[0].p.sortname;};
	$.fn.getSortOrder = function() {return this[0].p.sortorder;};
	$.fn.getSelectedRow = function() {return this[0].p.selrow};
	$.fn.getPage = function() {return this[0].p.page;};
	$.fn.getRowNum = function() {return this[0].p.rowNum;};
	$.fn.getMultiRow = function () {return this[0].p.selarrrow;};
	$.fn.getDataType = function () {return this[0].p.datatype;};
	$.fn.getRecords = function () {return this[0].p.records;};
	$.fn.getDataIDs = function () {	var ids=[];
		$("tr:gt(0)",this[0].grid.bDiv).each(function(i){
			ids[i]=this.id;
		});
		return ids;
	};
	$.fn.setUrl = function (newurl) { return this.each( function(){this.p.url=newurl;}); };
	$.fn.setSortName = function (newsort) {
		var $t = this[0];
		for(var i=0;i< $t.p.colModel.length;i++){
			if($t.p.colModel[i].name==newsort || $t.p.colModel[i].index==newsort){
				$t.p.lastsort = i;
				$t.p.sortname=newsort;
				break;
			}
		}
		return false;
	};
	$.fn.setSortOrder = function (neword) { return this.each( function(){this.p.sortorder=neword; });};
	$.fn.setPage = function (newpage) { return this.each( function() {
		if( typeof newpage === 'number' && newpage > 0) {this.p.page=newpage;}
		});
	};
	$.fn.setRowNum = function (newrownum) { 
		return this.each(function(){if( typeof newrownum === 'number' && newrownum > 0) {this.p.rowNum=newrownum;} });
	};
	$.fn.setDataType = function(newtype) { return this.each( function(){this.p.datatype=newtype; });}
	$.fn.setSelection = function(selection)	{ /*idea from Sven */
		var t = this[0], stat;
  		var pt = $("tbody tr#"+selection,t.grid.bDiv);
  		if (!pt.html()) return false;
  		if(!t.p.multiselect) {
  			if( t.p.selrow ) $("tbody tr#"+t.p.selrow,t.grid.bDiv).removeClass("selected");
  			t.p.selrow = $(pt).attr("id");
  			if($(pt).attr("class") !== "subgrid") $(pt).addClass("selected");
  			if( t.p.onSelectRow ) { t.p.onSelectRow(t.p.selrow, true); }
  		} else {  			
			t.p.selrow = selection;
			var ia = t.p.selarrrow.indexOf(t.p.selrow);
			if (  ia === -1 ){ 
				if($(pt).attr("class") !== "subgrid") { $(pt).addClass("selected");};
				stat = true;
				$("#jqg_"+t.p.selrow,t.grid.bDiv).attr("checked",stat);
				t.p.selarrrow.push(t.p.selrow);
			} else {
				if($(pt).attr("class") !== "subgrid") { $(pt).removeClass("selected");};
				stat = false;
				$("#jqg_"+t.p.selrow,t.grid.bDiv).attr("checked",stat);
				t.p.selarrrow.splice(ia,1);
			}			
  			if( t.p.onSelectRow ) { t.p.onSelectRow(t.p.selrow, stat); }
  		}
		return false;
	};
	$.fn.hideCol = function(colname) {
		return this.each(function() {
			var $t = this,w=0;
			$(this.p.colModel).each(function(i) {
				if (this.name === colname && !this.hidden) {
					w = $("table",$t.grid.hDiv).width();
					$("tr th:eq("+i+")",$t.grid.hDiv).css({display:"none"});
					$("tr",$t.grid.bDiv).each(function(j){
						$("td:eq("+i+")",this).css({display:"none"});
					});
					this.hidden=true;
					$("table",$t.grid.hDiv).width(w);
					return false;
				};
			});
		});
	};
	$.fn.showCol = function(colname) {
		return this.each(function() {
			$t = this; var w = 0;
			$($t.p.colModel).each(function(i) {
				if (this.name === colname && this.hidden) {
					$("tr th:eq("+i+")",$t.grid.hDiv).css("display","");
					$("tr",$t.grid.bDiv).each(function(){
						$("td:eq("+i+")",this).css("display","");
					});
					this.hidden=false;
					return false;
				};
			});
		});
	};
	$.fn.editRow = function(rowid,keys,oneditfunc) {
		var $t = this[0], nm, tmp, editable, cnt=0, focus=null, svr=[], self=this;
		var sz, ml,hc;
		if( !$t.p.multiselect ) {
			editable = $('#'+rowid,$t.grid.bDiv).attr("editable") || "0";
			if (editable === "0") {
				$('#'+rowid+' td',$t.grid.bDiv).each( function(i) {
					nm = $t.p.colModel[i].name;
					hc = $t.p.colModel[i].hidden===true ? true : false
					if ( nm !== 'cb' && nm !== 'subgrid' && $t.p.colModel[i].editable===true && !hc) {
						if(focus===null) focus = i;
						tmp = $(this).html().replace(/\&nbsp\;/ig,'');
						svr[nm]=tmp;
						$(this).html("");
						var opt = $.extend($t.p.colModel[i].editoptions || {} ,{id:rowid+"_"+nm,name:nm});
						if(!$t.p.colModel[i].edittype) $t.p.colModel[i].edittype = "text";
						var elc = createEl($t.p.colModel[i].edittype,opt,tmp);
						$(elc).addClass("editable");
						$(this).append(elc);						
						cnt++;
					}
				});
				if(cnt > 0) {
					svr['id'] = rowid; $t.p.savedRow.push(svr);
					$('#'+rowid,$t.grid.bDiv).attr("editable","1");
					$('#'+rowid+" td:eq("+focus+") input",$t.grid.bDiv).focus();
					if(keys===true) {
						$('#'+rowid,$t.grid.bDiv).bind("keydown",function(e) {
							if (e.keyCode === 27) self.restoreRow(rowid);
							if (e.keyCode === 13) self.saveRow(rowid);
						});
					}
					if( typeof oneditfunc === "function") oneditfunc(rowid);
				}
			}
		}
		function createEl(eltype,options,vl)
		{
			var elem = "";
			switch (eltype)
			{
				case "textarea" :
					elem = document.createElement("textarea");
					if (!options.rows) options.rows = 1;
					$(elem).attr(options);
					elem.innerHTML = vl;
					break;
				case "checkbox" :
					elem = document.createElement("input");
					elem.type = "checkbox";
					$(elem).attr({id:options.id,name:options.name});
					if(vl == options.value.split(":")[0]) {
						elem.checked=true;
						elem.defaultChecked=true;
					}
					break;
				case "select" :
					var so = options.value.split(";"),sv, ov;
					elem = document.createElement("select");
					$(elem).attr({id:options.id,name:options.name});
					for(var i=0; i<so.length;i++){
						sv = so[i].split(":");
						ov = document.createElement("option");
						ov.value = sv[0]; ov.innerHTML = sv[1];
						if (sv[1]==vl) ov.selected ="selected";
						elem.appendChild(ov);
					}
					break;
				case "text" :
					elem = document.createElement("input");
					elem.type = "text";
					if (!options.size) options.size = vl.length;
					$(elem).attr(options);
					elem.value = vl;
					break;
			}
			return elem;
		}
		return false;
	};
	$.fn.saveRow = function(rowid, succesfunc, url, extraparam, aftersavefunc) {
		var $t = this[0], nm, tmp={}, tmp2, editable, fr, self = this;
		editable = $('#'+rowid,$t.grid.bDiv).attr("editable");
		url = url ? url : $t.p.editurl;
		if (editable==="1" && url) {
			$('#'+rowid+" td",$t.grid.bDiv).each(function(i) {
				nm = $t.p.colModel[i].name;
				if ( nm !== 'cb' && nm !== 'subgrid' && $t.p.colModel[i].editable===true) {
					if( $t.p.colModel[i].hidden===true) tmp[nm] = $(this).html();
					else if( $t.p.colModel[i].edittype==='checkbox') tmp[nm]=  $("input",this).attr("checked") ? 1 : 0;
					else tmp[nm]= $("input, select>option:selected, textarea",this).val();
				}
			});
			if(tmp) { tmp["id"] = rowid; if(extraparam) $.extend(tmp,extraparam);}
			if(!$t.grid.hDiv.loading) {
				$t.grid.hDiv.loading = true;
				$("div.loading",$t.grid.hDiv).fadeIn("fast");
				$.post(url,tmp,function(res,stat){
					if (stat === "success"){
						var ret;
						if( typeof succesfunc === "function") ret = succesfunc(res);
						else ret = true;
						if (ret===true) {
							$('#'+rowid+" td",$t.grid.bDiv).each(function(i) {
								nm = $t.p.colModel[i].name;
								if ( nm !== 'cb' && nm !== 'subgrid' && $t.p.colModel[i].editable===true) {
									switch ($t.p.colModel[i].edittype) {
										case "select":
											tmp2 = $("select>option:selected", this).text();
											break;
										case "checkbox":
											var cbv = $t.p.colModel[i].editoptions.value.split(":") || ["Yes","No"];
											tmp2 = $("input",this).attr("checked") ? cbv[0] : cbv[1];
											break;
										case "text":
										case "textarea":
											tmp2 = $("input, textarea", this).val();
											break;
									}
									$(this).empty();
									$(this).html(tmp2 || "&nbsp;");
								}
							});
							$('#'+rowid,$t.grid.bDiv).attr("editable","0");
							for( var k=0;k<$t.p.savedRow.length;k++) {
								if( $t.p.savedRow[k].id===rowid) {fr = k; break;}
							};
							if(fr >= 0) $t.p.savedRow.splice(fr,1);
							if( typeof aftersavefunc === "function") aftersavefunc(rowid,res);
						} else self.restoreRow(rowid);
					} else {alert("Error Row: "+rowid+" Result: " +res+" Status: "+stat)}				
				});
				$t.grid.hDiv.loading = false;
				$("div.loading",$t.grid.hDiv).fadeOut("fast");
				$("#"+rowid,$t.grid.bDiv).unbind("keydown");
			}
		}
		return false;
	};
	$.fn.restoreRow = function(rowid) {
		var $t= this[0], nm, fr;
		for( var k=0;k<$t.p.savedRow.length;k++) {
			if( $t.p.savedRow[k].id===rowid) {fr = k; break;}
		};
		if(fr >= 0) {
			$('#'+rowid+" td",$t.grid.bDiv).each(function(i) {
				nm = $t.p.colModel[i].name;
				if ( nm !== 'cb' && nm !== 'subgrid' && $t.p.colModel[i].editable==true) {
					$(this).empty()
					$(this).html($t.p.savedRow[fr][nm] || "&nbsp;");
				}
			});
			$('#'+rowid,$t.grid.bDiv).attr("editable","0");		
			$t.p.savedRow.splice(fr,1);
		}
		return false;
	};
	$.fn.getRowData = function( rowid ) { var res = {};	if (rowid){
		var $t = this[0],nm;
		$('#'+rowid+' td',$t.grid.bDiv).each( function(i) {
			nm = $t.p.colModel[i].name; 
			if ( nm !== 'cb' && nm !== 'subgrid')
				res[nm] = $(this).text().replace(/\&nbsp\;/ig,'');
		});
		};
		return res;
	};
	$.fn.delRowData = function(rowid) { var success = false; if(rowid) {
		this.each(function() { $('#'+rowid,this.grid.bDiv).each(function(){ $(this).remove(); success=true; }) });
		};
		return success;
	};
	$.fn.setRowData = function(rowid, data) {
		var success = false, nm, vl=true;
		this.each(function(){
			var t = this;
			if( $("#"+rowid,t.grid.bDiv).attr('id')==rowid &&  data ) {
				success=true;
				$(this.p.colModel).each(function(i){
					nm = this.name;
					$(data).each(function() {
						if(this[nm]) {
							$("#"+rowid,t.grid.bDiv).find("td:eq("+i+")").html(this[nm]);
							vl = true;
							return false;
						}
						success = success && vl;
					});
				});
			}
		});
		return success;
	};
	$.fn.addRowData = function(rowid,data,pos) {
		if(!pos) pos = "last";
		var success = false;
		var nm, row, td, gi=0, si=0;
		if(data) {
			this.each(function() {
				t = this;
				row =  document.createElement("tr");
				row.id = rowid || t.p.records+1;
				if(t.p.multiselect) {
					td = $('<td></td>');
					$(td[0],t.grid.bDiv).html("<input type='checkbox'"+" id='jqg_"+rowid+"' class='cbox'/>");
					row.appendChild(td[0]);
					gi = 1;
				}
				if(t.p.subGrid ) {t.addSubGrid(t.grid.bDiv,row,gi); si=1;}
				for(var i = gi+si; i < this.p.colModel.length;i++){
					nm = this.p.colModel[i].name;
					td  = $('<td></td>');
					$(td[0]).html('&nbsp;');
					t.formatCol($(td[0],t.grid.bDiv),i);
					$(data).each(function(j) {
						if(this[nm]) {
							$(td[0]).html(this[nm]);
							return false;
						}
					});
					row.appendChild(td[0]);
				}
				if (pos === "last") $("tbody",t.grid.bDiv).append(row);
				else $("tbody tr:eq(1)",t.grid.bDiv).before(row);
				t.p.records++;
				if(!$.browser.msie) {
					t.scrollLeft = t.scrollLeft;
					$("tbody tr:eq(1) td",t.grid.bDiv).each( function( k ) {
						$(this).css("width",t.grid.headers[k].width+"px");
						t.grid.cols[k] = this;
					});
				}
				success = true;
			});
		}
		return success;
	};
	$.fn.sortGrid = function(colname,reload){
		var $t=this[0],idx=-1;
		if(!colname) colname = $t.p.sortname;
		for(var i=0;i<$t.p.colModel.length;i++) {
			if($t.p.colModel[i].index == colname || $t.p.colModel[i].name==colname) {
				idx = i;
				break;
			}
		}
		if(idx!=-1){
			var sort = $t.p.colModel[idx].sortable;
			if( typeof sort !== 'boolean') sort =  true;
			if( typeof reload !=='boolean') reload = false;
			if(sort) $t.sortData(colname, idx, reload);
		}
		return false;
	};
	$.fn.GridUnload = function(){
		this.each(function(){
			var defgrid = {id: $(this).attr('id'),cl: $(this).attr('class'),cellSpacing: $(this).attr('cellspacing') || '0',cellPadding:$(this).attr('cellpadding') || '0'};
			if (this.p.pager) {
				$(this.p.pager).unbind();
				$(this.p.pager).empty();
			}
			$(this).unbind();
			var newtable = document.createElement('table');
			$(newtable).attr({id:defgrid['id'],cellSpacing:defgrid['cellSpacing'], cellPadding:defgrid['cellPadding']});
			newtable.className = defgrid['cl'];
			$(this.grid.bDiv).remove();
			$(this.grid.hDiv).before(newtable).remove();
			this.p = null;
			this.grid =null;
		});
		return false;
	};
	$.fn.GridDestroy = function () {
		this.each(function(){
			if (this.p.pager) {
				$(this.p.pager).unbind();
				$(this.p.pager).remove();
			}
			$(this).unbind();
			$(this.grid.bDiv).remove();
			$(this.grid.hDiv).remove();
			this.p = null;
			this.grid =null;
		});
		return false;
	};
	if(p.imgpath !== "" ) {
		p.imgpath = p.imgpath + "/";
		p.sortascimg = p.imgpath+p.sortascimg;
		p.sortdescimg = p.imgpath+p.sortdescimg;
		p.firstimg = p.imgpath+p.firstimg;
		p.previmg = p.imgpath+p.previmg;
		p.nextimg = p.imgpath+p.nextimg;
		p.lastimg = p.imgpath+p.lastimg;
	}
	return this.each( function() {
		if(this.grid) {return false;}
		this.p = p; p = null;
		var ts = this;
		if( this.p.colNames.length === 0 || this.p.colNames.length !== this.p.colModel.length ) {
			alert("Length of colNames <> colModel or 0!");
			return false;
		}
		var onSelectRow = this.p.onSelectRow, ondblClickRow = this.p.ondblClickRow, onSortCol=this.p.onSortCol, loadComplete = this.p.loadComplete;
		var onRightClickRow = this.p.onRightClickRow;
		if(typeof onSelectRow !== 'function') {onSelectRow=false;}
		if(typeof ondblClickRow !== 'function') {ondblClickRow=false;}
		if(typeof onSortCol !== 'function') {onSortCol=false;}
		if(typeof loadComplete !== 'function') {loadComplete=false;}
		if(typeof onRightClickRow !== 'function') {onRightClickRow=false;}
		if(!Array.indexOf){
		    Array.prototype.indexOf = function(obj){
		        for(var i=0; i<this.length; i++){
		            if(this[i]==obj){
		                return i;
		            }
		        }
		        return -1;
		    }
		}
		var sortkeys = ["shiftKey","altKey","ctrlKey"];
		if (sortkeys.indexOf(ts.p.multikey) == -1 ) ts.p.multikey = null;
		var formatCol = function (elem, pos){
			var rowalign1 = ts.p.colModel[pos].align || "left";
			$(elem).css("text-align",rowalign1);
			if(ts.p.colModel[pos].hidden) $(elem).css("display","none");
			return false;
		}
		var resizeFirstRow = function (t){
			$("tbody tr:eq(1) td",t).each( function( k ) {
				$(this).css("width",grid.headers[k].width+"px");
				grid.cols[k] = this;
			});
			return false;
		}
		var addCell = function(t,row,cell,pos) {
			var td;
			td = document.createElement("td");
			$(td,t).html( cell);
			formatCol($(td,t), pos);
			row.appendChild(td);
			return false;
		}
		var addMulti = function(t,row){
			var cbid,td;
			td = document.createElement("td");
			cbid = "jqg_"+row.id;
			$(td,t).html("<input type='checkbox'"+" id='"+cbid+"' class='cbox'/>");
			formatCol($(td,t), 0);
			row.appendChild(td);
		}
		var reader = function (datatype) {
			var field, f=[], j=0;
			for(var i =0; i<ts.p.colModel.length; i++){
				var field = ts.p.colModel[i];
				if (field.name !== 'cb' && field.name !=='subgrid') {
					f[j] = datatype=="xml" ? field.xmlmap || field.name : field.jsonmap || field.name;
					j++;
				}
			}
			return f
		}
		var addXmlData = function addXmlData (xml,t) {
			if(xml) { $("tbody tr:gt(0)", t).remove(); } else { return false; }
			var row,gi=0,si=0,cbid,rowh=0,idn, f=null;
			if(!ts.p.xmlReader.repeatitems) f = reader("xml");
			if( !ts.p.keyIndex ) {
				idn = ts.p.xmlReader.id;
				if( idn.indexOf("[") === -1 )
					var getId = function( trow, k) { return $(idn,trow).text() || k }
				else 
					var getId = function( trow, k) { return trow.getAttribute(idn.replace(/[\[\]]/g,"")) || k }
			} else {
				var getId = function(trow) { return f.length >= ts.p.keyIndex ? $(f[ts.p.keyIndex],trow).text() : $(ts.p.xmlReader.cell+":eq("+ts.p.keyIndex+")",trow).text() }
			}
			$(ts.p.xmlReader.page,xml).each( function() { ts.p.page = this.textContent  || this.text ; });
			$(ts.p.xmlReader.total,xml).each( function() { ts.p.lastpage = this.textContent  || this.text ; }  );
			$(ts.p.xmlReader.records,xml).each( function() { ts.p.records = this.textContent  || this.text ; }  );
			$(ts.p.xmlReader.root+">"+ts.p.xmlReader.row,xml).each( function( j ) {
				row = document.createElement("tr");
				row.id = getId(this,j+1);
				if(ts.p.multiselect) {
					addMulti(t,row);
					gi = 1;
				}
				if (ts.p.subGrid) {
					addSubGrid(t,row,gi);
					si= 1;
				}
				if(ts.p.xmlReader.repeatitems===true){
					$(ts.p.xmlReader.cell,this).each( function (i) {
						addCell(t,row,this.textContent || this.text || '&nbsp;',i+gi+si);
					});
				} else {
					var v;
					for(var i = 0; i < f.length;i++) {
						v = $(f[i],this).text() || '&nbsp;';
						addCell(t, row, v, i+gi+si);
					}
				}
				$("tbody",t).append(row);
				if(ts.p.rowheight) rowh = rowh+ts.p.rowheight;
			});
			xml = null;
			if(isMozilla) { ts.scrollLeft = ts.scrollLeft; resizeFirstRow(t);}
			else if(!isMSIE){ resizeFirstRow(t);ts.scrollLeft = ts.scrollLeft;}
		  	ts.scrollTop = 0;
		  	if(ts.p.rowheight) $(grid.bDiv).css({height:rowh+2+'px'});
		 	if( ts.p.altRows === true ) { $("tbody tr:odd", t).addClass("alt"); }
			grid.hDiv.loading = false;
			$("div.loading",grid.hDiv).fadeOut("fast");
			updatepager();
			return false;
		}
		var addJSONData = function(data,t) {
			if(data) { $("tbody tr:gt(0)", t).remove(); } else { return false; }
			var row,cur,gi=0,rowh=0,si=0,drows,idn;
			ts.p.page = data[ts.p.jsonReader.page];
			ts.p.lastpage= data[ts.p.jsonReader.total];
			ts.p.records= data[ts.p.jsonReader.records];
			idn = !ts.p.keyIndex ? ts.p.jsonReader.id : ts.p.keyIndex;
			if(!ts.p.jsonReader.repeatitems) var f = reader("json");
			drows = data[ts.p.jsonReader.root];
			if (drows) {
			for (var i=0;i<drows.length;i++) {
				cur = drows[i];
				row = document.createElement("tr");
				row.id = cur[idn] || i+1;
				if(ts.p.multiselect){
					addMulti(t,row);
					gi = 1;
				}
				if (ts.p.subGrid) {
					addSubGrid(t,row,gi);
					si= 1;
				}
				if (ts.p.jsonReader.repeatitems === true) {
					if(ts.p.jsonReader.cell) cur = cur[ts.p.jsonReader.cell];
					for (var j=0;j<cur.length;j++) {
						addCell(t,row,cur[j] || '&nbsp;',j+gi+si);
					}
				} else {
					for (var j=0;j<f.length;j++) {
						addCell(t,row,cur[f[j]] || '&nbsp;',j+gi+si);
					}
				}
				$("tbody",t).append(row);
				if(ts.p.rowheight) rowh = rowh+ts.p.rowheight;
			}		
			}
			data = null;
			if(isMozilla) { ts.scrollLeft = ts.scrollLeft; resizeFirstRow(t);}
			else if(!isMSIE){ resizeFirstRow(t);ts.scrollLeft = ts.scrollLeft;}
		  	ts.scrollTop = 0;
		  	if(ts.p.rowheight) $(grid.bDiv).css({height:rowh+2+'px'});
		 	if( ts.p.altRows === true ) { $("tbody tr:odd", t).addClass("alt"); }
			grid.hDiv.loading = false;
			$("div.loading",grid.hDiv).fadeOut("fast");
			updatepager();
			return false;
		}
		var updatepager = function() {
			if(ts.p.pager) {
				$('span:eq(1)',ts.p.pager).html("/"+"&nbsp;"+ts.p.lastpage );
				$('input',ts.p.pager).val(ts.p.page);
				if (ts.p.viewrecords)
					$(ts.p.pager).find('span:eq(0)').html(ts.p.records+"&nbsp;"+ts.p.recordtext+"&nbsp;");
			}
			return false;
		}
		var populate = function () {
			if(!grid.hDiv.loading) {
				grid.hDiv.loading = true;
				$("div.loading",grid.hDiv).fadeIn("fast");
				var gdata = {page: ts.p.page, rows: ts.p.rowNum, sidx: ts.p.sortname, sord:ts.p.sortorder, _nd: (new Date().getTime())}
				switch(ts.p.datatype)
				{
				case "json":
					$.ajax({url:ts.p.url,type:ts.p.mtype,datatype:"json",data: gdata, complete:function(JSON) { addJSONData(eval("("+JSON.responseText+")"),ts.grid.bDiv); if(loadComplete) loadComplete();}});
					if( ts.p.loadonce ) ts.p.datatype = "local";
  				break;
				case "xml":
					$.ajax({url: ts.p.url,type:ts.p.mtype,dataType:"xml",data: gdata, complete:function(xml) { addXmlData(xml.responseXML,ts.grid.bDiv);if(loadComplete) loadComplete();}});
					if( ts.p.loadonce ) ts.p.datatype = "local";
				break;
				case "xmlstring":
					addXmlData(stringToDoc(ts.p.datastr),ts.grid.bDiv);
					ts.p.datastr = null;
					ts.p.datatype = "local";
					if(loadComplete) loadComplete();
				break;
				case "jsonstring":
					addJSONData(eval("("+ts.p.datastr+")"),ts.grid.bDiv);
					ts.p.datastr = null;
					ts.p.datatype = "local";
					if(loadComplete) loadComplete();
				break;
				case "local":
				case "clientSide":
					sortArrayData();
  				break;
				}
			}
			return false;
		}
		var stringToDoc =	function (xmlString) {
			var xmlDoc;
			if (isSafari2){
			 	var z=document.createElement('div');
			  	z.innerHTML = xmlString;
				xmlDoc=z;
				z.responseXML=z;
			} 
			else {
				try	{
					var parser = new DOMParser();
					xmlDoc = parser.parseFromString(xmlString,"text/xml");
				}
				catch(e) {
					xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
					xmlDoc.async=false;
					xmlDoc["loadXM"+"L"](xmlString);
				}
			}
			return (xmlDoc && xmlDoc.documentElement && xmlDoc.documentElement.tagName != 'parsererror') ? xmlDoc : null;			
		}
		var sortArrayData = function() {
			var newDir = ts.p.sortorder == "asc" ? 1 :-1;
			var column = ts.p.lastsort >=0 ? ts.p.lastsort:0;
			var st = ts.p.colModel[column].sorttype;
			if (st == 'float') {
				findSortKey = function($cell) {
					var key = parseFloat($cell.html().replace(/,/g, ''));
					return isNaN(key) ? 0 : key;
				}
			} else if (st=='int') {
				findSortKey = function($cell) {
					return IntNum($cell.html().replace(/,/g, ''))
				}
			} else if(st == 'date') {
				findSortKey = function($cell) {
					var fd = ts.p.colModel[column].datefmt || "Y-m-d";
					return parseDate(fd,$cell.html()).getTime();
				}
			} else {
				findSortKey = function($cell) {
					return $cell.html().toUpperCase();
				}
			}
			var rows = $(grid.bDiv).find('tbody > tr:gt(0)').get();
			$.each(rows, function(index, row) {
				row.sortKey = findSortKey($(row).children('td').eq(column));
				var a =1;
			});
			rows.sort(function(a, b) {
				if (a.sortKey < b.sortKey) return -newDir;
				if (a.sortKey > b.sortKey) return newDir;
				return 0;
			});
			$.each(rows, function(index, row) {
				$('tbody',grid.bDiv).append(row);
				row.sortKey = null;
			});
			if(isMozilla) { ts.scrollLeft = ts.scrollLeft; resizeFirstRow(grid.bDiv);}
			else if(!isMSIE){ resizeFirstRow(grid.bDiv);ts.scrollLeft = ts.scrollLeft;}
			ts.scrollTop = 0;
			grid.hDiv.loading = false;
			$("div.loading",grid.hDiv).fadeOut("fast");
		}
		var parseDate = function(format, date) { //so only numbers for now
			var tsp = {m : 1, d : 1, y : 1970, h : 0, i : 0, s : 0};
			format = format.toLowerCase();
			date = date.split(/[\\\/:_;.\s-]/);
			format = format.split(/[\\\/:_;.\s-]/);
		    for(var i=0;i<format.length;i++){
		        tsp[format[i]] = IntNum(date[i],tsp[format[i]]);
		    }
		    tsp.m = parseInt(tsp.m)-1;
		    var ty = tsp.y;
		    if (ty >= 70 && ty <= 99) tsp.y = 1900+tsp.y;
		    else if (ty >=0 && ty <=69) tsp.y= 2000+tsp.y;
		    return new Date(tsp.y, tsp.m, tsp.d, tsp.h, tsp.i, tsp.s,0);
		}
		var addSubGrid = function(t,row,pos) {
			var td, res,_id;
			td = document.createElement("td");
			$(td,t).html("<img src='"+ts.p.imgpath+"plus.gif'/>")
				.toggle( function() {
					$(this).html("<img src='"+ts.p.imgpath+"minus.gif'/>");
					res = $(this).parent();
					var atd= pos==1?'<td></td>':'';
					_id = $(res).attr("id");
					var subdata = "<tr class='subgrid'>"+atd+"<td><img src='"+ts.p.imgpath+"line3.gif'/></td><td colspan='"+parseInt(ts.p.colNames.length-1)+"'><div id='sg_"+_id+"' class='tablediv'>"; 
					$(this).parent().after( subdata+ "</div></td></tr>" );
					$(".tablediv",ts).css("width", ts.grid.width-20+"px");
					if( typeof ts.p.subGridRowExpanded == 'function') {
						ts.p.subGridRowExpanded("sg_"+ _id,_id);
					} else {
						populatesubgrid(res);
					}
				}, function() {
					if( typeof ts.p.subGridRowColapsed == 'function') {
						res = $(this).parent();
						_id = $(res).attr("id");
						ts.p.subGridRowColapsed("sg_"+_id,_id );
					}
					$(this).parent().next().remove(".subgrid");
					$(this).html("<img src='"+ts.p.imgpath+"plus.gif'/>");
				});
			formatCol($(td,t), pos);
			row.appendChild(td);
			return false;
		}
		var populatesubgrid = function( rd ) {
			var res,sid,dp;
			sid = $(rd).attr("id");
			dp ={id:sid};
			if(!ts.p.subGridModel[0]) return false;
			if(ts.p.subGridModel[0].params)
				for(var j=0; j < ts.p.subGridModel[0].params.length; j++)
					for(var i=0; i<ts.p.colModel.length; i++)
						if(ts.p.colModel[i].name == ts.p.subGridModel[0].params[j])
							dp[ts.p.colModel[i].name]= $("td:eq("+i+")",rd).text().replace(/\&nbsp\;/ig,'');
			if(!grid.hDiv.loading) {
				grid.hDiv.loading = true;
				$("div.loading",grid.hDiv).fadeIn("fast");
				switch(ts.p.datatype) {
					case "xml":
					$.ajax({type:ts.p.mtype, url: ts.p.subGridUrl, dataType:"xml",data: dp, complete: function(sxml) { subGridJXml(sxml.responseXML, sid); } });
					break;
					case "json":
					$.ajax({type:ts.p.mtype, url: ts.p.subGridUrl, dataType:"json",data: dp, complete: function(JSON) { res = subGridJXml(JSON,sid); } });
					break;
				}
      		}
			return false;
    	}
		var subGridCell = function(trdiv,cell,pos){
			var tddiv;
			tddiv = document.createElement("div");
			tddiv.className = "celldiv";
			$(tddiv).html(cell);
			$(tddiv).width( ts.p.subGridModel[0].width[pos] || 80);
			trdiv.appendChild(tddiv);
		}
    	var subGridJXml = function(sjxml, sbid){
      		var trdiv, tddiv,result = "", i,cur, sgmap;
       		var dummy = document.createElement("span");
       		trdiv = document.createElement("div");
       		trdiv.className="rowdiv";
      		for (i = 0; i<ts.p.subGridModel[0].name.length; i++) {
       			tddiv = document.createElement("div");
       			tddiv.className = "celldivth";
       			$(tddiv).html(ts.p.subGridModel[0].name[i]);
       			$(tddiv).width( ts.p.subGridModel[0].width[i]);
       			trdiv.appendChild(tddiv);
       		}
       		dummy.appendChild(trdiv);
      		if (sjxml){
				if(ts.p.datatype === "xml") {
					sgmap = ts.p.xmlReader.subgrid;
					$(sgmap.root+">"+sgmap.row, sjxml).each( function(){
						trdiv = document.createElement("div");
						trdiv.className="rowdiv";
						if(sgmap.repeatitems === true) {
							$(sgmap.cell,this).each( function(i) {
								subGridCell(trdiv, this.textContent || this.text || '&nbsp;',i);
							});
						} else {
							var f = ts.p.subGridModel[0].mapping;
							if (f) {
								for (i=0;i<f.length;i++) {
									subGridCell(trdiv, $(f[i],this).text() || '&nbsp;',i);
								}
							}
						}
						dummy.appendChild(trdiv);
					});
				} else {
					sjxml = eval("("+sjxml.responseText+")");
					sgmap = ts.p.jsonReader.subgrid;
					for (i=0;i<sjxml[sgmap.root].length;i++) {
						cur = sjxml[sgmap.root][i];
						trdiv = document.createElement("div");
						trdiv.className="rowdiv";
						if(sgmap.repeatitems === true) {
							if(sgmap.cell) cur=cur[sgmap.cell];
							for (var j=0;j<cur.length;j++) {
								subGridCell(trdiv, cur[j] || '&nbsp;',j);
							}
						} else {
							var f = ts.p.subGridModel[0].mapping;
							for (var j=0;j<f.length;j++) {
								subGridCell(trdiv, cur[f[j]] || '&nbsp;',j);
							}
						}
						dummy.appendChild(trdiv);
					}
				}
				$("#sg_"+sbid).append($(dummy).html());
        		sjxml = null
        		grid.hDiv.loading = false;
        		$("div.loading",grid.hDiv).fadeOut("fast");
      		}
			return false;
    	}
		var setPager = function (){
			$(ts.p.pager).append('<span></span>&nbsp;<img id="first" src="'+ts.p.firstimg+'">&nbsp;&nbsp;<img id="prev" src="'+ts.p.previmg+'">&nbsp;<input class="selbox" type="text" size="3" maxlength="5" value="0"/><span></span>&nbsp;<img id="next" src="'+ts.p.nextimg+'">&nbsp;&nbsp;<img id="last" src="'+ts.p.lastimg+'">');
			if(ts.p.rowList.length >0){
				var str="<SELECT class='selbox'>";
				for(var i=0;i<ts.p.rowList.length;i++){
					str +="<OPTION value="+ts.p.rowList[i]+((ts.p.rowNum == ts.p.rowList[i])?' selected':'')+">"+ts.p.rowList[i];
				}
				str +="</SELECT>";
				$(ts.p.pager).append("&nbsp;&nbsp;"+str);
				$(ts.p.pager).find("select").bind('change',function() { 
					ts.p.rowNum = this.value>0 ? this.value : ts.p.rowNum; populate();
					ts.p.selrow = null;
				});
			}
			$(ts.p.pager).find('img').click( function() {
				var cp = IntNum(ts.p.page);
				var last = IntNum(ts.p.lastpage), selclick = false;
				var fp=true; var pp=true; var np=true; var lp=true;
				if(last ===0 || last===1) {fp=false;pp=false;np=false;lp=false; }
				else if( last>1 && cp >=1) {
					if( cp === 1) { fp=false; pp=false; } 
					else if( cp>1 && cp <last){ }
					else if( cp===last){ np=false;lp=false; }
				} else if( last>1 && cp===0 ) { np=false;lp=false; cp=last-1;}
				if( $(this).attr('id') === 'first' && fp ) { ts.p.page=1; populate(); selclick=true;} 
				if( $(this).attr('id') === 'prev' && pp) { ts.p.page=(cp-1);populate(); selclick=true;} 
				if( $(this).attr('id') === 'next' && np) { ts.p.page=(cp+1);populate(); selclick=true;} 
				if( $(this).attr('id') === 'last' && lp) { ts.p.page=last;  populate(); selclick=true;}
				if(selclick) {
					ts.p.selrow = null;
					if(ts.p.multiselect) {ts.p.selarrrow =[];$('#cb_jqg',ts.grid.hDiv).attr("checked",false);}
					ts.p.savedRow = [];
				}
			}).hover(function() { $(this).addClass("jsHover"); },
				function () { $(this).removeClass("jsHover"); }  
			);
			$('input',ts.p.pager).keypress( function(e) {
				var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
				if(key == 13) {
					ts.p.page = $(this).val()>0 ? $(this).val():ts.p.page;
					populate();
					ts.p.selrow = null;
					return false;
				}
				return this;
			});
			return false;
		}
		var sortData = function (index, idxcol,reload){
			if(!reload) {
				if( ts.p.lastsort === idxcol /*ts.p.sortname === index*/) {				    
					if( ts.p.sortorder === 'asc') {
						ts.p.sortorder = 'desc';
					} else if(ts.p.sortorder === 'desc') { ts.p.sortorder='asc';}
				} else { ts.p.sortorder='asc';}
				ts.p.page = 1;
			}
			var imgs = ts.p.sortorder==='asc' ? ts.p.sortascimg : ts.p.sortdescimg;
			imgs = "<img src='"+imgs+"'>";
			var thd= $("thead:first",grid.hDiv).get(0);
			$("tr th div#"+ts.p.colModel[ts.p.lastsort].name+" img",thd).remove();
			$("tr th div#"+index,thd).append(imgs);
			ts.p.lastsort = idxcol;
			ts.p.sortname = ts.p.colModel[idxcol].index || index;
			if(onSortCol) {onSortCol(index,idxcol);}
			if(ts.p.selrow && ts.p.datatype == "local" && !ts.p.multiselect){ $('#'+ts.p.selrow,grid.bDiv).removeClass("selected");}
			ts.p.selrow = null;
			if(ts.p.multiselect && ts.p.datatype !== "local"){ts.p.selarrrow =[]; $("#cb_jqg",ts.grid.hDiv).attr("checked",false);}
			ts.p.savedRow =[];
			populate();
			return false;
		}
		var setGridWidth = function () {
			var initwidth = 0; 
			for(var l=0;l<ts.p.colModel.length;l++)
				initwidth += IntNum(ts.p.colModel[l].width || 150);
			var tblwidth = ts.p.width ? ts.p.width : initwidth;
			for(l=0;l<ts.p.colModel.length;l++) {
                if(!ts.p.shrinkToFit) 
                    ts.p.colModel[l].owidth = ts.p.colModel[l].width;
				ts.p.colModel[l].width = Math.round(tblwidth/initwidth*ts.p.colModel[l].width);
			}
			return false;
		}
		var IntNum = function(val,defval) {
			val = parseInt(val,10);
			if (isNaN(val)) {
				return defval ? defval : 0;
			} else {
				return val;
			} 
		}
		if(this.p.subGrid) {
		  this.p.colNames.unshift("");
		  this.p.colModel.unshift({name:'subgrid',width:25,sortable: false,resizable:false});
		}
		if(this.p.multiselect) {
			this.p.colNames.unshift("<input id='cb_jqg' type='checkbox'/>");
			this.p.colModel.unshift({name:'cb',width:25,sortable:false,resizable:false});
		}
		this.p.xmlReader = $.extend( this.p.xReader, this.p.xmlReader);
		this.p.jsonReader = $.extend( this.p.jReader, this.p.jsonReader);
		if( this.p.loadonce) this.p.pager = false;
		if (this.p.width) setGridWidth();
		var thead = document.createElement("thead");
		var trow = document.createElement("tr");
		thead.appendChild(trow); 
		var i=0, th, idn, thdiv;
		this.p.keyIndex=false;
		for (var col in this.p.colModel) { //Brice Burgess
			if (this.p.colModel[col].key) {
				this.p.keyIndex = i;
				break;
			}
			i++;
		}
		for(i=0;i<this.p.colNames.length;i++){
			th = document.createElement("th");
			idn = ts.p.colModel[i].name;
			idn = idn ? idn : i+1;
			thdiv = document.createElement("div");
			thdiv.id = ""+idn+"";
			$(thdiv).html(ts.p.colNames[i]+"&nbsp;");
			th.appendChild(thdiv);
			trow.appendChild(th);
		}
		if(this.p.multiselect) {
			$('#cb_jqg',trow).addClass("cbox").click(function(){
				if (this.checked) {
					$("[@id^=jqg_]",grid.bDiv).attr("checked",true);
					$("tr:gt(0)",ts.grid.bDiv).each(function(i) {
						$(this).addClass("selected");
						ts.p.selarrrow[i]=this.id;
					});
				}
				else {
					$("[@id^=jqg_]",grid.bDiv).attr("checked",false);
					$("tr",grid.bDiv).removeClass("selected");
					ts.p.selarrrow = [];
				}
			});
		}
		this.appendChild(thead);
		thead = $("thead:first",ts).get(0);
		var w, res, sort;
		$("tr:first th",thead).each(function ( j ) {
			w = ts.p.colModel[j].width || 150;
			if(typeof ts.p.colModel[j].resizable == 'undefined') ts.p.colModel[j].resizable = true;
			res = document.createElement("span");
			$(res).html("&nbsp;");
			if(ts.p.colModel[j].resizable){
			$(res).mousedown(function (e) {
				grid.dragStart( j ,e.clientX);
				return false;
			});
			} else {$(res).css("cursor","default");}
			$(this).width(w).prepend(res);
			if( ts.p.colModel[j].hidden) $(this).css("display","none");
			grid.headers[j] = { width: w, el: this };
		});
		$("tr:first th div",thead).each(function(l) {
			sort = ts.p.colModel[l].sortable;
			if( typeof sort !== 'boolean') sort =  true;
			if(sort) { 
				$(this).css("cursor","pointer");
				$(this).click(function(){sortData(this.id,l);});
			}
		});
		var tbody = document.createElement("tbody");
		trow = document.createElement("tr");
		trow.style.display="none";
		tbody.appendChild(trow);
		var td, ptr;
		for(i=0;i<ts.p.colNames.length;i++){
			td = document.createElement("td");
			trow.appendChild(td);
		}
		this.appendChild(tbody);
		var gw=0; //if hidden grid
		$("tbody tr:first td",ts).each(function(ii) {
			w = ts.p.colModel[ii].width || 150;
			$(this).css("width",w+"px");
			if( ts.p.colModel[ii].hidden) {
				$(this).css("display","none");
			} else {
				w +=  IntNum($(this).css("padding-left")) +
				IntNum($(this).css("padding-right"))+
				IntNum($(this).css("border-left-width"))+
				IntNum($(this).css("border-right-width"));
			}
			grid.cols[ii] = this;
			gw += w;
		});
		grid.width = $(this).width();
		if (grid.width == 0) grid.width = gw;
		grid.hTable = document.createElement("table");
		grid.hTable.cellSpacing="0"; 
		grid.hTable.cellPadding="0"; 
		grid.hTable.className = "scroll";
		grid.hTable.appendChild(thead);
		grid.hDiv = document.createElement("div");
		$(grid.hDiv)
		  	.css({ width: grid.width+"px", overflow: "hidden"})
			.prepend('<div class="loading">'+ts.p.loadtext+'</div>')					
			.append(grid.hTable)
			.bind("selectstart", function () { return false; });
		$(ts).mouseover(function(e) {
			td = (e.target || e.srcElement);
			ptr = $(td,ts).parents("tr:first");
			if($(ptr).attr("class") !== "subgrid") {
				$(ptr).addClass("over");
				td.title = $(td).text();
			}
			return false;
		}).mouseout(function(e) {
			td = (e.target || e.srcElement);
			ptr = $(td,ts).parents("tr:first");
			$(ptr).removeClass("over");
			td.title = "";
			return false;
		}).css("width", grid.width+"px").before(grid.hDiv).click(function(e) {
			if ( !ts.p.multikey) {
				td = (e.target || e.srcElement);
				ptr = $(td,ts).parents("tr:first");
				$(ts).setSelection($(ptr).attr("id"));
			} else {
				if (e[ts.p.multikey]){
					td = (e.target || e.srcElement);
					ptr = $(td,ts).parents("tr:first");
					$(ts).setSelection($(ptr).attr("id"));
				} else {
					td = (e.target || e.srcElement);
					ptr = $(td).parents("td:first");
					if ( $(ptr).html() !== null) {
						td = $("[@id^=jqg_]",ptr).attr("checked");
						td = typeof td == "undefined" ? false: td;
						$("[@id^=jqg_]",ptr).attr("checked",!td);
					}
				}
			}
			e.stopPropagation();
		}).bind('reloadGrid', function(e) {
			ts.p.selrow=null;
			if(ts.p.multiselect) {ts.p.selarrrow =[];$('#cb_jqg',ts.grid.hDiv).attr("checked",false);}
			populate();
		});
		if( ondblClickRow ) {
			$(this).dblclick(function(e) {
				td = (e.target || e.srcElement);
				ptr = $(td,ts).parents("tr:first");
				ts.p.ondblClickRow($(ptr).attr("id"));
				return false;
			});
		}
		if (onRightClickRow)
			$(this).bind('contextmenu', function(e) {
				td = (e.target || e.srcElement);
				ptr = $(td,ts).parents("tr:first");
				ts.p.onRightClickRow($(ptr).attr("id"));
				return false;
			});
		grid.bDiv = document.createElement("div");
		$(grid.bDiv)
		  	.scroll(function (e) {grid.scroll()})
			.css({ height: ts.p.height+(isNaN(ts.p.height)?"":"px"), padding: "0px", margin: "0px", overflow: "auto", width: (grid.width)+17+"px"})
			.append(this);
		var isMSIE = $.browser.msie ? true:false;
		var isMozilla = $.browser.mozilla ? true:false;
		var isSafari2 = $.browser.safari && ( parseInt($.browser.version) <= 2 || parseInt($.browser.version) <= 419) ? true : false;
		if( isMSIE ) {
			if( $("tbody",this).size() === 2 ) { $("tbody:first",this).remove();}
			if( ts.p.multikey) $(grid.bDiv).bind("selectstart",function(){return false;});
		} else {
			if( ts.p.multikey) $(grid.bDiv).bind("mousedown",function(){return false;});
		}
		$(grid.hDiv).mousemove(function (e) {grid.dragMove(e.clientX);}).after(grid.bDiv);
		if(ts.p.pager){
			if( $(ts.p.pager).attr("class") === "scroll") $(ts.p.pager).css({ width: (grid.width)+"px", overflow: "hidden"});
			setPager();
		}
		$(document).mouseup(function (e) {grid.dragEnd();});
		ts.formatCol = function(a,b) {formatCol(a,b);};
		ts.addSubGrid = function(a,b,c) {addSubGrid(a,b,c);};
		ts.sortData = function(a,b,c){sortData(a,b,c);}
		this.grid = grid;
		populate();
        if (!ts.p.shrinkToFit) { //Brice Burgess
            $("tr:first th", thead).each(function(j){
                var w = ts.p.colModel[j].owidth;
                var diff = w - ts.p.colModel[j].width;
                if (diff > 0) {
                    grid.headers[j].width = w;
                    $(this).add(grid.cols[j]).width(w);
                    grid.width = grid.width + diff;
                    $('table',grid.bDiv).add(grid.hTable).width(grid.width);
                    grid.hDiv.scrollLeft = grid.bDiv.scrollLeft;
                }
            });
        }
		$(window).unload(function () {
			$(this).unbind();
			this.p = null;
			this.grid = null;
			$(ts.p.pager).unbind();
		});
	});
};
})(jQuery);
