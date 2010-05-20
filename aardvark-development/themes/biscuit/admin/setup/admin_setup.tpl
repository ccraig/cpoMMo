{include file="inc/admin.header.tpl"}

<h2>{t}Setup Page{/t}</h2>

<ul id="sec_nav">
	<li><a href="{$url.base}admin/setup/setup_configure.php"><img src="{$url.theme.shared}images/icons/settings.png" alt="settings icon" class="navimage" />
	<h3>{t}Configure{/t}</h3>
	<span>{t}Set your mailing list name, administrator's information, default behaviors, define all messages, turn off and on the demonstration mode, and edit many other items.{/t}</span></a>
	</li>
    
	<li><a href="{$url.base}admin/setup/setup_form.php"><img src="{$url.theme.shared}images/icons/form.png" alt="form icon" class="navimage" />
	<h3>{t}Subscription Forms{/t}</h3>
	<span>{t}Preview and generate the subscription form for your website. The forms can be tailored and themed to fit into your existing website.{/t}</span></a>
	</li>

	<li><a href="{$url.base}admin/setup/setup_language.php"><img src="{$url.theme.shared}images/icons/language.png" alt="language icon" class="navimage" />
	<h3>{t}Language Settings{/t}</h3>
    <span>{t}Choose the language for the poMMo management software. There are currently 14 international languages available.{/t}</span></a>
	</li>
</ul>

{include file="inc/admin.footer.tpl"}