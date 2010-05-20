<div class="output">
{include file="inc/messages.tpl"}
</div>

<form class="json" action="{$smarty.server.PHP_SELF}" method="post">

<div class="alert">
{t}Templates allow you to re-use your crafted message bodies. The HTML and text version is remembered.{/t}
</div>

<p>{t escape=no 1="<strong>" 2="</strong>"}You may %1load%2 or %1delete%2 templates from here.{/t}</p>


<div>
<label for="template">{t}Template{/t}:</label>
<select name="template">
<option value="">{t}choose template{/t}</option>
{foreach from=$templates item=name key=key}
<option value="{$key}">{$name}</option>
{/foreach}
</select>
</div>


<div class="t_description" style="color: green; margin: 5px 12px;">
<strong>{t}Description{/t}:</strong>
<div>
{t}No template selected{/t}
</div>
</div>

<hr />

<div class="buttons">
<input type="submit" name="skip" value="{t}Skip{/t}" />
<input type="submit" name="load" value="{t}Load{/t}" />
<input type="submit" name="delete" value="{t}Delete{/t}" />
</div>

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
