<form class="ajax" action="{$smarty.server.PHP_SELF}" method="post">

<fieldset>
<h3>{t}SMTP Throttling{/t}</h3>

<div>
<label for="throttle_SMTP">{t}Throttle Sharing{/t}</label>
<select name="throttle_SMTP" id="throttle_SMTP" class="onChange">
<option value="individual"{if $throttle_SMTP == 'individual'} selected="selected"{/if}>{t}off{/t}</option>
<option value="shared"{if $throttle_SMTP == 'shared'}  selected="selected"{/if}>{t}on{/t}</option>
</select>
<div class="notes">{t}ON: the throttler is global{/t}<br />
{t}OFF: independent throttler per relay{/t}</div>
</div>

<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
<!--<div class="output alert">{if $output}{$output}{/if}</div>-->
</fieldset>

{foreach from=$smtpStatus key=id item=status}
<br class="clear">

<fieldset>
<h3>{t 1=$id}SMTP #%1{/t}</h3>

<div class="formSpacing">
<label>{t}SMTP Status:{/t}</label>
{if $status}
<img src="{$url.theme.shared}images/icons/ok.png" alt="ok icon" />{t}Connected to SMTP server{/t}
{else}
<img src="{$url.theme.shared}images/icons/nok.png" alt="not ok icon" />{t}Unable to connect to SMTP server{/t}
{/if}
</div>

<div class="formSpacing">
<label for="host[{$id}]">{t}SMTP Host:{/t}&nbsp;</label>
<input type="text" name="host[{$id}]" value="{$smtp[$id].host|escape}" size="30" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}IP address or name of SMTP server{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<div class="notes">{t}(IP Address or Name of SMTP server){/t}</div>-->
</div>

<div class="formSpacing">
<label for="port[{$id}]">{t}Port Number:{/t}&nbsp;</label>
<input type="text" name="port[{$id}]" value="{$smtp[$id].port|escape}" size="10" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}port # of SMTP server [usually 25]{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<div class="notes">{t}(Port # of SMTP server [usually 25]){/t}</div>-->
</div>

<div class="formSpacing">
<label for="auth[{$id}]">{t}SMTP Authentication:{/t}&nbsp;</label>
<input type="radio" name="auth[{$id}]" value="on"{if $smtp[$id].auth == 'on'} checked="checked"{/if} /> on&nbsp;&nbsp;
<input type="radio" name="auth[{$id}]" value="off"{if $smtp[$id].auth != 'on'} checked="checked"{/if} /> off&nbsp;&nbsp;<a href="#" class="tooltip" title="{t}toggle SMTP authentication [usually off]{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<div class="notes">{t}(Toggle SMTP Authentication [usually off]){/t}</div>-->
</div>

<div class="formSpacing">
<label for="user[{$id}]">{t}SMTP Username:{/t}&nbsp;</label>
<input type="text" name="user[{$id}]" value="{$smtp[$id].user|escape}" size="27" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}optional{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<div class="notes">{t}(optional){/t}</div>-->
</div>

<div class="formSpacing">
<label for="pass[{$id}]">{t}SMTP Password:{/t}&nbsp;</label>
<input type="text" name="pass[{$id}]" value="{$smtp[$id].pass|escape}" size="27" maxlength="60" />&nbsp;<a href="#" class="tooltip" title="{t}optional{/t}"><img src="{$url.theme.shared}/images/icons/help-small.png" alt="tip" /></a>
<!--<div class="notes">{t}(optional){/t}</div>-->
</div>

<div class="formSpacing"></div>

<div class="buttons">
<button type="submit" name="updateSmtpServer[{$id}]" id="updateSmtpServer{$id}" value="{t 1=$id}Update Relay #%1{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/tick.png" alt="update relay"/>{t 1=$id}Update Relay #%1{/t}</button>
{if $id == 1}
 - {t}This is your default relay{/t}
{else}
<button type="submit" name="deleteSmtpServer[{$id}]" id="deleteSmtpServer{$id}" value="{t 1=$id}Remove Relay #%1{/t}" class="negative"><img src="{$url.theme.shared}/images/icons/cross.png" alt="delete relay"/>{t 1=$id}Remove Relay #%1{/t}</button>
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
{/if}
</div>

<br class="clear">

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>
{/foreach}

{if $addServer}
<div class="buttons">
<button type="submit" name="addSmtpServer[{$addServer}]" id="addSmtpServer{$addServer}" value="{t}Add Another Relay{/t}" class="positive"><img src="{$url.theme.shared}/images/icons/add-small.png" alt="add relay"/>{t}Add Another Relay{/t}</button>
</div>
{/if}
</form>

<br class="clear">