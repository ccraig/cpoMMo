{capture name=head}{* used to inject content into the HTML <head> *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/pommo.js"></script>
{include file="inc/ui.grid.tpl"}
{/capture}
{include file="inc/user.header.tpl" sidebar='off'}

<h2>{t}Mailings History{/t}</h2>

{include file="inc/messages.tpl"}

{if $tally > 0}
<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div>

<ul class="inpage_menu">
<li><a href="ajax/mailing_preview.php" class="visit"><img src="{$url.theme.shared}images/icons/mailing_small.png"/>{t}View Mailing{/t}</a></li>
</ul>

<script type="text/javascript">
$().ready(function() {ldelim}	
	
	var p = {ldelim}	
	colNames: [
		'ID',
		'{t escape=js}Subject{/t}',
		'{t escape=js}Sent{/t}'
	],
	rowNum: {$state.limit},
	rowList: [],
	{literal}
	colModel: [
		{name: 'id', index: 'id', hidden: true, width: 1},
		{name: 'subject', width: 150},
		{name: 'start', width: 130}
	],
	url: 'ajax/mailing.list.php'
	};
	
	poMMo.grid = PommoGrid.init('#grid',p);
});
</script>

<script type="text/javascript">
$().ready(function(){
	$('a.visit').click(function(){
		var rows = poMMo.grid.getRowIDs();
		if(rows) {
			// serialize the data
			var data = $.param({'mailings[]': rows});
			
			// rewrite the HREF of the clicked element
			var oldHREF = this.href;
			this.href += (this.href.match(/\?/) ? "&" : "?") + data
			
			window.location = this.href;
		}
		return false;
	});
});

</script>
{/literal}

{else}
<strong>{t}No records returned.{/t}</strong>
{/if}

{include file="inc/user.footer.tpl"}