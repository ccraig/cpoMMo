{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/effects.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/dragdrop.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/controls.js" type="text/javascript"></script>
{/capture}{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}




<div id="mainbar">

<h1>{t}Fields Page{/t}</h1>

<img src="{$url.theme.shared}images/icons/fields.png" class="articleimg">

{if $intro}<p>{$intro}</p>{/if}


<h2>{t}Fields{/t} &raquo;</h2>
  
{if $messages}
    <div class="msgdisplay">
    {foreach from=$messages item=msg}
   	 <div>* {$msg}</div>
    {/foreach}
    </div>
 {/if}
 
 <form action="" method="POST">
	<div class="field">
		<b>{t}Make New{/t} &raquo;</b>
		<input type="text" class="text"  title="{t}type new field name{/t}" maxlength="60" size="30" 
		name="field_name" id="field_name"  value="{t}type new field name{/t}" />
		<select name="field_type">
			<option value="text">{t}Text{/t}</option>
			<option value="number">{t}Number{/t}</option>
			<option value="checkbox">{t}Check Box{/t}</option>
			<option value="multiple">{t}Multiple Choice{/t}</option>
			<option value="date">{t}Date{/t}</option>
		</select>
		<input class="button" type="submit" value="{t}Add{/t}" />
	</div>
</form>	

{literal}
<style>
.handle {
	cursor: move;
}
</style>
{/literal}

<br>
	<span>{t}Delete{/t}</span>
	<span style="margin-left: 20px; width: 25px;">{t}Edit{/t}</span>
	<span style="margin-left: 20px; margin-right: 20px;">{t}Order{/t}</span>
	<span style="text-align:left; margin-left: 5px;">{t}Field Name{/t}</span>
	
	<div style="background-color: #cccccc; padding: 3px;">
		---
	 	<span style="margin-left: 40px;">---</span>
		<span style="margin-left: 40px; margin-right: 20px;">---</span>
		<span style="text-align:left; margin-left: 20px;"><strong>E-Mail</strong></span>
	 	
	</div>	
	
<div id="demoOrder">
	{foreach name=demos from=$fields key=key item=demo}
	<div id="demo_{$key}">
		<a href="{$smarty.server.PHP_SELF}?field_id={$key}&delete=TRUE&field_name={$demo.name}">
	 	 		<img src="{$url.theme.shared}images/icons/delete.png" border="0"></a>
		<span style="margin-left: 25px;">
		<a href="fields_edit.php?field_id={$key}">
				<img src="{$url.theme.shared}images/icons/edit.png" border="0"></a>
		</span>
		<span class="handle" style="margin-left: 25px; margin-right: 20px; "><img src="{$url.theme.shared}images/icons/order.png"></span>
		<span style="text-align:left; margin-left: 12px;">
		{if $demo.active == 'on'}<strong>{$demo.name}</strong>{else}{$demo.name}{/if}
				 ({$demo.type})
		</span>
	</div>	

	{foreachelse}
	 	<div><br><strong>{t}No fields have been assigned.{/t}</strong></div>
	{/foreach}
</div>

<br>
<div><li>{t escape=no}Names in <strong>bold</strong> are active.{/t}</li></div>
<div><li>{t}Change the ordering of fields on the subscription form by dragging and dropping the order icon{/t}</li></div>
 
<div id="ajaxOutput" class="msgdisplay"></div>
 
   
{literal}
<script type="text/javascript">
// <![CDATA[

Sortable.create('demoOrder',{tag:'div', handle: 'handle', onUpdate:function(){new Ajax.Updater('ajaxOutput', 'ajax_demoOrder.php', {onComplete:function(request){new Effect.Highlight('demoOrder',{});}, parameters:Sortable.serialize('demoOrder'), evalScripts:true, asynchronous:true})}});


// ]]>
</script>
{/literal}

</div>
<!-- end mainbar -->

{include file="admin/inc.footer.tpl"}