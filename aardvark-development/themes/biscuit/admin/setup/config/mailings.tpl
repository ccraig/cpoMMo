{* Field Validation - see docs/template.txt documentation *}
{fv form='mailings'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="list_fromname"}
{fv validate="list_fromemail"}
{fv validate="list_frombounce"}
{fv validate="demo_mode"}
{fv validate="public_history"}
{fv validate="list_charset"}
{fv validate="maxRuntime"}

{include file="inc/ui.tooltips.tpl"}

<form action="{$smarty.server.PHP_SELF}" method="post" class="json">

<div class="formSpacing">
<label for="list_fromname"><strong class="required">{t}Default From Name:{/t}&nbsp;</strong>{fv message="list_fromname"}</label>
<input type="text" name="list_fromname" value="{$list_fromname|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}default name mailings will be sent from{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(default name mails will be sent from){/t}</span>-->
</div>

<div class="formSpacing">
<label for="list_fromemail"><strong class="required">{t}Default From Email:{/t}&nbsp;</strong>{fv message="list_fromemail"}</label>
<input type="text" name="list_fromemail" value="{$list_fromemail|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}default email mailings will be sent from{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(default email mails will be sent from){/t}</span>-->
</div>

<div class="formSpacing">
<label for="list_frombounce"><strong class="required">{t}Bounce Address:{/t}&nbsp;</strong>{fv message="list_frombounce"}</label>
<input type="text" name="list_frombounce" value="{$list_frombounce|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}returned emails will be sent to this address{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(returned emails will be sent to this address){/t}</span>-->
</div>

<div class="formSpacing">
<label for="list_charset"><strong class="required">{t}Character Set:{/t}&nbsp;</strong>{fv message="list_charset"}</label>
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
</select>&nbsp;<a href="#" class="tooltip" title="{t}select default character set of mailings{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(select default character set of mailings){/t}</span>-->
</div>

<div class="formSpacing">
<label for="maxRuntime"><strong class="required">{t}Runtime (seconds):{/t}&nbsp;</strong>{fv message="maxRuntime"}</label>
<input type="text" name="maxRuntime" value="{$maxRuntime|escape}" size="4" maxlength="5" />&nbsp;<a href="#" class="tooltip" title="{t}seconds a processing script runs for: default: 80 secs, minimum: 15 secs{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(seconds a processing script runs for: default: 80 secs, minimum: 15 secs){/t}</span>-->
</div>

<div class="formSpacing">
<a href="config/ajax.throttle.php" id="throttleTrigger">{t}Set mailing throttle values{/t}</a>&nbsp;&nbsp;<a href="#" class="tooltip" title="{t}controls mails per second, bytes per second, and domain limits{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(controls mails per second, bytes per second, and domain limits){/t}</span>-->
</div>

<div class="formSpacing">
<label for="public_history">{t}Public Mailings:{/t}&nbsp;{fv message="public_history"}</label>
<input type="radio" name="public_history" value="on"{if $public_history == 'on'} checked="checked"{/if} /> {t}on{/t}&nbsp;&nbsp;
<input type="radio" name="public_history" value="off"{if $public_history != 'on'} checked="checked"{/if} /> {t}off{/t}&nbsp;&nbsp;<a href="{$url.base}/user/mailings.php" class="tooltip" title="{t}when on, the public can view past mailings at this %1URL%2{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t escape=no 1="<a href='`$url.base`user/mailings.php'>" 2='</a>'}(when on, the public can view past mailings at this %1URL%2){/t}</span>-->
</div>

<div class="formSpacing">
<label for="demo_mode">{t}Demonstration Mode:{/t}&nbsp;{fv message="demo_mode"}</label>
<input type="radio" name="demo_mode" value="on"{if $demo_mode == 'on'} checked="checked"{/if} /> {t}on{/t}&nbsp;&nbsp;
<input type="radio" name="demo_mode" value="off"{if $demo_mode != 'on'} checked="checked"{/if} /> {t}off{/t}&nbsp;&nbsp;<a href="#" class="tooltip" title="{t}toggle demonstration mode{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<span class="notes">{t}(toggle demonstration mode){/t}</span>-->
</div>

<div class="formSpacing"></div>

<div class="buttons">
<button type="submit" value="{t}Update{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="update"/>{t}Update{/t}</button>
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<br class="clear">

<div class="output">{if $output}{$output}{/if}</div>

<div class="clear"></div>
</form>

{literal}
<script type="text/javascript">
$().ready(function(){
	$('#throttleWindow').jqmAddTrigger($('#throttleTrigger'));
});
</script>
{/literal}