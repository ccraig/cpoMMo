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

<h2>{t}Mailing List Management Management{/t}</h2>

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
	<li><a href="../plugins.php" title="{t}Return to Plugin Menu{/t}">
		&raquo; {t}Return to Plugin Menu{/t}</a></li>
</ul>
</fieldset>


<fieldset>
<legend>{t}Use Cases{/t}</legend>
<ul class="inpage_menu">
	<li><a href="ajax/list_add.php?height=400&amp;width=500" title="{t}Add a new Mailing List{/t}" class="thickbox">
		&raquo; {t}Add a new Mailing List{/t}</a></li>
	<li><a href="ajax/list_disable.php?height=400&amp;width=500" title="{t}Add Permission Group{/t}" class="thickbox">
		&raquo; {t}Deactivate all mailing lists{/t}</a></li>
</ul>
</fieldset>


<div id="plugincontent">

		<br>
		<p class="count">({t 1=$nrlists}%1 lists{/t})</p>
		<br><br>

		{if ($nrlists <= 0) } 
			<p style="align:center;"><i>No Mailing List found.</i></p>
		{else}
		
			<table summary="user details" id="users">
			{*<table border="0" border="0" cellspacing="1" cellpadding="3" width="100%"
			style="font-size:12px;">*}{*Why is the font always so big in certain forms?*}
			
			{foreach key=key item=item from=$list}
			
				{if $item.uid=="" OR $item.uname==""}

					{if $titel == NULL}
						{assign var="titel" value=FALSE}

						<tr><td colspan="5" style="height:10px; border-color: white; "></td></tr>
						<tr style="background-color: lightgrey;"><td colspan="5">
							<div>Noone administrates this mailing lists:</div>
						</td></tr>
					{/if}
					<tr  style="background-color: lightgrey;">
						<td>
						Mailing List: <b>{$item.name}</b> (id: {$item.lid})
						</td>
						<td colspan="3" align="right" width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="edit">
										<button onclick="window.location.href='list_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="delete">
										<button onclick="window.location.href='list_main.php'">
											<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
										</button>
								</form>
						</td>
					</tr>
					
				{else}

					{if $actusr!=$item.uname}
						{assign var="actusr" value=$item.uname}
						
						<tr><td colspan="5" style="height:10px; border-color: white; "></td></tr>
						<tr style="background-color:#D2D2D2;">
							<td colspan="5">
								<b>{$item.uid}: {$item.uname}</b>
							</td>
						</tr>
					
					{/if}
					
					<tr style="background-color:#EFEFEF;">
						<td>
							{$item.name} (id: {$item.lid})
						</td>
						<td><a onclick="javascript:document.getElementById('klappe{$item.lid}').style.display='block';">&raquo; show info</a><!--hidden, visible-->
						</td>
						<td width="30">
								{if $plugitem.pactive==1}
									<input type="hidden" name="active" value="0">
									<button class="edit tsToggleEdit" onclick="window.location.href='list_main.php'">
										<img alt="deactivate plugin" src="/pommo/aardvark-development/themes/shared/images/icons/yes.png" />
									</button>
								{else}
									<input type="hidden" name="active" value="1">
									<button class="edit tsToggleEdit" onclick="window.location.href='list_main.php'">
										<img alt="activate plugin" height="28" src="/pommo/aardvark-development/themes/shared/images/icons/nok.png" />
									</button>
								{/if}
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="edit">
										<button onclick="window.location.href='list_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
						</td>
						<td width="30">
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="listid" value="{$item.lid}">
										<input type="hidden" name="action" value="delete">
										<button onclick="window.location.href='list_main.php'">
											<img alt="delete" src="/pommo/aardvark-development/themes/shared/images/icons/delete.png"/>
										</button>
								</form>
						</td>
					</tr>
					<!-- ausklappen AJAX-->
					<tr style="padding:0px; margin:0px;">
						<td style="padding:0px 0px 0px 0px; margin:0px 0px 0px 0px;" colspan="5">
							<div id="klappe{$item.lid}" style="display:none; padding:0px; margin:0px;">
								<table cellspacing="0" cellpadding="3" width="100%" style="font-size:12px; border: 1px solid #CCCCCC; background-color: #EFEFEF; margin-left: 25px; ">
								<tr>
									<td>Description: </td><td>{$item.desc}</td>
								</tr><tr>
									<td>List Created: </td><td>{$item.created}</td>
								</tr><tr>
									<td>Mailings Sent: </td><td>{$item.setmailings}</td>
								</tr><tr>
									<td>Sender Information: </td><td>{$item.senderinfo}</td>
								</tr><tr>
									<td># Receiver: </td><td># {$item.receiver} <a>&raquo; view receiver list</a></td>
								</tr>
								</tr><tr>
									<td>Groups: </td><td> gruppennamen <a>&raquo; edit group list</a> delete und neue groupen adden AJAX</td>
								</tr>
								<tr>
									<td colspan="2" align="right">
										<a onclick="javascript:document.getElementById('klappe{$item.lid}').style.display='none';">&raquo; close</a>
									</td>
								</tr>
								</table><br>
							</div>
						</td>
					</tr>
					
				{/if}
			
			{/foreach}
		
			</table>
			
		{/if} {* some mailinglists found *}


		<br>
		<form method="POST" action="" name="alloff">
			<input type="hidden" name="setallmailinglistsoff" value="TRUE">
			<a href="#" onClick="document.alloff.submit()">&raquo Deactivate all mailing lists</a><br>
		</form>
		<br><br>



{* USE CASES *}
{if $showDelete OR $showEdit OR $showAdd}
<div id="pluginaction" style="float: top; border: 1px solid red; background-color:pink; padding:20px;">
	->ajax
	{if $showDelete AND $listdata}
	
		<h3> Delete a mailing list </h3>
		Do you really want to delete this mailing list?
		List ID: {$listdata.list_id}<br>
		Name: {$listdata.list_name}<br>
		Description: {$listdata.list_desc}<br>
		Recipients: #<br>
		Manager: {$listdata.user_id}<br><br>
		<form action="">
			<input type="submit" name="deleteList" value="ReallyDelete">
			<button onclick="window.location.href='list_main.php'">
				No i don't.
			</button>
		</form>
	{/if}

	{if $showEdit}
	
		{*Mailgroups und listdata*}
		<h3> Edit a mailing list </h3>
		<form action="">
			ID: <input type="text" name="listid" value="{$listdata.lid}"><br>
			Name: <input type="text" name="listname" value="{$listdata.lname}"><br>
			Description: <input type="text" name="listdesc" value="{$listdata.ldesc}"><br>
			Sender info: <input type="text" name="senderemail" value="{$listdata.lsenderinfo}"><br>		
			User: drop down responsible <input type="text" name="userarray" value="{*$listdata.*}"><button>+</button>	<br>
			Group: drop down <input type="text" name="grouparray" value="$mailgroups"><button>+</button>	<br>	
			{$mailgroups}<br>
			<input type="submit" name="editList" value="Edit"><input type="reset" name="reset" value="Reset">
		</form>
	{/if}

	{if $showAdd}
		<h3> Add a Mailing List </h3>
		<form action="">
			Name: <input type="text" name="listname" value=""><br>
			Description: <input type="text" name="listdesc" value=""><br>
			Sender info: <input type="text" name="senderemail" value=""><br>		
			User: drop down responsible <input type="text" name="userarray" value="testemail@blah.com"><button>+</button>	<br>
			Group: drop down <input type="text" name="grouparray" value="3"><button>+</button>	<br>	
		
			<input type="submit" name="addList" value="Add"><input type="reset" name="reset" value="Reset">
		</form>
	{/if}
</div>
{/if}
{*END USE CASES*}





	</div> <!-- plugincontent -->

{include file="inc/admin.footer.tpl"}