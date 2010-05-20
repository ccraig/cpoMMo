{include file="admin/inc.header.tpl"}

</div>
<!-- begin content -->

	<h1>{t}Mailings History{/t}</h1>

		{* Display a eventual error message *}
		{if $messages}
			<div class="msgdisplay">
				{foreach from=$messages item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}
		{if $errors}
			<br>
			<div class="errdisplay">
				{foreach from=$errors item=msg}
					<div>* {$msg}</div>
				{/foreach}
			</div>
		{/if}

	
		<div style="width:100%;">
			<span style="float: right; margin-right: 30px;">
				<a href="admin_mailings.php">{t 1=$returnStr}Return to %1{/t}</a>
			</span>
		</div>
		<p style="clear: both;"></p>
		<hr>


    	<!-- Ordering options -->
		<div style="text-align: center; width: 100%;" >
	
			<form name="bForm" id="bForm" method="POST" action="">
		
				{t}Mailings per Page:{/t} 
			
				<SELECT name="limit" onChange="document.bForm.submit()">
					<option value="10"{if $state.limit == '10'} SELECTED{/if}>10</option>
					<option value="20"{if $state.limit == '20'} SELECTED{/if}>20</option>
					<option value="50"{if $state.limit == '50'} SELECTED{/if}>50</option>
					<option value="100"{if $state.limit == '100'} SELECTED{/if}>100</option>
				</SELECT>
		
				<span style="width: 30px;"></span>
	
				{t}Order by:{/t}
				<SELECT name="sortBy" onChange="document.bForm.submit()">
					<option value="subject"{if $state.sortBy == 'subject'} SELECTED{/if}>subject</option>
					<option value="started"{if $state.sortBy == 'started'} SELECTED{/if}>Start Date</option>
					<option value="finished"{if $state.sortBy == 'finished'} SELECTED{/if}>Finish Date</option>
					<option value="mailgroup"{if $state.sortBy == 'mailgroup'} SELECTED{/if}>Mail group</option>
					<option value="sent"{if $state.sortBy == 'sent'} SELECTED{/if}>Mails Sent</option>
					<option value="ishtml"{if $state.sortBy == 'ishtml'} SELECTED{/if}>HTML Mail</option>
				</SELECT>

				<span style="width: 15px;"></span>
	
				<SELECT name="sortOrder" onChange="document.bForm.submit()">
					<option value="ASC"{if $state.sortOrder == 'ASC'} SELECTED{/if}>{t}ascending{/t}</option>
					<option value="DESC"{if $state.sortOrder == 'DESC'} SELECTED{/if}>{t}descending{/t}</option>
				</SELECT>
	
			</form>
			
		</div>
		<!-- End Ordering Options -->

		<br><br>
		<div style="text-align: center; width: 100%;" >
			( <em>{t 1=$rowsinset}%1 mailings{/t}</em> )
		</div>

		<!-- Table of Mailings -->
		<div style="text-align: center; width: 100%;" id="mailingtable" >
	
		<form name="oForm" id="oForm" method="POST" action="mailings_mod.php">
			<table cellspacing="0" cellpadding="5" border="0" style="text-align: left; margin: 10px; margin-left:auto; margin-right:auto; ">

					<!--Table headers-->

					<tr>
							<td nowrap style="text-align:center;">{t}select{/t}</td>
							<td nowrap style="text-align:center;">{t}delete{/t}</td>
							<td nowrap style="text-align:center;">{t}view{/t}</td>
							<td nowrap style="text-align:center;">{t}reload{/t}</td>
					  		<td nowrap style="text-align:center;"><b>{t}Subject{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Group (count){/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Sent{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Started{/t}</b></td>
				  			<td nowrap style="text-align:center;"><b>{t}Finished{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}Duration{/t}</b></td>
					  		<td nowrap style="text-align:center;"><b>{t}HTML{/t}</b></td>
					</tr>

			
					<!-- The Mailings -->	
				{foreach name=mailloop from=$mailings key=key item=mailitem}
					<tr bgcolor="{cycle values="#EFEFEF,#FFFFFF"}">

							<td style="text-align:center;" nowrap>
									<input type="checkbox" name="mailid[]" value="{$mailitem.mailid}">
							</td>
						
							<td style="text-align:center;" nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=delete">{t}delete{/t}</a>
							</td>

							<td style="text-align:center;" nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=view">{t}view{/t}</a>
							</td>

							<td style="text-align:center;" nowrap>
									<a href="mailings_mod.php?mailid={$mailitem.mailid}&action=reload">
									<img src="{$url.theme.shared}images/icons/reload-small.png" border="0" alt="{t}Reload, edit and resend Mail{/t}"></a>{*<!--{t}reload{/t}-->*}
							</td>

							<td nowrap><i>{$mailitem.subject}</i></td>
							<td nowrap>{$mailitem.mailgroup} ({$mailitem.subscriberCount})</td>
							<td style="text-align:center;" nowrap>{$mailitem.sent}</td>
							<td style="text-align:center;" nowrap>{$mailitem.started}</td>
							<td style="text-align:center;" nowrap>{$mailitem.finished}</td>
							<td nowrap>{$mailitem.duration} 
							{if $mailitem.mps}
								({$mailitem.mps} {t}mails/second{/t})</td>
							{/if}
							<td style="text-align:center;">
							{if $mailitem.ishtml == 'on'}
								<a href="mailing_preview.php?action=viewhtml&viewid={$mailitem.mailid}" target="_blank">
								<img src="{$url.theme.shared}images/icons/viewhtml.png" border="0" alt="{t}View HTML in new browser window{/t}"></a>
							{/if}
							</td>
							
						{*{foreach name=propsloop from=$mailitem key=key item=item}
							<td nowrap>{$item}</td> {$key}:{$item}
						{/foreach}-$mailitem.finished}*}
				
				
					</tr>				
				{foreachelse}
					<tr>
						<td colspan="11">
							{t}No mailing found.{/t}
						</td>
					</tr>
				
				{/foreach}
				
				

					<tr>
							<td colspan="12" style="text-align:left;">
								<b><a href="javascript:SetChecked(1,'mailid[]');">{t}Check All{/t}</a> 
								&nbsp;&nbsp; || &nbsp;&nbsp; 
								<a href="javascript:SetChecked(0,'mailid[]');">{t}Clear All{/t}</a></b>
							</td>
					</tr>
				
			</table>
		</div>

		<div style="text-align: center; width: 100%;" >
		
			<SELECT name="action">
					<option value="view">{t}View{/t} {t}checked mailings{/t}</option>
					<option value="delete">{t}Delete{/t} {t}checked mailings{/t}</option>
			</SELECT>

			&nbsp;&nbsp;&nbsp; 
			<input type="submit" name="send" value="{t}go{/t}">
					
			<br><br>
			{$pagelist}

		</form>
	
		</div>

		<!-- End Table of Mailings -->



	<!-- end mainbar -->

	{literal}
	<script type="text/javascript">
	// <![CDATA[

	/* The following code is to "check all/check none" NOTE: form name must properly be set */
	var form='oForm' //Give the form name here
	function SetChecked(val,chkName) {
		dml=document.forms[form];
		len = dml.elements.length;
		var i=0;
		for( i=0 ; i<len ; i++) {
			if (dml.elements[i].name==chkName) {
				dml.elements[i].checked=val;
			}
		}
	}
	// ]]>
	</script>
	{/literal}

{include file="admin/inc.footer.tpl"}