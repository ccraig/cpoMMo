{capture name=head}
{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />

<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript">
	_editor_url  = "{$url.theme.shared}js/xinha/";
	_editor_lang = "en";
</script>

{if $ishtml == 'on'}
	{if $editorType == 'text'}
		<script type="text/javascript" language="javascript">
			var xinha_enabled = false;
			function xinhaSubmit() {ldelim}
				document.bForm.submit();
				return true;
			{rdelim}
		</script>
	{else}
		<script type="text/javascript" src="{$url.theme.shared}js/xinha/htmlarea.js"></script>
		<script type="text/javascript" src="{$url.theme.shared}js/xinha/config.js"></script>
		<script type="text/javascript" language="javascript">
			var xinha_enabled = true;
			function xinhaSubmit() {ldelim}
				document.bForm.onsubmit();
				document.bForm.submit();
				return true;
			{rdelim}
			$(function() {ldelim}
				xinha_init();
			{rdelim});
		</script>

	{/if}
{else}
	<script type="text/javascript" language="javascript">
		var xinha_enabled = false;
	</script>
{/if}

{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

{include file="inc/messages.tpl"}

<a href="#" id="mergeopen">{t}Add Personalization{/t}</a>

<div id="mailmerge">

<a href="#" id="mergeclose">{t}Close{/t}</a>

<div id="mergehelp">

<img src="{$url.theme.shared}images/icons/help.png" alt="help icon" />

<p>{t escape=no 1='<tt>' 2='</tt>' 3='&hellip;'}Mailings can be personalized by adding subscriber field values to the body. For instance, you can have mailings begin with "Dear Susan, %3" instead of "Dear Subscriber, %3". The syntax for personalization is; %1[[field_name]]%2 or %1[[field_name|default_value]]%2. If 'default_value' is supplied and a subscriber has no value for 'field_name', %1[[field_name|default_value]]%2 will be replaced by %1default_value%2. The %1[[%3]]%2 will be erased and replaced with nothing if a default value is not supplied and the subscriber field value does not exist. Thus you can start a mailing with %1Dear [[firstName|Friend]] [[lastName]],%3%2 providing you collect 'firstName' and 'lastName' fields.{/t}</p>

</div>

<div id="selectField">

<div>
<label for="field">{t}Insert field{/t}:</label>
<select id="field">
<option value="">{t}choose field{/t}</option>
<option value="Email">{t}Email{/t}</option>
{foreach from=$fields key=id item=field}
<option value="{$field.name}">{$field.name}</option>
{/foreach}
</select>
</div>

<div>
<label for="default">{t}Default value{/t}:</label>
<input type="text" id="default" />
</div>

<div class="buttons">

<input type="submit" id="insert" value="{t}Insert{/t}" />

</div>			

</div>

</div>

<form method="post" action="" id="bForm" name="bForm">

{if $ishtml == 'on'}
<fieldset>
<legend>{t}Formating options{/t}</legend>

<div>
<label for="editorType">{t}Editor type{/t}:</label>
<select name="editorType" id="editorType" onchange="xinhaSubmit()">
<option value="wysiwyg" title="What You See Is What You Get">WYSIWYG</option>
<option value="text"{if $editorType == 'text'} selected="selected"{/if}>{t}plain text{/t}</option>
</select>
</div>

<div>
<label for="altInclude">{t}Alternative Text Body{/t}</label>
<select name="altInclude" id="altInclude" onchange="xinhaSubmit()">
<option value="yes">{t}Include{/t}</option>
<option value="no"{if $altInclude == 'no'} selected="selected"{/if}>{t}Exclude{/t}</option>
</select>
</div>

</fieldset>

<fieldset>
<legend>{t}HTML Message{/t}</legend>
<div>
<textarea id="body" name="body" rows="10" cols="120" style="width: 100%;">{$body}</textarea>
</div>
<div><span class="error">{validate id="body" message=$formError.body}</span></div>

</fieldset>

{if $altInclude != 'no'}
<fieldset>
<legend>{t}Text Message{/t}</legend>

<button type="submit" name="altGen" id="altGen" onclick="xinhaSubmit()">
<img src="{$url.theme.shared}images/icons/down.png" alt="down icon" /> {t}Copy text from HTML Message{/t}
</button>

<div>
<textarea rows="10" cols="120" name="altbody" id="altbody">{$altbody}</textarea>
</div>

</fieldset>
{/if}

<div class="buttons">

<input type="submit" id="bForm-submit" name="preview" value="{t}Continue{/t}" />
<a href="mailings_send.php">{t}Cancel{/t}</a>

</div>

{else}
<fieldset>

<legend>{t}Mailing Body{/t}</legend>

<div>
<label for="body"><span class="required">{t}Message:{/t}</span></label>
<textarea rows="10" cols="120" id="body" name="body">{$body}</textarea>
</div>
<div><span class="error">{validate id="body" message=$formError.body}</span></div>

</fieldset>

<div class="buttons">

<input type="submit" id="bForm-submit" name="preview" value="{t}Continue{/t}" />
<a href="mailings_send.php">{t}Cancel{/t}</a>

</div>

{/if}

</form>

{literal}
<script type="text/javascript">
$(function() {

	/********

	$("#altGen").click(function(){
		$("#altbody").val(xinha_editors.body.getHTML());
		return false;
	});

	***********/

	function displayMailMerge() {
		$("#mergeopen").toggleClass('selected');
		$("#mailmerge").toggle(); return false;
	}

	$("#mergeopen").click(function() { displayMailMerge(); });
	$("#mergeclose").click(function() { displayMailMerge();	});

	$("#mergehelp img").click(function() {
		$("#mergehelp p").toggle(); return false;
	});

	$("#insert").click(function() {
		if ($("#field").val() == '') {
			alert ('{/literal}{t}You must choose a field{/t}{literal}');
			return false;
		}

		// sting to append
		var str = '[['+($("#field").val())+(($("#default").val() == '') ? '' : '|'+$("#default").val())+']]';

		if (!xinha_enabled) {
			// append to plain text editor (regular textarea)
			$("#body").get(0).value += (str);
		}
		else {
			// append to xinha editor
			xinha_editors.body.insertHTML(str);
		}

		// hide dialogue
		displayMailMerge();
		$("#field").add("#default").val("");

		return false;
	});
});
</script>
{/literal}
{include file="inc/admin.footer.tpl"}