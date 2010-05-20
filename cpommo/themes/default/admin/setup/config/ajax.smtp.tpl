<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<legend>{t}SMTP Throttling{/t}</legend>

<div>
<label for="throttle_SMTP">{t}Throttle Sharing{/t}</label>
<select name="throttle_SMTP" id="throttle_SMTP" class="onChange">
<option value="individual"{if $throttle_SMTP == 'individual'} selected="selected"{/if}>{t}off{/t}</option>
<option value="shared"{if $throttle_SMTP == 'shared'}  selected="selected"{/if}>{t}on{/t}</option>
</select>
<div class="notes">{t}(ON; the throttler will be global. OFF; independent throttler per relay.){/t}</div>
</div>

<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<div class="output alert">{if $output}{$output}{/if}</div>
</fieldset>

{foreach from=$smtpStatus key=id item=status}
<fieldset>
<legend>{t 1=$id}SMTP #%1{/t}</legend>

<div>
<label>{t}SMTP Status:{/t}</label>
{if $status}
<img src="{$url.theme.shared}images/icons/ok.png" alt="ok icon" />{t}Connected to SMTP server{/t}
{else}
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" />{t}Unable to connect to SMTP server{/t}
{/if}
</div>

<div>
<label for="host[{$id}]">{t}SMTP Host:{/t}</label>
<input type="text" name="host[{$id}]" value="{$smtp[$id].host|escape}" />
<div class="notes">{t}(IP Address or Name of SMTP server){/t}</div>
</div>

<div>
<label for="port[{$id}]">{t}Port Number:{/t}</label>
<input type="text" name="port[{$id}]" value="{$smtp[$id].port|escape}" />
<div class="notes">{t}(Port # of SMTP server [usually 25]){/t}</div>
</div>

<div>
<label for="auth[{$id}]">{t}SMTP Authentication:{/t}</label>
<input type="radio" name="auth[{$id}]" value="on"{if $smtp[$id].auth == 'on'} checked="checked"{/if} /> on
<input type="radio" name="auth[{$id}]" value="off"{if $smtp[$id].auth != 'on'} checked="checked"{/if} /> off
<div class="notes">{t}(Toggle SMTP Authentication [usually off]){/t}</div>
</div>

<div>
<label for="user[{$id}]">{t}SMTP Username:{/t}</label>
<input type="text" name="user[{$id}]" value="{$smtp[$id].user|escape}" />
<div class="notes">{t}(optional){/t}</div>
</div>

<div>
<label for="pass[{$id}]">{t}SMTP Password:{/t}</label>
<input type="text" name="pass[{$id}]" value="{$smtp[$id].pass|escape}" />
<div class="notes">{t}(optional){/t}</div>
</div>

<div class="buttons">
<input type="submit" name="updateSmtpServer[{$id}]" id="updateSmtpServer{$id}" value="{t 1=$id}Update Relay #%1{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
{if $id == 1}
 - {t}This is your default relay{/t}
{else}
<input type="submit" name="deleteSmtpServer[{$id}]" id="deleteSmtpServer{$id}" value="{t 1=$id}Remove Relay #%1{/t}" />
{/if}
<div class="output alert">{if $output}{$output}{/if}</div>
</div>

</fieldset>
{/foreach}

{if $addServer}
<input type="submit" name="addSmtpServer[{$addServer}]" id="addSmtpServer{$addServer}" value="{t}Add Another Relay{/t}" />
{/if}
</form>