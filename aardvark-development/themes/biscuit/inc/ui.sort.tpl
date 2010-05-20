<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.mouse.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.sortable.js"></script>
{literal}
<script type="text/javascript">

PommoSort = {
	init: function(e,p) {
		this.params = $.extend(this.params,p);
		this.sortBox = $(e).sortable(this.params);
		return this;
	},
	params: {
		// ui.sortable params
		items: '.sortable',
		update: function(e,ui) {
			
			// re-stripe
			$('#grid').jqStripe({rowSelector: 'div.sortable'});
			
			var order = new Array();
			$(PommoSort.params.items,PommoSort.sortBox).each(function(){
				var id = this.id.substr(2);
				order.push(id);
			});
			if(PommoSort.params.updateURL) {
				$.getJSON(
					PommoSort.params.updateURL,
					{'order[]': order},
					function(json){ return; }
				);
			}
		},
		// PommoSort params
		updateURL: false
	},
	sortBox: null
};
</script>
{/literal}