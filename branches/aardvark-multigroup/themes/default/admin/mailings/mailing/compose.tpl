{if $success}
<input type="hidden" id="success" value="{$success}" />
<img src="{$url.theme.shared}images/loader.gif" alt="Loading Icon" title="Please Wait" border="0" />{t}Please Wait{/t}...
{php}return;{/php}
{/if}

<div class="output">
{include file="inc/messages.tpl"}
</div>

<form action="{$smarty.server.PHP_SELF}" method="post">

<div class="compose">
<h4>{t}HTML Message{/t}</h4>
<textarea name="body" {if $wysiwyg == 'on'}class="wysiwyg"{/if}>{$body}</textarea>
<span class="notes">({t}Leave blank to send text only{/t})</span>
</div>

<ul class="inpage_menu">
<li><a href="#" id="e_toggle"><img src="{$url.theme.shared}images/icons/viewhtml.png" alt="icon" border="0" align="absmiddle" /> {t escape=no 1="<span id='toggleText'>$toggleText</span>"}Toggle %1{/t}</a></li>
<li><a href="#" id="e_specialLink">{t}Insert Special Link{/t}</a></li>
<li><a href="#" id="e_personalize">{t}Add Personalization{/t}</a></li>
<li><a href="#" id="e_template"><img src="{$url.theme.shared}images/icons/edit.png" alt="icon" border="0" align="absmiddle" /> {t}Save as Template{/t}</a></li>
</ul>

<div class="compose">
<h4>{t}Text Version{/t}</h4>
<textarea name="altbody">{$altbody}</textarea>
<span class="notes">({t}Leave blank to send HTML only{/t})</span>
</div>

<ul class="inpage_menu">
<li><a href="#" id="e_altbody"><img src="{$url.theme.shared}images/icons/reload.png" alt="icon" border="0" align="absmiddle" /> {t}Copy text from HTML Message{/t}</a></li>
<li><input type="submit" id="submit" name="submit" value="{t}Continue{/t}" /></li>
</ul>


</form>

{literal}
<script type="text/javascript">
$().ready(function() {
	var scope = $('form');
	var toggle = $('#toggleText');
	
	$('#e_toggle').click(function() {
		if(toggle.html() == 'WYSIWYG') {
			pommo.sendAjax('mailing/ajax.wysiwyg.php?wysiwyg=on');
			$('textarea[@name=body]',scope).addClass('wysiwyg');
			pommo.makeTiny(scope);
			toggle.html('HTML');
		}
		else {
			pommo.sendAjax('mailing/ajax.wysiwyg.php?wysiwyg=off');
			pommo.brakeTiny(scope);
			toggle.html('WYSIWYG');
		}
		
		return false;
	});
	
	$('#e_altbody').click(function() {
		
		var body = {body: (pommo.isTiny.length > 0) ?
			tinyMCE.getContent() : $('textarea[@name=body]',scope).val()}

		$('#wait').jqmShow();
			
		$.ajax({
			type: "POST",
			url: "mailing/ajax.altbody.php",
			data: body,
			dataType: 'json',
			success: function(json){
				$('textarea[@name=altbody]',scope).val(json.altbody);
				$('#wait').jqmHide();
			}
		});
			
		return false;
	});
	
	$('#e_personalize').click(function() {
		$('#personalize').jqmShow();
		return false;
	});
	
	$('#e_specialLink').click(function() {
		$('#specialLink').jqmShow();
		return false;
	});
	
	$('#e_template').click(function() {
		pommo.bodySubmit(scope,function() {
			$('#addTemplate').jqmShow();
		});
		return false;
	});
	
	$('#submit').click(function() {
		pommo.clickedTab = $('#mailing ul li:last-child:eq(0) a');
		pommo.bodySubmit(scope);
		return false;
	});
});
</script>
{/literal}
