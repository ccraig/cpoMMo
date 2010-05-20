{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
 _editor_url  = "{$url.theme.shared}js/xinha/"; 
 _editor_lang = "en";
</script>

{if $ishtml == 'html'}
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

{/capture}{include file="admin/inc.header.tpl"}


<div style="position: relative; width: 100%; z-index: 1;">
	<a class="pommoOpen" href="#">{t}Add Personalization{/t}</a>
		<div id="selectField" style="z-index: 2; display: none; position: absolute; top: -5px; left: -5px; width: 90%; background-color: #e6eaff; padding: 7px; border: 1px solid;">
			<div class="pommoHelp">
				<img src="{$url.theme.shared}images/icons/help.png" align="absmiddle" border="0" style="float: right; margin-left: 10px;">
				<span style="font-weight: bold;">{t}Add Personalization{/t}: </span>
				<span class="pommoHelp">
				{t}Mailings can be personalized by adding subscriber field values to the body. For instance, you can have mailings begin with "Dear Susan, ..." instead of "Dear Subsriber, ...". The syntax for personalization is; [[field_name]] or [[field_name|default_value]]. If 'default_value' is supplied and a subscriber has no value for 'field_name', [[field_name|default_value]] will be replaced by default_value. If no default is supplied and no value exists for the field, [[...]] will be replaced with a empty (blank) string, allowing mailings to start with "Dear [[firstName|Friend]] [[lastName]]," (example assumes firstName and lastName are collected fields).{/t}
				</span>
				<hr style="clear: both;">
			</div>
			
			<select id="field">
				<option value="">{t}choose field{/t}</option>
				<option value="Email">Email</option>
				{foreach from=$fields key=id item=field}
				<option value="{$field.name}">{$field.name}</option>
				{/foreach}
			</select>
			
			<span style="margin-left: 20px; margin-right: 20px;"> Default: <input type="text" id="default"></span>
			
			<input id="insert" type="submit" value="{t}Insert{/t}">
			
			<br>
			
			<a class="pommoClose" href="#" style="float: right;">
					<img src="{$url.theme.shared}images/icons/left.png" align="absmiddle" border="0">{t}Go Back{/t}
			</a>				
		</div>
</div>

<form id="bForm" name="bForm" action="" method="POST">

{if $ishtml == 'html'}
	<SELECT name="editorType" onChange="xinhaSubmit()">
		<option value="wysiwyg">{t}Use WYSIWYG Editor{/t}</option>
		<option value="text" {if $editorType == 'text'}SELECTED{/if}>{t}Use Plain Text Editor{/t}</option>
	</SELECT>
	....... {t}Include alternative text body?{/t}
	<SELECT name="altInclude" onChange="xinhaSubmit()">
		<option value="yes">{t}Yes{/t}</option>
		<option value="no" {if $altInclude == 'no'}SELECTED{/if}>{t}No{/t}</option>
	</SELECT>
	...... &nbsp; <input class="button" id="bForm-submit" name="preview" type="submit" value="{t}Continue{/t}" />
	
	<hr>
	
	<fieldset>
		<legend>{t}HTML Message{/t}</legend>
		<textarea id="body" name="body" rows="10" cols="80" style="width: 100%;">{$body}</textarea>
	</fieldset>
	
	<br>
	
	{if $altInclude != 'no'}
	<fieldset>
		<legend>{t}Text Message{/t}</legend>
		
		<img src="{$url.theme.shared}images/icons/down.png" align="absmiddle">&nbsp; &nbsp; 
		<input type="submit" name="altGen" id="altGen" value="{t}Copy text from HTML Message{/t}" onClick="xinhaSubmit()">
		
		<textarea  rows="10" cols="80" name="altbody" id="altbody">{$altbody}</textarea>
	</fieldset>
	{/if}

{else}
  <fieldset>
    <legend>{t}Mailing Body{/t}</legend>

		<div class="field">
			<label for="body"><span class="required">{t}Message:{/t}</span></label>
			<textarea  rows="10" cols="80"  id="body" name="body" />{$body}</textarea>
		</div>
  </fieldset>
  
 <div>
	<input class="button" id="bForm-submit" name="preview" type="submit" value="{t}Continue{/t}" />
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
	
	
	$("#personalize").click(function() {
		$("#selectField").slideDown('slow', function() {
			$(this).find("a.pommoClose").click(function() {
					$("#selectField").slideUp('slow', function() { $(this).unclick(); });
					return false;
				});
			});
		return false;
		});
	***********/
	
	$("a.pommoOpen").click(function() { $(this).siblings("div").slideDown(); return false; });
		
	$("a.pommoClose").click(function() { $(this).parent().slideUp(); return false; });
	
	$("div.pommoHelp img").click(function() {
		$(this).parent().find("span.pommoHelp").toggle(); return false;
		});
		
	$("#insert").click(function() {		
		if ($("#field").val() == '') { 
			alert ('{/literal}{t}You must choose a field{/t}{literal}'); 
			return false; 
			}
		
		// sting to append
		var str = '[['+($("#field").val())+(($("#default").val() == '')? '' : '|'+$("#default").val())+']]';
		
		if (!xinha_enabled) {
			// append to plain text editor (regular textarea)
			$("#body").get(0).value += (str);
		}
		else {
			// append to xinha editor
			xinha_editors.body.insertHTML(str);
		}
		
		
		// hide dialog
		$("#field").add("#default").val("");
		$(this).parent().hide();
		
		return false;
	});
});
</script>
{/literal}


{include file="admin/inc.footer.tpl"}