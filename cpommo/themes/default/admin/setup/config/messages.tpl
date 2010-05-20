{* Field Validation - see docs/template.txt documentation *}
{fv form='messages'}
{fv prepend='<span class="error">' append='</span>'}
{fv validate="subscribe_sub"}
{fv validate="subscribe_msg"}
{fv validate="subscribe_web"}
{fv validate="unsubscribe_sub"}
{fv validate="unsubscribe_msg"}
{fv validate="unsubscribe_web"}
{fv validate="confirm_sub"}
{fv validate="confirm_msg"}
{fv validate="activate_sub"}
{fv validate="activate_msg"}
{fv validate="update_sub"}
{fv validate="update_msg"}
{fv validate="notify_email"}
{fv validate="notify_subscribe"}
{fv validate="notify_unsubscribe"}
{fv validate="notify_update"}
{fv validate="notify_pending"}


<form action="{$smarty.server.PHP_SELF}" method="post" class="json">
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
<legend>{t}Messages{/t}</legend>

{t}Customize the messages sent to your users when they subscribe, unsubscribe, attempt to subscribe, or request to update their records.{/t}


<h2>{t}Subscription{/t}</h2>

<h3>{t}Email{/t}</h3>
<input type="checkbox" name="subscribe_email" {if $subscribe_email}checked {/if}/>{t}(Check to Enable){/t}
<div>
<label for="subscribe_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="subscribe_sub"}</label>
<input type="text" name="subscribe_sub" value="{$subscribe_sub|escape}" />
</div>

<div>
<label for="subscribe_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="subscribe_msg"}</label>
<textarea name="subscribe_msg" cols="70" rows="10">{$subscribe_msg|escape}</textarea>
</div>

<h3>{t}Website{/t}</h3>
<div>
<label for="subscribe_web"><strong class="required">{t}Message:{/t}</strong>{fv message="subscribe_web"}</label>
<textarea name="subscribe_web" cols="70" rows="5">{$subscribe_web|escape}</textarea>
<div class="notes">{t escape='no'}(displayed on webpage){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[subscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

<hr />
<h2>{t}Unsubscription{/t}</h2>

<h3>{t}Email{/t}</h3>
<input type="checkbox" name="unsubscribe_email" {if $unsubscribe_email}checked {/if}/>{t}(Check to Enable){/t}
<div>
<label for="unsubscribe_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="unsubscribe_sub"}</label>
<input type="text" name="unsubscribe_sub" value="{$unsubscribe_sub|escape}" />
</div>

<div>
<label for="unsubscribe_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="unsubscribe_msg"}</label>
<textarea name="unsubscribe_msg" cols="70" rows="10">{$unsubscribe_msg|escape}</textarea>
</div>

<h3>{t}Website{/t}</h3>
<div>
<label for="unsubscribe_web"><strong class="required">{t}Message:{/t}</strong>{fv message="unsubscribe_web"}</label>
<textarea name="unsubscribe_web" cols="70" rows="5">{$unsubscribe_web|escape}</textarea>
<div class="notes">{t escape='no'}(displayed on webpage){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[unsubscribe]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

<hr />
<h2>{t}Subscription Confirmation{/t}</h2>

<h3>{t}Email{/t}</h3>
<div>
<label for="confirm_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="confirm_sub"}</label>
<input type="text" name="confirm_sub" value="{$confirm_sub|escape}" />
</div>

<div>
<label for="confirm_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="confirm_msg"}</label>
<textarea name="confirm_msg" cols="70" rows="10">{$confirm_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[confirm]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

<hr />
<h2>{t}Account Access{/t}</h2>

<h3>{t}Email{/t}</h3>
<div>
<label for="activate_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="activate_sub"}</label>
<input type="text" name="activate_sub" value="{$activate_sub|escape}" />
</div>

<div>
<label for="activate_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="activate_msg"}</label>
<textarea name="activate_msg" cols="70" rows="10">{$activate_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[activate]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output alert">{if $output}{$output}{/if}</div>

<hr />
<h2>{t}Update Validation{/t}</h2>

<h3>{t}Email{/t}</h3>
<div>
<label for="update_sub"><strong class="required">{t}Subject:{/t}</strong>{fv message="update_sub"}</label>
<input type="text" name="update_sub" value="{$update_sub|escape}" />
</div>

<div>
<label for="update_msg"><strong class="required">{t}Message:{/t}</strong>{fv message="update_msg"}</label>
<textarea name="update_msg" cols="70" rows="10">{$update_msg|escape}</textarea>
<div class="notes">{t escape='no' 1='<tt>' 2='</tt>'}(Use %1[[url]]%2 for the confirm link at least once){/t}</div>
</div>

<div class="buttons">
<input type="submit" value="{t}Update{/t}" />
<input type="submit" name="restore[update]" value="{t}Restore to Defaults{/t}" />
<img src="{$url.theme.shared}images/loader.gif" alt="loading..." class="hidden" name="loading" />
</div>

<div class="output">{if $output}{$output}{/if}</div>

</fieldset>

</form>