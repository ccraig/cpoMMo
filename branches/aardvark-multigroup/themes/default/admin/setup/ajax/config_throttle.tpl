<div id="tbody">

<form action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<legend>{t}Global Rates{/t}</legend>

<h2>{t}Mail Rate{/t}</h2>

<div class="slide">
<a href="#" class="minus">-</a>
<a href="#" class="plus">+</a>
<div id="mps" class="track"><div class="handle"></div></div>
<p>{t escape=no 1='<span class="out"></span> '}%1 per Second{/t}<br />{t escape=no 1='<span class="out"></span>'}%1 per Hour{/t}</p>
</div>

<h2>{t}Bandwidth Limit{/t}</h2>

<div class="slide">
<a href="#" class="minus">-</a>
<a href="#" class="plus">+</a>
<div id="bps" class="track"><div class="handle"></div></div>
<p>{t escape=no 1='<span class="out"></span> KB '}%1 per Second{/t}<br />{t escape=no 1='<span class="out"></span> MB'}%1 per Hour{/t}</p>
</div>

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

<fieldset>
<legend>{t}Rates per Domain{/t}</legend>

{t}In addition to global throttling, you can limit the number of bytes/mails a single domain receives per period. This is useful to prevent "slamming" of a host -- which can get you banned or marked as spam. As example, you can choose to send no more than 1 mail every 20 seconds to a domain by setting the mails to 1 and the period interval to 20. Warning; this setting will significantly delay a mailing if many of your subscribers use the same domain (e.g. @gmail.com).{/t}

<br />
	
<h2>{t}Period Interval{/t}</h2>

<div class="slide">
<a href="#" class="minus">-</a>
<a href="#" class="plus">+</a>
<div id="dp" class="track trackAlt"><div class="handle"></div></div>
<p>{t escape=no 1='<span class="out"></span> '}%1 seconds{/t}</p>
</div>

<h2>{t}Mail Rate{/t}</h2>

<div class="slide">
<a href="#" class="minus">-</a>
<a href="#" class="plus">+</a>
<div id="dmpp" class="track"><div class="handle"></div></div>
<p>{t escape=no 1='<span class="out"></span> '}%1 per Period{/t}</p>
</div>

<h2>{t}Bandwidth Limit{/t}</h2>

<div class="slide">
<a href="#" class="minus">-</a>
<a href="#" class="plus">+</a>
<div id="dbpp" class="track"><div class="handle"></div></div>
<p>{t escape=no 1='<span class="out"></span> KB '}%1 per Period{/t}</p>
</div>

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

<input type="hidden" name="throttle-submit" value="true" />
<input id="vmps" type="hidden" name="mps" value="true" />
<input id="vbps" type="hidden" name="bps" value="true" />
<input id="vdp" type="hidden" name="dp" value="true" />
<input id="vdmpp" type="hidden" name="dmpp" value="true" />
<input id="vdbpp" type="hidden" name="dbpp" value="true" />

</form>

</div>

{literal}
<script type="text/javascript">
$().ready(function(){

	assignForm($('#tbody')[0]);

	// .ready() doesn't seem to work .. slider vals are not initialized
	setTimeout("tInit()",200);
});

function tInit() {
	var slides={{/literal}
		mps: {$mps},
		bps: {$bps},
		dp: {$dp},
		dmpp: {$dmpp},
		dbpp: {$dbpp} {literal}};

	for (i in slides) {
		var o={
			accept: '.handle',
			values: [[slides[i],0]],
			onSlide: function(xp, yp, x, y) { var e=this.parentNode; tUpdate({type: e.id,x:x,e:e}); }};
		if (i == 'dmpp')
			o.fractions = 5;
		tUpdate({type: i, x: slides[i], e: $('#'+i).Slider(o)[0]});
	}

	$('a.minus').click(function(){ 
		tUpdate({e: $('../div',this).SliderSetValues([[-1,0]])});
		return false;
	});

	$('a.plus').click(function(){ 
		tUpdate({e: $('../div',this).SliderSetValues([[1,0]])}); 
		return false;
	});	
}

// this is necessary because there is no callback called after SliderSetValues()... =[
function tUpdate(o) { //console.log(o);
	var p=$.extend({e:false,type:false,x:false}, o);

	if(p.x === false) {
		var coords = p.e.SliderGetValues();
		p.x = coords[0][0][2];
		p.type = p.e.attr('id');
	}

	// get output element -- (paragragh sibling of slider element)
	var out=$('../p',p.e).find('span.out');

	var maxStr='{/literal}{t}No Limit{/t}{literal}';

	switch(p.type) {
		case 'mps':
			var v=p.x/40 || maxStr; var mph=v*60*60 || maxStr;
			out[0].innerHTML=v; out[1].innerHTML=mph;
			break;
		case 'bps':
			var v=p.x*2 || maxStr; var mbph=Math.round(v*60*60/1024) || maxStr;
			out[0].innerHTML=v; out[1].innerHTML=mbph;
			break;
		case 'dp':
			var v=Math.round(p.x/13+5) || maxStr; out.html(v);
			break;
		case 'dmpp':
			var v=Math.round(p.x/40) || maxStr; out.html(v);
			break;kbps
		case 'dbpp':
			var v=p.x || maxStr; out.html(v);
			break;
	}	

	// update form element
	$('#v'+p.type).val(v);
}
</script>
{/literal}