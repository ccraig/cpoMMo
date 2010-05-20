{* Field Validation - see docs/template.txt documentation *}
{fv form='messages'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="subscribe_sub"}
{fv validate="subscribe_msg"}
{fv validate="subscribe_suc"}
{fv validate="activate_sub"}
{fv validate="activate_msg"}
{fv validate="unsubscribe_suc"}
{fv validate="notify_email"}
{fv validate="notify_subscribe"}
{fv validate="notify_unsubscribe"}
{fv validate="notify_update"}
{fv validate="notify_pending"}

<form action="{$smarty.server.PHP_SELF}" method="post">
<fieldset>
<legend>{t}notifications{/t}</legend>

{t}Administrators can be sent a notification of subscription list changes.{/t}

<div>
<label for="notify_email">{t}Notification email(s):{/t}{fv message="notify_email"}</label>
<input type="text" name="notify_email" value="{$notify_email|escape}" />
<span class="notes">{t}(Notifications will be sent to the above address(es). Multiple addresses can be entered -- seperate with a comma.){/t}</span>
</div>

<div>
<label for="notify_subject">{t}Subject Prefix:{/t}{fv message="notify_subject"}</label>
<input type="text" name="notify_subject" value="{$notify_subject|escape}" />
<span class="notes">{t}(The subject of Notification Mails will begin with this){/t}</span>
</div>

<div>
<label for="notify_subscribe">{t escape=no 1="<strong>$t_subscribe</strong>"}Notify on %1{/t}{fv message="notify_subscribe"}</label>
<input type="radio" name="notify_subscribe" value="on"{if $notify_subscribe == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="notify_subscribe" value="off"{if $notify_subscribe != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(sent upon successful subscription){/t}</span>
</div>

<div>
<label for="notify_unsubscribe">{t escape=no 1="<strong>$t_unsubscribe</strong>"}Notify on %1{/t}{fv message="notify_unsubscribe"}</label>
<input type="radio" name="notify_unsubscribe" value="on"{if $notify_unsubscribe == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="notify_unsubscribe" value="off"{if $notify_unsubscribe != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(sent upon successful unsubscription){/t}</span>
</div>

<div>
<label for="notify_update">{t escape=no 1="<strong>$t_update</strong>"}Notify on %1{/t}{fv message="notify_update"}</label>
<input type="radio" name="notify_update" value="on"{if $notify_update == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="notify_update" value="off"{if $notify_update != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(sent upon subscriber update){/t}</span>
</div>

<div>
<label for="notify_pending">{t escape=no 1="<strong>$t_pending</strong>"}Notify on %1{/t}{fv message="notify_pending"}</label>
<input type="radio" name="notify_pending" value="on"{if $notify_pending == 'on'} checked="checked"{/if} /> {t}on{/t}
<input type="radio" name="notify_pending" value="off"{if $notify_pending != 'on'} checked="checked"{/if} /> {t}off{/t}
<span class="notes">{t}(sent upon subscription attempt){/t}</span>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

<fieldset>
<legend>{t}subscribe{/t}</legend>

<div>
<label for="subscribe_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="subscribe_sub"}</label>
<input type="text" name="subscribe_sub" value="{$subscribe_sub|escape}" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<label for="subscribe_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="subscribe_msg"}</label>
<textarea name="subscribe_msg" rows="8" cols="44">{$subscribe_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div>
<label for="subscribe_suc"><strong class="required">{t}Success:{/t}</strong>{fv message="subscribe_suc"}</label>
<textarea name="subscribe_suc" rows="3" cols="44">{$subscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[subscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

<fieldset>
<legend>{t}activate Records{/t}</legend>

<div>
<label for="activate_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="activate_sub"}</label>
<input type="text" name="activate_sub" value="{$activate_sub|escape}" />
<div class="notes">{t}(Subject of Sent Email){/t}</div>
</div>

<div>
<label for="activate_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="activate_msg"}</label>
<textarea name="activate_msg" rows="8" cols="44">{$activate_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[activate]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

<fieldset>
<legend>{t}Unsubscribe{/t}</legend>

<div>
<label for="unsubscribe_suc"><strong class="required">{t}Success:{/t}</strong>{fv message="unsubscribe_suc"}</label>
<textarea name="unsubscribe_suc" rows="3" cols="44">{$unsubscribe_suc|escape}</textarea>
<div class="notes">{t}(Message displayed upon success){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[unsubscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

</fieldset>

</form>