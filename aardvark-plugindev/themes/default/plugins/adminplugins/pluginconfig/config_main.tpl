{include file="inc/admin.header.tpl"}

<h2>{t}Plugin Configuration{/t}</h2>

<!--<div id="boxMenu">-->

	<div>
		<a class="pommoClose" href="../plugins.php" style="float: right; line-height:18px; " >
		<img src="{$url.theme.shared}/images/icons/left.png" width="21" height="21" align="absmiddle" border="0">&nbsp;
		{t}Return to Plugin Menu{/t}
		</a>
	</div>
	<!--<div style="text-align: right; clear: both;width: 1px;"></div>-->
	<br>

<!--</div>--> <!--boxmenu-->

	{include file="inc/messages.tpl"}
	
	<div id="plugincontent">

		<!--CONTENT-->

		{if $nrplugins < 1}
			
			<p>
			<div style="text-align:center;">{t}No Plugin activated. Select a category below to activate its plugins.{/t}</div>
			</p>
			
		{else}
		
			<h3>{t}Active Plugins{/t}</h3>
			<div style="text-align:left;"><i>({t}Activated parameters:{/t} {$nrplugins})</i></div>
			
			<table border="0" border="0" cellspacing="1" cellpadding="4" width="100%">
			
				{foreach key=key item=plugitem from=$plugins}
			
					{if $mom!=$plugitem.category}
						{assign var="mom" value=$plugitem.category}
						<tr><td colspan="3" style="height:10px; border-color: white; "></td></tr>
						<tr style="background-color:#D2D2D2;">
							<td colspan="3">
								<div style="float:left;"><b>{$mom}</b></div>
								<form name="catoff{$plugitem.cid}" method="POST" style="padding:0px; margin:0px; float:right; text-align:right; width:50%;">
									<input type="hidden" name="switchcid" value="{$plugitem.cid}">
									<input type="hidden" name="active" value="0">
									<a href="#" onClick="document.catoff{$plugitem.cid}.submit()">&raquo; deactivate category</a><br>
									{*<button onclick="window.location.href='config_main.php'" style="font-size:10px;">&raquo; deactivate category</button>*}
								</form>
							</td>

						</tr>
					{/if}

					{if $plugitem.pid == NULL}
						<tr><td style="text-align:center;" colspan="3">{t}No plugins detected in this category{/t}</td></tr>
					{else}

						<tr style="text-align:center; background-color:#EFEFEF;">
							<td style="text-align: left; ">
								<div class="pluginheader">
									<div style="float:left;"><b>{$plugitem.uniquename}</b> - <i>{$plugitem.name}</i></div>
									<div style="float:right;">[id:{$plugitem.pid}] [ver:{$plugitem.version}]</div><br>
								</div>
								<div class="plugindetails">{$plugitem.desc}</div>
							</td>
							<td>
							
								<form action="" method="POST" style="padding:0px;margin:0px;">
									<input type="hidden" name="switchplugin" value="TRUE">
									<input type="hidden" name="switchid" value="{$plugitem.pid}">

								{if $plugitem.pactive==1}
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
								</form>
									
							</td>
							<td>
								<form action="" method="POST" style="padding:0px;margin:0px;">
										<input type="hidden" name="setupid" value="{$plugitem.pid}">
										<input type="hidden" name="viewsetup" value="TRUE">
										<button onclick="window.location.href='config_main.php'">
											<img alt="edit" src="/pommo/aardvark-development/themes/shared/images/icons/edit.png"/>
										</button>
								</form>
							</td>
	
						</tr>
						
						{if ($viewsetup AND $plugitem.pid==$setupid)}
						
							{if $plugsetup == NULL}
								<tr><td colspan="3" style="text-align:center;">
								<div class="pluginsetup" style="padding:3px; float: right; border: 1px solid silver; width: 600px;
													background-color:#eeeeee; margin-bottom:10px; ">
									<i>{t}No setup parameter for this plugin{/t}</i>
								</div></td></tr>
							{else}
											
								<tr>
									<td colspan="3" style="">
										<div class="pluginsetup" style="padding:3px; float: right; border: 1px solid silver; width: 80%;
													background-color:#eeeeee; margin-bottom:10px;">
											<div style="height:20px;">
											<div style="float:right;"><a href="config_main.php">&raquo close </a></div>
											<div style="float:left;"><b>{t}Current Setup:{/t}</b></div>
											</div>
											<div style="text-align: right; clear: both;width: 1px;"></div>

												<form action="" method="POST" style="padding:0px; margin:0px;">
														<input type="hidden" name="changeid" value="{$plugitem.pid}">
																		{foreach name=data key=key item=item from=$plugsetup}
																			{if $item.data_type == 'TXT' OR $item.data_type == 'NUM' OR $item.data_type == 'BOOL'}
																				<input type="hidden" name="old[]" value="{$item.data_value}">
																				<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
																				<input style="margin: 0px; width: 20em; display: inline;" name="plugindata[{$item.data_id}]" value="{$item.data_value}">
																					<div class="notes" style="margin: 0px 0px 0px 150px; padding: 0px; line-height:14px;"><i>{$item.data_desc}</i></div>
																					<div class="notes" style="margin: 0px 0px 4px 150px; padding: 0px; line-height:12px;">[id: {$item.data_id}&nbsp;/{$item.data_type}]</div>
																			{*{elseif $item.data_type == 'ENUM'}
																					{if  $dropdown}
																						<label for="" style="min-width: 150px; float: left; width: 6em;">{$item.data_name}</label>
																							<select size="Höhe" name="plugindata[{$item.data_id}]">
																							{foreach name=opt key=ke item=it from=$dropdown}
																							<option>{$it.plugin_uniquename}</option>
																							{/foreach}
																						</select>
																					{/if}*}
																			{/if}
																		{/foreach}
														<input type="submit" name="changesetup" value="Change">
												</form>
										</div>
									</td>
								</tr>			
							{/if}	
						{/if}
					{/if}
				{/foreach}
				
				{*<tr>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr>*}
			</table>
		{/if} {*if nrplugins > 1*}
		{*<br>&raquo <a href="">Deactivate all categories</a><br>*}
			<form method="POST" action="" name="alloff">
					<input type="hidden" name="setallpluginsoff" value="TRUE">
				<a href="#" onClick="document.alloff.submit()">&raquo Deactivate all plugins</a><br>
			</form>
		<br>
		<br>


		
			<!-- CATEGORY TABLE -->
			
			<h3>{t}Inactive Categories{/t}</h3>
			
			{if $inactive == NULL }
				<i>{t}No inactive Category{/t}{*All categories activated*}</i>
			{else}
					<i>({t}Click the activate button to enable the use of a plugin category ans its various subplugins:{/t})</i>
					<table border="0" cellspacing="0" cellpadding="4" width="100%">
				
							{foreach key= key item=item from=$inactive}
								<tr style="background-color:#D2D2D2;">
									<td>
										<div style="float:left;"><b>{$item.name}</b></div>{*{$item.cid}*}
										<form name="caton{$item.cid}" action="" method="POST" style="padding:0px; margin:0px; float:right; text-align:right; width:50%;">
											<input type="hidden" name="switchcid" value="{$item.cid}">
											<input type="hidden" name="active" value="1">
											<a href="#" onClick="document.caton{$item.cid}.submit()">&raquo; activate category</a><br>
										</form>
									</td>
								</tr>
								<tr style="background-color:#D2D2D2;">
									<td><div>{$item.desc}</div></td>
								</tr>
								<tr>
									<td style="background-color:#FFFFFF; height:1px; padding: 0px; margin:0px;"></td>
								</tr>
							</tr>
							{/foreach}			
					</table>
			{/if}
		<br><br>

	</div> <!-- plugincontent -->

<!--</div>--> <!--boxmenu closed at the top-->
{include file="inc/admin.footer.tpl"}
