<script type="text/javascript" src="{$url.theme.shared}js/jq/grid.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/jqgrid.css" />

<script type="text/javascript">
var PommoGrid = {ldelim}
	grid: null,
	defaults: {ldelim}
		loadtext: "{t escape=js}Processing{/t}...",
		recordtext: "{if empty($state.search)}{t escape=js}Record(s){/t}{else}{t escape=js}Match(es){/t}{/if}",
		imgpath: "{$url.theme.shared}/images/grid",
		sortorder: '{$state.order}',
		sortname: '{$state.sort}',
		rowNum: {$state.limit},
		{literal}
		colNames: [],
		colModel: [],
		rowList: [10,50,150,300,500,1000],
		url: 'ajax/404',
		datatype: 'json',
		pager: '#gridPager',
		viewrecords: true,
		multiselect: true,
		height: 270,
		width: 670,
		shrinkToFit: false,
		jsonReader: {repeatitems: false}
	},
	init: function(e,p) {
		this.grid = $(e).jqGrid($.extend(this.defaults,p));
		return this;
	},
	getRowID: function() {
		var row = this.grid.getSelectedRow();
		return (row == null) ? false : row;
	},
	getRowIDs: function() {
		var ids = this.grid.getMultiRow();
		return (ids.length == 0) ? false : ids;
	},
	getRow: function(id){
		id = id || this.getRowID(); // allows non passing of id to auto-get selected row
		return (!id) ? false : this.grid.getRowData(id);
	},
	delRow: function(ids) {
		if (!(ids instanceof Array))
			ids = [ids];
		for (i=0; i<ids.length; i++)
			this.grid.delRowData(ids[i]);
	},
	addRow: function(id,data) { // id = "key", data = column data
		data = data || false;
		if(!data) {
			data = id;
			id = data.id;	
		}
		this.grid.addRowData(id,data);
	},
	setRow: function(id,data) { // id = "key", data = column data
		data = data || false;
		if(!data) {
			data = id;
			id = data.id;	
		}
		this.grid.setRowData(id,data);
	},
	reset: function() {
		// todo; Add method to jqGrid which clears selection.
		return;
	}
}
</script>
{/literal}