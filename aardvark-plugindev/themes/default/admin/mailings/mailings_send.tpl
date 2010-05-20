{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />
{/capture}{include file="inc/admin.header.tpl"}

{include file="inc/messages.tpl"}

<form method="post" action="">

<fieldset>
<legend>{t}Mailing Parameters{/t}</legend>

<p>{t escape=no 1="<strong class=\"required\">" 2="</strong>"}Fields in %1bold%2 are required{/t}</p>

<div>
<label for="subject"><span class="required">{t}Subject:{/t}</span> <span class="error">{validate id="subject" message=$formError.subject}</span></label>
<input type="text" size="60" maxlength="60" name="subject" value="{$subject|escape}" id="subject" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="ishtml"><span class="required">{t}Mail Format:{/t}</span> <span class="error">{validate id="ishtml" message=$formError.ishtml}</span></label>
<select name="ishtml" id="ishtml">
<option value="on"{if $ishtml == 'on'} selected="selected"{/if}>{t}HTML Mailing{/t}</option>
<option value="off"{if $ishtml == 'off'} selected="selected"{/if}>{t}Plain Text Mailing{/t}</option>
</select>
<span class="notes">{t}(Select the format of this mailing){/t}</span>
</div>

<div>
<label for="mailgroup"><span class="required">{t}Send Mail To:{/t}</span> <span class="error">{validate id="mailgroup" message=$formError.mailgroup}</span></label>
<select name="mailgroup" id="mailgroup">
<option value="all"{if $mailgroup == 'all'} selected="selected"{/if}>{t}All subscribers{/t}</option>
{foreach from=$groups item=group key=key}
<option value="{$key}"{if $mailgroup == $key} selected="selected"{/if}>{$group.name}</option>
{/foreach}
</select>
<span class="notes">{t}(Select who should receive the mailing){/t}</span>
</div>

<div>
<label for="fromname"><span class="required">{t}From Name:{/t}</span> <span class="error">{validate id="fromname" message=$formError.fromname}</span></label>
<input type="text" size="60" maxlength="60" name="fromname" value="{$fromname|escape}" id="fromname" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="fromemail"><span class="required">{t}From Email:{/t}</span> <span class="error">{validate id="fromemail" message=$formError.fromemail}</span></label>
<input type="text" size="60" maxlength="60" name="fromemail" value="{$fromemail|escape}" id="fromemail" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div>
<label for="frombounce"><span class="required">{t}Return:{/t}</span> <span class="error">{validate id="frombounce" message=$formError.frombounce}</span></label>
<input type="text" size="60" maxlength="60" name="frombounce" value="{$frombounce|escape}" id="frombounce" />
<span class="notes">{t}(maximum of 60 characters){/t}</span>
</div>

<div class="advanced">
<label for="list_charset"><span class="required">{t}Character Set:{/t}</span> <span class="error">{validate id="list_charset" message=$formError.list_charset}</span></label>
<select name="list_charset" id="list_charset">
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
</div>

</fieldset>

<div class="buttons">

<input type="submit" id="submit" name="submit" value="{t}Continue{/t}" />

</div>

</form>

{include file="inc/admin.footer.tpl"}