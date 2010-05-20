<div class="output">
{include file="inc/messages.tpl"}
</div>

<div class="compose">
<h4>{t}HTML Message{/t}</h4>
<textarea name="body">{$body}</textarea>
<span class="notes">({t}Leave blank to send text only{/t})</span>
</div>

<ul class="inpage_menu">
<li><a href="#" id="e_toggle"><img src="{$url.theme.shared}images/icons/viewhtml.png" alt="icon" border="0" align="absmiddle" /><span id="toggleText">{t}Enable WYSIWYG{/t}</span></a></li>
<li><a href="mailing/ajax.personalize.php" id="e_personalize"><img src="{$url.theme.shared}images/icons/subscribers_tiny.png" alt="icon" border="0" align="absmiddle" /> {t}Add Personalization{/t}</a></li>
<li><a href="mailing/ajax.addtemplate.php" id="e_template"><img src="{$url.theme.shared}images/icons/edit.png" alt="icon" border="0" align="absmiddle" /> {t}Save as Template{/t}</a></li>
</ul>

<div class="compose">
<h4>{t}Text Version{/t}</h4>
<textarea name="altbody">{$altbody}</textarea>
<span class="notes">({t}Leave blank to send HTML only{/t})</span>
</div>

<form id="compose" class="json mandatory" action="{$smarty.server.PHP_SELF}" method="post">
<input type="hidden" name="compose" value="true" />
<ul class="inpage_menu">
<li><a href="#" id="e_altbody"><img src="{$url.theme.shared}images/icons/reload.png" alt="icon" border="0" align="absmiddle" /> {t}Copy text from HTML Message{/t}</a></li>
<li><input type="submit" id="submit" name="submit" value="{t}Continue{/t}" /></li>
</ul>
</form>

<script type="text/javascript">
var onText = '{t escape=js}Disable WYSIWYG{/t}';
var offText = '{t escape=js}Enable WYSIWYG{/t}';

$().ready(function() {ldelim}
	
	wysiwyg.init({ldelim}
		language: '{$lang}',
		baseURL: '{$url.theme.shared}../wysiwyg/',
		t_weblink: '{t escape=js}View this Mailing on the Web{/t}',
		t_unsubscribe: '{t escape=js}Unsubscribe or Update Records{/t}',
		textarea: $('textarea[@name=body]')
	{rdelim});
	
	{if $wysiwyg == 'on'}
		// Enable the WYSIWYG
		wysiwyg.enable();
		$('#toggleText').html(onText);
	{/if}
	
	{literal}
	
	// Command Buttons (toggle HTML, add personalization, save template, generate altbody)
	$('#e_toggle').click(function() {
		if(wysiwyg.enabled) {
			if(wysiwyg.disable()) {
				$('#toggleText').html(offText) 
				$.getJSON('mailing/ajax.rpc.php?call=wysiwyg&disable=true');
			}
		}
		else {
			if(wysiwyg.enable()) {
				$('#toggleText').html(onText);
				$.getJSON('mailing/ajax.rpc.php?call=wysiwyg&enable=true');
			}
		}
		return false;
	});
	
	$('#e_personalize').click(function() {
		$('#dialog').jqmShow(this);
		return false;
	});
	
	$('#e_template').click(function() {
		
		// submit the bodies
		var post = {
			body: wysiwyg.getBody(),
			altbody: $('textarea[@name=altbody]').val()
		},trigger = this;
		
		poMMo.pause();
		
		$.post('mailing/ajax.rpc.php?call=savebody',post,function(){
			$('#dialog').jqmShow(trigger);
			poMMo.resume();
		});
		
		return false;
	});
	
	
	$('#e_altbody').click(function() {
		
		var post = {
			body: wysiwyg.getBody()
		};
		
		poMMo.pause();
		
		$.post('mailing/ajax.rpc.php?call=altbody',post,function(json){
			$('textarea[@name=altbody]').val(json.altbody);
			poMMo.resume();
		},"json");
		
		return false;
	});
	
	
	$('#compose').submit(function() {
		// submit the bodies
		var post = {
			body: wysiwyg.getBody(),
			altbody: $('textarea[@name=altbody]').val()
		};
		
		poMMo.pause();
		
		$.post('mailing/ajax.rpc.php?call=savebody',post,function(){
			poMMo.resume();
		});
	});
	
});

</script>
{/literal}
