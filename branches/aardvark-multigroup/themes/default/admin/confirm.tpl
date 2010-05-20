{capture name=confirmMsg}
<div id="warnmsg" class="warn">
{if $confirm.msg}
<p>{$confirm.msg}</p>
{/if}
</div>

<p><strong>{t}Confirm your action.{/t}</strong></p>

<div style="float: left; margin:10px 30px;">
<a href="{$confirm.nourl}" class="jqmClose"><img src="{$url.theme.shared}images/icons/undo.png" alt="undo icon" class="navimage" /> {t}No{/t} {t}please return{/t}</a>
</div>

<div style="float: left; margin:10px 30px;">
<a href="{$confirm.yesurl}" class="_yes"><img src="{$url.theme.shared}images/icons/ok.png" alt="accept icon" class="navimage" /> {t}Yes{/t} {t}I confirm{/t}</a>
</div>
{/capture}

{if $confirm.ajaxConfirm}
<div id="confirmMsg" style="display: none;">
{$smarty.capture.confirmMsg}
</div>

{literal}
<script type="text/javascript">
$('#confirm')
	.find('.jqmdMSG')
		.html($('#confirmMsg').html())
		.end()
	.find('a,.jqmClose')
		.click(function(){
			if(this.className == '_yes') {
				$('#{/literal}{$confirm.targetID}{literal} div.jqmdMSG').load(this.href,function(){$('#confirm').jqmHide(); if(assignForm)assignForm(this);});
				$('#confirm div.jqmdMSG').html('{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...{literal}');
			}
			else
				$('#confirm').jqmHide();
			return false;
		})
		.end()
	.jqmShow();
</script>
{/literal}

{else}
{include file="inc/admin.header.tpl"}

{if $confirm.title}
<h2>{$confirm.title}</h2>
{else}
<h2>{t}Confirm{/t}</h2>
{/if}

{$smarty.capture.confirmMsg}

{include file="inc/admin.footer.tpl"}
{/if}