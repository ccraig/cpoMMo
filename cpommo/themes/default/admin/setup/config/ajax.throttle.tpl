<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<legend>{t}Global Rates{/t}</legend>

<div>
<h2>{t}Mail Rate{/t}</h2>
<div id="mps" class="ui-slider">
	<div class="ui-slider-handle"></div>	
</div>
<p>{t escape=no 1='<span></span> '}%1 per Second{/t}<br />{t escape=no 1='<span></span>'}%1 per Hour{/t}</p>
</div>


<div>
<h2>{t}Bandwidth Limit{/t}</h2>
<div id="bps" class="ui-slider">
	<div class="ui-slider-handle"></div>	
</div>
<p>{t escape=no 1='<span></span> KB '}%1 per Second{/t}<br />{t escape=no 1='<span></span> MB'}%1 per Hour{/t}</p>
</div>

</fieldset>


<div class="output alert">{if $output}{$output}{/if}</div>

<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="throttle_restore" value="{t}Restore Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />

<input type="hidden" name="throttle-submit" value="true" />

<div id="inputs" class="hidden">
	<input type="hidden" name="mps" value="true" />
	<input type="hidden" name="bps" value="true" />
	<input type="hidden" name="dp" value="true" />
	<input type="hidden" name="dmpp" value="true" />
	<input type="hidden" name="dbpp" value="true" />
</div>

<fieldset>
<legend>{t}Rates per Domain{/t}</legend>

{t}You may also limit the amount of mail a single domain receives in a period. This is useful for larger mailings, and prevents the "slamming" of the domain (which can get your mails rejected). As example, you can choose to send no more than 1 mail every 20 seconds to a domain by setting the mails to 1 and the period interval to 20. Warning; this setting will significantly delay a mailing if many of your subscribers use the same domain (e.g. @yahoo.com).{/t}

<br />

<div>
<h2>{t}Period Interval{/t}</h2>
<div id="dp" class="ui-slider ui-slider-alt">
	<div class="ui-slider-handle"></div>	
</div>
<p>{t escape=no 1='<span></span> '}%1 seconds{/t}</p>
</div>


<div>
<h2>{t}Mail Rate{/t}</h2>
<div id="dmpp" class="ui-slider ui-slider-alt">
	<div class="ui-slider-handle"></div>	
</div>
<p>{t escape=no 1='<span></span> '}%1 per Period{/t}</p>
</div>

<div>
<h2>{t}Bandwidth Limit{/t}</h2>
<div id="dbpp" class="ui-slider ui-slider-alt">
	<div class="ui-slider-handle"></div>	
</div>
<p>{t escape=no 1='<span></span> KB '}%1 per Period{/t}</p>
</div>

</fieldset>

<div class="output alert">{if $output}{$output}{/if}</div>
<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />


</form>

{literal}
<script type="text/javascript">

var maxStr='{/literal}{t}No Limit{/t}{literal}';

PommoSlider.onSlide = function(slider, v) {
	var out = $(slider).siblings().find('span');
	switch(slider.id) {
		case 'mps':
			out[0].innerHTML=(v > 0) ? Math.round(v/60*10000)/10000 : maxStr;
			out[1].innerHTML=(v > 0) ? (v/60)*60*60 : maxStr;
			break;
		case 'bps':
			out[0].innerHTML=(v > 0) ? v : maxStr;
			out[1].innerHTML=(v > 0) ? Math.round(v*60*60/1024) : maxStr;
			break;
		case 'dp':
		case 'dmpp':
		case 'dbpp':
			out[0].innerHTML=(v > 0) ? v : maxStr;
			break;
	};
	var val = (out[0].innerHTML == maxStr) ? 0 : out[0].innerHTML;
	$('#inputs input[@name='+slider.id+']').val(val);
};


$('div.ui-slider').each(function(){
	var p = {};
	switch(this.id) {
		case 'mps':
			p.maxValue = 300;
			p.minValue = 0;
			p.startValue = {/literal}{$mps}{literal};
			break;
		case 'bps':
			p.maxValue = 400;
			p.minValue = 0;
			p.startValue = {/literal}{$bps}{literal};
			break;
		case 'dp':
			p.maxValue = 20;
			p.minValue = 5;
			p.startValue = {/literal}{$dp}{literal};
			break;
		case 'dmpp':
			p.maxValue = 5;
			p.minValue = 0;
			p.startValue = {/literal}{$dmpp}{literal};
			break;
		case 'dbpp':
			p.maxValue = 400;
			p.minValue = 0;
			p.startValue = {/literal}{$dbpp}{literal};
			break;
			
	}
	PommoSlider.onSlide(this,p.startValue);
	PommoSlider.init($(this),p);
});
</script>
{/literal}