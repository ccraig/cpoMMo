<div class="helpToggle">
<img src="{$url.theme.shared}images/icons/help.png" alt="help icon" style="float: left; margin: 0 10px; 0 5px;" />
<p>
{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}Mailings may be personalized per subscriber. You can inject %1subscriber field%2 values, clickable links, and tracking information.{/t}
</p>

<p>
{t}Personalizations allow you to write a message like "Dear [[firstName|Loyal Subscriber]], Happy New Year! .... follow this link to unsubscribe or update your records; [[!unsubscribe]]".{/t}
</p>
</div>

<hr />

<div id="personal">
	
	<div style="float: left;" class="alert">
		<input type="radio" name="type" value="field" checked="yes"/>{t}Personalization{/t} <br />
		<input type="radio" name="type" value="link" />{t}Link (URL){/t} <br />
		<input type="radio" name="type" value="track" />{t}Tracking (ID){/t} <br />
	</div>
	
	<div style="float: left; margin-left: 25px;">
		

		<div class="pType" name="field">
			<select>
			<option value="email">{t}Email{/t}</option>
			<option value="ip">{t}IP Address{/t}</option>
			<option value="registered">{t}Registered{/t}</option>
			{foreach from=$fields key=id item=field}
			<option value="{$field.name}">&nbsp;&nbsp;{$field.name}</option>
			{/foreach}
			</select>
		
			<p>
			<label for="default">{t}Default (optional; used if no value exists){/t}:</label><br />
			<input type="text" name="default" style="width: 200px;" />
			</p>
		</div>
			
			
		<div class="pType hidden" name="link">
			<select>
			<option value="!unsubscribe">{t}Unsubscribe or Update Records{/t}</option>
			<option value="!weblink">{t}View on Web (public mailings must be enabled){/t}</option>
			</select>
		</div>
		
		
		<div class="pType hidden" name="track">	
			<select >
			<option value="!subscriber_id">{t}Subscriber ID{/t}</option>
			<option value="!mailing_id">{t}Mailing ID{/t}</option>
			</select>
		</div>
		
		<div class="buttons">
		<button name="submit">{t}Insert{/t}</button>
		<button class="jqmClose">{t}Cancel{/t}</button>
		</div>
	
	</div>

</div>

{literal}
<script type="text/javascript">
$().ready(function(){
	$('#personal input[@type=radio]').change(function(){
		$('#personal div.pType').hide();
		$('#personal div.pType[@name='+this.value+']').show();
	});
	
	$('#personal button[@name=submit]').click(function(){
		
		// construct the value
		var vals = $('#personal div.pType:visible :input');
		var out = (vals.size()>1 && $(vals[1]).val() != '') ?
			'[['+$(vals[0]).val()+'|'+$(vals[1]).val()+']]' :
			'[['+$(vals[0]).val()+']]';
		
		// inject personalization into WYSIWYG	
		wysiwyg.inject(out);
		
		// close the dialog
		$('#dialog').jqmHide();
		
	});
});
</script>
{/literal}
	


