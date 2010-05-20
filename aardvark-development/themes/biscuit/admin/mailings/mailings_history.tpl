{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
{include file="inc/ui.dialog.tpl"}
{include file="inc/ui.grid.tpl"}
{/capture}

{include file="inc/admin.header.tpl" sidebar='off'}

<h2><img src="{$url.theme.shared}images/icons/history.png" alt="history icon" class="navimage left" />{t}Mailings History{/t}</h2>

{t}View mailings that have already been sent, view last notices, or even reload previously sent emails to send again.{/t}

<br class="clear"/>

{include file="inc/messages.tpl"}

{if $tally > 0}
<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridPager" class="scroll" style="text-align:center;"></div><br />

	<div class="buttons">
		<a href="ajax/mailing_preview.php" class="modal visit"><img src="{$url.theme.shared}images/icons/mailing_small.png" alt="preview" />{t}View Mailing{/t}</a>
		<a href="ajax/history.rpc.php?call=notice" class="modal"><img src="{$url.theme.shared}images/icons/examine_small.png" alt="view last" />{t}View Last Notices{/t}</a>
		<a href="ajax/history.rpc.php?call=reload" class="modal visit"><img src="{$url.theme.shared}images/icons/reload-small.png" alt="reload" />{t}Reload Checked{/t}</a>
		<a href="ajax/history.rpc.php?call=delete" class="modal confirm"><img src="{$url.theme.shared}images/icons/cross.png"alt="delete" />{t}Delete Checked{/t}</a>
	</div>

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