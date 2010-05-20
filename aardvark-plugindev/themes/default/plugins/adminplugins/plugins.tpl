{include file="inc/admin.header.tpl"}

<h2>{t}Plugin Menu{/t}</h2>

<div id="boxMenu">

		<div>
			<a href="{$url.base}plugins/adminplugins/usermanager/user_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Manage Users for Pommo and their permissions{/t}</a> - 
			{t}Use more users in pommo{/t}
		</div>

		<div>
			<a href="{$url.base}plugins/adminplugins/listmanager/list_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Mailing List Management{/t}</a> - 
			{t}Different users can have different lists to manage.{/t}
		</div>	

		<div>
			<a href="{$url.base}plugins/adminplugins/respmanager/resp_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Manage Responsible Persons for Mailing Lists{/t}</a> - 
			{t}Witch one will you choose??????{/t}
		</div>

		<div>
			<a href="{*{$url.base}plugins/adminplugins/pluginconfig/config_main.php*}">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}Bounce Mailbox{/t}</a> - 
			{t}Manage and see the bounce mailbox. Download mails, ... don't know yet{/t}
		</div>
		
		<div>
			<a href="{$url.base}plugins/adminplugins/pluginconfig/config_main.php">
			<img src="" class="navimage" width="64" height="64" /> <!--src="{$url.theme.shared}/images/icons/subscribersa.png"-->
			{t}GENERAL PLUGIN SETUP{/t}</a> - 
			{t}Plugin 'connections' setup. (text?) The standard values for LDAP, DB, Auth, Bounce Server, if you want to use special features like mailing queue or not -- go here {/t}
		</div>

</div>

{include file="inc/admin.footer.tpl"}