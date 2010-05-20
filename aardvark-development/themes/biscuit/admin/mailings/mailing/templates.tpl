<form class="json" action="{$smarty.server.PHP_SELF}" method="post">

<div class="alert">
{t}Templates allow you to re-use your crafted message bodies. The HTML and text version is remembered.{/t}<br />
{t escape=no 1="<strong>" 2="</strong>"}You may %1load%2 or %1delete%2 templates from here.{/t}
</div>

<div class="formSpacing">
<label for="template">{t}Template{/t}:</label>
<select name="template">
<option value="">{t}choose template{/t}</option>
{foreach from=$templates item=name key=key}
<option value="{$key}">{$name}</option>
{/foreach}
</select>
</div>

<hr class="hr" />

<div class="t_description">
<strong>{t}Template Description{/t}:</strong>
<div>
{t}No template selected{/t}
</div>
</div>

<hr class="hr" />

<div class="buttons">
<button type="submit" name="skip" value="{t}Skip{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/continue.png" alt="skip"/>{t}Skip{/t}</button>
<button type="submit" name="load" value="{t}Load{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/load.png" alt="load"/>{t}Load{/t}</button>
<button type="submit" name="delete" value="{t}Delete{/t}" class="negative"><img src="{$url.theme.shared}/images/icons/cross.png" alt="delete"/>{t}Delete{/t}</button>
</div>

<br class="clear">

<div class="output">{include file="inc/messages.tpl"}</div>

<div class="clear"></div>
</form>

{literal}
<script type="text/javascript">
$().ready(function() {
	var scope = $('form.json');
	
	$('select',scope).change(function(){
		var v = $(this).val();
		if(v == '') 
			$('div.t_description div',scope).html('{/literal}{t}No template selected{/t}{literal}');
		else
		$('div.t_description div',scope)
			.html('{/literal}<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{literal}')
			.load('mailing/ajax.rpc.php?call=getTemplateDescription&id='+v);
	});
	
	// called as success callback from form submission
	poMMo.callback.deleteTemplate = function(p) {
		// remove the deleted option (p[0])
		$('option[@value='+p.id+']',scope).remove();
		
		// output the passed message (p[1])
		$('div.output').addClass('error').html(p.msg);
	}
	
});
</script>
{/literal}
