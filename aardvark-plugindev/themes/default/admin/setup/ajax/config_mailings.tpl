{* Field Validation - see docs/template.txt documentation *}
{fv form='mailings'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="list_fromname"}
{fv validate="list_fromemail"}
{fv validate="list_frombounce"}
{fv validate="demo_mode"}
{fv validate="public_history"}
{fv validate="list_charset"}

<form action="{$smarty.server.PHP_SELF}" method="post">

<div>
<label for="list_fromname"><span class="required">{t}From Name:{/t}</span>{fv message="list_fromname"}</label>
<input type="text" name="list_fromname" value="{$list_fromname|escape}" />
<span class="notes">{t}(Default name mails will be sent from){/t}</span>
</div>

<div>
<label for="list_fromemail"><span class="required">{t}From Email:{/t}</span>{fv message="list_fromemail"}</label>
<input type="text" name="list_fromemail" value="{$list_fromemail|escape}" />
<span class="notes">{t}(Default email mails will be sent from){/t}</span>
</div>

<div>
<label for="list_frombounce"><span class="required">{t}Bounce Address:{/t}</span>{fv message="list_frombounce"}</label>
<input type="text" name="list_frombounce" value="{$list_frombounce|escape}" />
<span class="notes">{t}(Returned emails will be sent to this address){/t}</span>
</div>

<div>
<label for="demo_mode">{t}Demonstration Mode:{/t}{fv message="demo_mode"}</label>
<input type="radio" name="demo_mode" value="on"{if $demo_mode == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="demo_mode" value="off"{if $demo_mode != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(Toggle Demonstration Mode){/t}</span>
</div>

<div>
<label for="public_history">{t}Public Mailings{/t}{fv message="public_history"}</label>
<input type="radio" name="public_history" value="on"{if $public_history == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="public_history" value="off"{if $public_history != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t escape=no 1="<a href='`$url.base`user/mailings.php'>" 2='</a>'}(When on, the public can view past mailings at this %1URL%2){/t}</span>
</div>

<div>
<br clear="both" />
<a href="ajax/config_throttle.php" id="throttleTrigger"><img src="{$url.theme.shared}images/icons/right.png" alt="back icon" class="navimage" /> {t}Set mailing throttle values{/t}</a>
<span class="notes">{t}(controls mails per second, bytes per second, and domain limits){/t}</span>
<br clear="both" />
</div>

<div>
<label for="list_charset"><span class="required">{t}Character Set:{/t}</span>{fv message="list_charset"}</label>
<select name="list_charset">
<option value="UTF-8"{if $list_charset == 'UTF-8'} selected="selected"{/if}>{t}UTF-8 (recommended){/t}</option>
<option value="ISO-8859-1"{if $list_charset == 'ISO-8859-1'} selected="selected"{/if}>{t}Western (ISO-8859-1){/t}</option>
<option value="ISO-8859-15"{if $list_charset == 'ISO-8859-15'} selected="selected"{/if}>{t}Western (ISO-8859-15){/t}</option>
<option value="ISO-8859-2"{if $list_charset == 'ISO-8859-2'} selected="selected"{/if}>{t}Central/Eastern European (ISO-8859-2){/t}</option>
<option value="ISO-8859-7"{if $list_charset == 'ISO-8859-7'} selected="selected"{/if}>{t}Greek (ISO-8859-7){/t}</option>
<option value="ISO-2022-JP"{if $list_charset == 'ISO-2022-JP'} selected="selected"{/if}>{t}Japanese (ISO-2022-JP){/t}</option>
<option value="EUC-JP"{if $list_charset == 'EUC-JP'} selected="selected"{/if}>{t}Japanese (EUC-JP){/t}</option>
<option value="cp1251"{if $list_charset == 'cp1251'} selected="selected"{/if}>{t}cyrillic (Windows-1251){/t}</option>
<option value="KOI8-R"{if $list_charset == 'KOI8-R'} selected="selected"{/if}>{t}cyrillic (KOI8-R){/t}</option>
<option value="GB2312"{if $list_charset == 'GB2312'} selected="selected"{/if}>{t}Simplified Chinese (GB2312){/t}</option>
</select>
<span class="notes">{t}(Select Default Character Set of Mailings){/t}</span>
</div>

<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<div class="output alert">{if $output}{$output}{/if}</div>

</form>

{literal}
<script type="text/javascript">
$().ready(function(){
	$('#throttleWindow').jqm({
		overlay: 0,
		ajax: '@href',
		target: '.jqmdMSG',
		wrapClass: 'throttleWrap',
		trigger: '#throttleTrigger'
	}).jqDrag('div.jqmdTC');
});
</script>
{/literal}