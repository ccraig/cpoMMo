{capture name=head} {* used to inject content into the HTML <head> *}
	{* Include in-place editing of subscriber table *}
	<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/jq/form.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/sorter.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/tableEditor/editor.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/validate.js"></script>
	<script type="text/javascript" src="{$url.theme.shared}js/table.js"></script>
	<script type="text/javascript">{literal}
		$().ready(function() {
			$('#orderForm select').change(function() {
				$('#orderForm')[0].submit();
			});
		});
		{/literal}
	</script>

	{* Styling of user table *}
	<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/table.css" />
	<link type="text/css" rel="stylesheet" href="{$url.theme.shared}js/thickbox/thickbox.css" />
	
{/capture}


{include file="inc/admin.header.tpl"} {*sidebar='off'*}


<h2>{t}poMMo User Manager{/t}</h2>

{*<div id="boxMenu">*}
	{include file="inc/messages.tpl"}


<fieldset>
<legend>{t}Sorting and Navigation{/t}</legend>
<ul class="inpage_menu">
	<li>
		<label for="info">{t}Extended Info{/t}</label>
		<select name="info">
			<option value="show"{if $state.info == 'show'} selected="selected"{/if}>{t}show{/t}</option>
			<option value="hide"{if $state.info == 'hide'} selected="selected"{/if}>{t}hide{/t}</option>
		</select>
	</li>
	<li><a href="../plugins.php" title="{t}Return to Plugins Menu{/t}">
		&raquo; {t}Return to Plugins Menu{/t}</a></li>
</ul>
</fieldset>


<fieldset>
<legend>{t}Use Cases{/t}</legend>
<ul class="inpage_menu">
	<li><a href="ajax/user_add.php?height=400&amp;width=500" title="{t}Add User{/t}" class="thickbox">
		&raquo; {t}Add User{/t}</a></li>
	<li><a href="ajax/permgroup_add.php?height=400&amp;width=500" title="{t}Add Permission Group{/t}" class="thickbox">
		&raquo; {t}Add Permission Group{/t}</a></li>
</ul>
</fieldset>


<div id="plugincontent">


	{* ---------------------- [user] MATRIX ------------------------ *}
	
		<br>
		<p class="count">({t 1=$nrusers}%1 users{/t})</p>
		<br><br>
		
		{if $nrusers > 0}
			<table summary="user details" id="users">
				<thead>
					<tr>
						<th name="ID" class="sort">ID</th>
						<th name="name" class="sort">Name</th>
						<th name="group" class="sort">Group</th>
						<th name="lastlogin" class="sort">last login</th>
						<th name="logintries" class="sort">logins</th>
						<th style="text-align: center;">active</th>
						<th style="text-align: center;">edit</th>
						<th style="text-align: center;">delete</th>
						{if $state.info == 'show'}
						<th name="pass" class="sort">Pass</th>
						<th name="created" class="sort">created</th>
						<th name="lastedited" class="sort">last edit</th>
						{/if}
					</tr>
				</thead>
				
				<tbody>
				{foreach name=aussen key=nr item=user from=$user}
					<tr style="background-color:{cycle values="#eeeeee,#d0d0d0"}">
						<td style="text-align: center;">{$user.id}</td>
						<td style="text-align: left;">{$user.name}</td>
						<td style="text-align: left;">{$user.perm}</td>
						<td style="text-align: center;">{$user.lastlogin|date_format:"%d.%m.%Y %H:%M"}</td>{*%x*}
						<td style="text-align: center;">{$user.logintries}</td>
						<td>
								{if $user.active==1}
									<input type="hidden" name="active" value="0">
									<button class="edit tsToggleEdit" onclick="window.location.href='config_main.php'">
										<img alt="deactivate plugin" src="/pommo/aardvark-development/themes/shared/images/icons/yes.png" />
									</button>
								{else}
									<input type="hidden" name="active" value="1">
									<button class="edit tsToggleEdit" onclick="window.location.href='config_main.php'">
										<img alt="activate plugin" height="28" src="/pommo/aardvark-development/themes/shared/images/icons/nok.png" />
									</button>
								{/if}
						</td>
						<td>
								<a href="ajax/user_edit.php?userid={$user.id}&amp;height=400&amp;width=500" title="{t}Edit User{/t}" class="thickbox">
								<button>
									<img alt="edit" border="0" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
								</button>
								</a>
						</td>
						<td>
								<a href="ajax/delete.php?userid={$user.id}&amp;height=400&amp;width=500" title="{t}Edit User{/t}" class="thickbox">
								<button>
									<img alt="edit" border="0" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
								</button>
								</a>
						</td>
						{if $state.info == 'show'}
						<td style="text-align: left;">{$user.pass}</td>
						<td style="text-align: center;">{$user.created|date_format:"%d.%m.%Y"}</td>{*:"%A, %B %e, %Y" or |date_format:"%m/%d/%Y"}*}
						<td style="text-align: center;">{$user.lastedit}</td>
						{/if}
						
						{*<td class="cell" style="text-align: center;"><a href="user_main.php?action=edit&userid={$user.id}">edit</a></td>		
						<td class="cell" style=" text-align: center;"><a href="user_main.php?action=delete&userid={$user.id}">delete</a></td>*}
					</tr>
				{/foreach}
			
			</table>
			
		{else} {* If NOT >0 users *}
			<p>{t}No users found.{/t}</p>
		{/if}
		


	{* ----------------- [group] MATRIX --------------------- *}
	
	<br>
	<p class="count">({t 1=$nrperm}%1 permission groups{/t})</p>
	<br><br>

	{if $nrperm > 0}
		<table summary="permission groups" id="permgroups">

			<thead>
				<tr>
					<th name="gid" class="sort">ID</th>
					<th name="gname" class="sort">Groupname</th>
					<th name="gperm" class="sort">Permissions</th>
					<th name="gdesc" class="sort">Description</th>
					<th style="text-align: center;">edit</th>
					<th style="text-align: center;">delete</th>
				</tr>
			</thead>
					
			<tbody>
			{foreach name=gr key=nr item=item from=$permgroups}
					<tr style="background-color:{cycle values="#eeeeee,#d0d0d0"}">
						<td valign="top">{$item.id}</td>
						<td valign="top">{$item.name}</td>
						<td valign="top">
							{foreach name=pe key=pkey item=pitem from=$item.perm}
								{$pitem.name|lower} <br>
							{/foreach}
						</td>
						<td valign="top">{$item.desc}</td>
						<td valign="top">
								<a href="ajax/permgroup_edit.php?groupid={$item.id}&amp;height=400&amp;width=500" title="{t}Edit Permission Group{/t}" class="thickbox">
								<button>
									<img alt="edit" border="0" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
								</button>
								</a>
						</td>
						<td valign="top">
								<a href="ajax/delete.php?groupid={$item.id}&amp;height=400&amp;width=500" title="{t}Edit User{/t}" class="thickbox">
								<button>
									<img alt="edit" border="0" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
								</button>
								</a>
						</td>
					</tr>
			{/foreach}
			</tbody>
				
		</table>
		
	{else} {* If NOT >0 permgroups *}
		<p>{t}No permission groups found.{/t}</p>
	{/if}
	
	
</div> <!--plugincontent-->
			
{include file="inc/admin.footer.tpl"}