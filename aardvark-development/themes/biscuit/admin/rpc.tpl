{include file="inc/messages.tpl"}

{if $callbackFunction}
<script type="text/javascript">
	if($.isFunction(poMMo.callback.{$callbackFunction}))
		poMMo.callback.{$callbackFunction}({$callbackParams});
</script>
{/if}