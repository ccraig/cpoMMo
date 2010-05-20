<div>
<p>{t escape=no 1="<a href=\"`$url.base`admin/setup/setup_fields.php\">" 2="</a>"}Mailings may be personalized per subscriber. You can inject %1subscriber field%2 values, clickable links, and tracking information.{/t}</p>

<p>{t}Personalizations allow you to write a message like "Dear [[firstName|Loyal Subscriber]], Happy New Year! .... follow this link to unsubscribe or update your records; [[!unsubscribe]]".{/t}
</p>
</div>

<hr class="hr" />

<div id="personal">
	
	<div style="float: left; padding: 20px 50px 20px 50px; line-height: 28px;" class="alert">
		<input type="radio" name="type" value="field" checked="yes"/>&nbsp;{t}Personalization{/t} <br />
		<input type="radio" name="type" value="link" />&nbsp;{t}Link (URL){/t} <br />
		<input type="radio" name="type" value="track" />&nbsp;{t}Tracking (ID){/t} <br />
	</div>
	
	<div style="float: left; margin-left: 35px; padding-top: 20px;">
		

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
			<input type="text" name="default" style="width: 300px;" />
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
        
        <br class="clear">
	        
        <div class="buttons">
			<button type="submit" id="submit" name="submit" value="{t}Insert{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="save"/>{t}Insert{/t}</button>
			<button type="submit" class="jqmClose negative" value="{t}Cancel{/t}" ><img src="{$url.theme.shared}/images/icons/cross.png" alt="cancel"/>{t}Cancel{/t}</button>
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
	


