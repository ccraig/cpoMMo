{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.grid.tpl"}
{/capture}

{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Mailings History{/t}</h2>

{include file="inc/messages.tpl"}

{if $tally > 0}
<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div>

<ul class="inpage_menu">
<li><a href="ajax/mailing_preview.php" class="modal visit"><img src="{$url.theme.shared}images/icons/mailing_small.png"/>{t}View Mailing{/t}</a></li>
<li><a href="ajax/history.rpc.php?call=notice" class="modal"><img src="{$url.theme.shared}images/icons/examine_small.png"/>{t}View Last Notices{/t}</a></li>
<li><a href="ajax/history.rpc.php?call=reload" class="modal visit"><img src="{$url.theme.shared}images/icons/typewritter_small.png"/>{t}Reload Checked Mailing{/t}</a></li>
<li><a href="ajax/history.rpc.php?call=delete" class="modal confirm"><img src="{$url.theme.shared}images/icons/delete.png"/>{t}Delete Checked Mailings{/t}</a></li>
</ul>

<script type="text/javascript">
$().ready(function() {ldelim}	
	
	var p = {ldelim}	
	colNames: [
		'ID',
		'{t escape=js}Subject{/t}',
		'{t escape=js}Group (count){/t}',
		'{t escape=js}Sent{/t}',
		'{t escape=js}Started{/t}',
		'{t escape=js}Finished{/t}',
		'{t escape=js}Status{/t}'
	],
	{literal}
	colModel: [
		{name: 'id', index: 'id', hidden: true, width: 1},
		{name: 'subject', width: 150},
		{name: 'group', width: 120},
		{name: 'sent', width: 40},
		{name: 'start', width: 130},
		{name: 'end', width: 130},
		{name: 'status', width: 70}
	],
	url: 'ajax/history.list.php'
	};
	
	poMMo.grid = PommoGrid.init('#grid',p);
});
</script>

<script type="text/javascript">
$().ready(function(){
	
	// Setup Modal Dialogs
	PommoDialog.init();

	$('a.modal').click(function(){
		var rows = poMMo.grid.getRowIDs();
		if(rows) {
			
			// check for confirmation
			if($(this).hasClass('confirm') && !poMMo.confirm())
				return false;
				
			// serialize the data
			var data = $.param({'mailings[]': rows});
			
			// rewrite the HREF of the clicked element
			var oldHREF = this.href;
			this.href += (this.href.match(/\?/) ? "&" : "?") + data
			
			// trigger the modal dialog, or visit the URL
			if($(this).hasClass('visit'))
				window.location = this.href;
			else
				$('#dialog').jqmShow(this);
			
			// restore the original HREF
			this.href = oldHREF;
			
			poMMo.grid.reset();
			
		}
		return false;
	});
});

poMMo.callback.deleteMailing = function(p) {
	poMMo.grid.delRow(p.ids);
	$('#dialog').jqmHide();  		
}

</script>
{/literal}

{else}
<strong>{t}No records returned.{/t}</strong>
{/if}

{capture name=dialogs}
{include file="inc/dialog.tpl" id=dialog wide=true tall=true}
{/capture}

{include file="inc/admin.footer.tpl"}