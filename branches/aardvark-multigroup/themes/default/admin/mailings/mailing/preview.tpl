{if $success}
<input type="hidden" id="redirect" value="{$success}" />
<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...
{literal}
<script type="text/javascript">
var v=$('#redirect').val().toString();
switch(v) {
	case '1':
	case '2':
	case '3':
		setTimeout("$('#mailing').triggerTab("+v+")",400);
		break;
	default:
		window.location = v;
};
</script>
{/literal}
{php}return;{/php}
{/if}

<h4>{t}Preview Mailing{/t}</h4>

{include file="inc/messages.tpl"}

<div class="msgpreview">
	<p><strong>{t}Subject:{/t}</strong> <tt>{$subject}</tt></p>
	
	<p><strong>{t}To:{/t}</strong> <span style="color: #EA2B2B; font-size: 120%;">{$group}</span> (<em>{$tally}</em> {t}recipients{/t})</p>
	
	<p><strong>{t}From:{/t}</strong> {$fromname} <tt>&lt;{$fromemail}&gt;</tt></p>
	
	{if $fromemail != $frombounce}
	<p><strong>{t}Bounces:{/t}</strong> <tt>&lt;{$frombounce}&gt;</tt></p>
	{/if}
	
	<p><strong>{t}Character Set:{/t}</strong> <tt>{$list_charset}</tt></p>
</div>


<ul class="inpage_menu">
<li><a href="#" id="e_test"><img src="{$url.theme.shared}images/icons/world_test.png" alt="icon" border="0" align="absmiddle" />{t}Send Test{/t}</a></li>
<li><a href="#" id="e_send"><img src="{$url.theme.shared}images/icons/world.png" alt="icon" border="0" align="absmiddle" />{t}Send Mailing{/t}</a></li>
</ul>


<h4>{t}Message{/t}</h4>

<div class="msgpreview">
	{if $ishtml == 'on'}
	<strong>{t}HTML Message{/t}</strong> :
	<a href="ajax/mailing_preview.php" title="{t}Preview Message{/t}" onclick="return !window.open(this.href)">{t}Click Here{/t}</a>
	<hr />
	{/if}
	{$altbody}
</div>


{literal}
<script type="text/javascript">
$().ready(function() {
	
	$('#e_test').click(function() {
		$('#testMailing').jqmShow();
		return false;
	});
	
	$('#e_send').click(function() {
		// reload the tab (fragment == tab container)
		$('#wait').jqmShow();
		$(this).parents('div.fragment:first')
			.load('mailing/preview.php?sendaway=true', function(){$('#wait').jqmHide();});
		return false;
	});
	
});
</script>
{/literal}