{* Field Validation - see docs/template.txt documentation *}
{* {fv form='general'} *}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="subject"}
{fv validate="mailgroup"}
{fv validate="fromname"}
{fv validate="fromemail"}
{fv validate="frombounce"}

<form class="json mandatory" action="{$smarty.server.PHP_SELF}" method="post">

<div class="output alert">{include file="inc/messages.tpl"}</div>

<p>{t escape=no 1="<span class=\"required\">" 2="</span>"}Fields marked like %1 this %2 are required.{/t}</p>

<div>
<label for="subject"><span class="required">{t}Subject:{/t}</span>{fv message="subject"}</label>
<input type="text" name="subject" value="{$subject|escape}" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="mailgroup"><span class="required">{t}Send Mail To:{/t}</span>{fv message="mailgroup"}</label>
<select name="mailgroup">
<option value="all"{if $mailgroup == 'all'} selected="selected"{/if}>{t}All subscribers{/t}</option>
{foreach from=$groups item=group key=key}
<option value="{$key}"{if $mailgroup == $key} selected="selected"{/if}>{$group.name}</option>
{/foreach}
</select>
<span class="notes">{t}(Select who should receive the mailing){/t}</span>
</div>

<div>
<label for="fromname"><span class="required">{t}From Name:{/t}</span>{fv message="fromname"}</label>
<input type="text" name="fromname" value="{$fromname|escape}" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="fromemail"><span class="required">{t}From Email:{/t}</span>{fv message="fromemail"}</label>
<input type="text" name="fromemail" value="{$fromemail|escape}" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="frombounce"><span class="required">{t}Return:{/t}</span>{fv message="frombounce"}</label>
<input type="text" name="frombounce" value="{$frombounce|escape}" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

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
<span class="notes">{t}(Select Character Set of Mailings){/t}</span>

<div class="buttons">

<input type="submit" id="submit" name="submit" value="{t}Continue{/t}" />
<img src="{$url.theme.shared}images/loader.gif" name="loading" class="hidden" title="{t}loading...{/t}" alt="{t}loading...{/t}" />

</div>

</form>