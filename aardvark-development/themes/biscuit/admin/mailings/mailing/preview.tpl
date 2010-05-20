<h3>{t}Mailing Details{/t}</h3>

<form id="sendForm" class="json" action="{$smarty.server.PHP_SELF}" method="post">
	<div class="output">{include file="inc/messages.tpl"}</div>
	<input type="hidden" name="sendaway" value="true">
</form>

<div class="msgpreview">
	<p><strong>{t}Subject:{/t}</strong> {$subject}</p>
	
	<p><strong>{t}To:{/t}</strong> <span style="color: #EA2B2B; font-size: 120%;">{$group}</span> <em>({$tally} {t}recipients{/t})</em></p>
	
	<p><strong>{t}From:{/t}</strong> {$fromname} <em>({$fromemail})</em></p>
	
	{if $fromemail != $frombounce}
	<p><strong>{t}Bounces:{/t}</strong> {$frombounce}</p>
	{/if}
	
	<p><strong>{t}Character Set:{/t}</strong> {$list_charset}</p>
</div>


<div class="buttons" style="float:right;">
<a href="mailing/ajax.mailingtest.php" id="e_test" class="positive"><img src="{$url.theme.shared}images/icons/world_test.png" alt="test" />{t}Send Test{/t}</a>
<a href="#" id="e_send" class="positive"><img src="{$url.theme.shared}images/icons/send_mail-small.png" alt="send email" />{t}Send Mailing{/t}</a>
</div>

<div class="clear"></div>

<h3>{t}Preview Message{/t}</h3>

<div class="msgpreview">
	{if $ishtml == 'on'}
	<strong>{t}HTML Message{/t}</strong> :
	<a href="ajax/mailing_preview.php" title="{t}Preview Message{/t}" onclick="return !window.open(this.href)">{t}Click Here{/t}</a>
	<hr class="hr" />
	{/if}
	<strong>{t}Text Version{/t}</strong>: <br /><br />
	<div>{$altbody}</div>
</div>


{literal}
<script type="text/javascript">
$().ready(function() {
	
	$('#e_test').click(function() {
		$('#dialog').jqmShow(this);
		return false;
	});
	
	$('#e_send').click(function() {
		$('#sendForm').submit();
		return false;
	});
});
</script>
{/literal}