{capture name=head}{* used to inject content into the HTML <head> *}
{* Include in-place editing of subscriber table *}
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/table.js"></script>

{literal}
<script type="text/javascript">
$().ready(function() {
	$('#orderForm select').change(function() {
		$('#orderForm')[0].submit();
	});
});
</script>
{/literal}

{* Styling of table *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/table.css" />
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
{/capture}
{include file="inc/admin.header.tpl" sidebar='off'}

<ul class="inpage_menu">
<li><a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a></li>
</ul>

<h2>{t}Mailings History{/t}</h2>

{include file="inc/messages.tpl"}

<form method="post" action="" id="orderForm">

<input type="hidden" name="resetPager" value="true" />

<fieldset>
<legend>{t}Sorting{/t}</legend>

<ul class="inpage_menu">

<li>
<label for="sort">{t}Sort by{/t}</label>
<select name="sort">
<option value="subject"{if $state.sort == 'subject'} selected="selected"{/if}>{t}Subject{/t}</option>
<option value="mailgroup"{if $state.sort == 'mailgroup'} selected="selected"{/if}>{t}Group{/t}</option>
<option value="subscriberCount"{if $state.sort == 'subscriberCount'} selected="selected"{/if}>{t}Subscriber Count{/t}</option>
<option value="started"{if $state.sort == 'started'} selected="selected"{/if}>{t}Time Created{/t}</option>
</select>
</li>

<li>
<label for="order">{t}Order by{/t}</label>
<select name="order">
<option value="asc"{if $state.order == 'asc'} selected="selected"{/if}>{t}ascending{/t}</option>
<option value="desc"{if $state.order == 'desc'} selected="selected"{/if}>{t}descending{/t}</option>
</select>
</li>

<li>
<label for="limit">{t}# per page{/t}</label>
<select name="limit">
<option value="10"{if $state.limit == '10'} selected="selected"{/if}>10</option>
<option value="50"{if $state.limit == '50'} selected="selected"{/if}>50</option>
<option value="150"{if $state.limit == '150'} selected="selected"{/if}>150</option>
<option value="300"{if $state.limit == '300'} selected="selected"{/if}>300</option>
<option value="500"{if $state.limit == '500'} selected="selected"{/if}>500</option>
</select>
</li>

</ul>

</fieldset>
</form>

<form method="post" action="mailings_mod.php" name="oForm" id="oForm">
<fieldset>
<legend>{t}Mailings{/t}</legend>

<p class="count">({t 1=$tally}%1 mailings{/t})</p>

{if $tally > 0}
<table summary="mailing details">
<thead>
<tr>
<th name="key"></th>
<th>{t}Subject{/t}</th>
<th>{t}Group (count){/t}</th>
<th>{t}Sent{/t}</th>
<th>{t}Started{/t}</th>
<th>{t}Finished{/t}</th>
<th>{t}Status{/t}</th>
</tr>
</thead>

<tbody>

{foreach from=$mailings key=id item=o}
<tr>
<td>
<p class="hidden">{$id}</p>
DELETE  <a href="ajax/mailing_preview.php?mail_id={$id}&amp;height=320&amp;width=480" title="{t}Message Preview{/t}" class="thickbox">{t}View{/t}</a>
<a href="ajax/mailing_reload.php?mail_id={$id}" title="{t}Reload Mailing{/t}">{t}Reload{/t}</a>
</td>

<td>{$o.subject}</td>
<td>{$o.group} ({$o.tally})</td>
<td>{$o.sent}</td>
<td>{$o.start}</td>
<td>{$o.end} <div>({$o.mph} {t}Mails/Hour{/t})</div></td>
<td>
{if $o.status == 0}
	{t}Complete{/t}
{elseif $o.status == 1}
	{t}Processing{/t}
{else}
	{t}Cancelled{/t}
{/if}
<div>(<a href="ajax/history_notices.php?mail_id={$id}&amp;height=320&amp;width=480" title="{t}View Last Notices{/t}" class="thickbox">{t}View Last Notices{/t}</a>)
</td>
</tr>
{/foreach}

</tbody>
</table>

{literal}
<script type="text/javascript">
$().ready(function() {	
	$("table").tableSorter({
		sortClassAsc: 'sortUp', 		// class name for ascending sorting action to header
		sortClassDesc: 'sortDown',	// class name for descending sorting action to header
		headerClass: 'sort', 				// class name for headers (th's)
		disableHeader: 0					// DISABLE Sorting on edit/delete column
	});

	$('table tbody tr').quicksearch({
		attached: "table",
		position: "before",
		labelClass: "quicksearch",
		stripeRowClass: ['r1', 'r2', 'r3'],
		labelText: "{/literal}{t}Quick Search{/t}{literal}",
		inputText: "{/literal}{t}search table{/t}{literal}",
		loaderImg: '{/literal}{$url.theme.shared}images/loader.gif{literal}'
	});	
});
</script>
{/literal}
{/if}

</fieldset>
</form>

{* Include Pagination *}
{include file="inc/pager.tpl"}

{include file="inc/admin.footer.tpl"}