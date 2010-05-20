{include file="inc/admin.header.tpl"}

<h2>{t}Subscription Forms{/t}</h2>

<ul id="sec_nav">
	<li><a href="{$url.base}user/subscribe.php" target="_blank"><img src="{$url.theme.shared}images/icons/form.png" alt="form icon" class="navimage" />
	<h3>{t}Default{/t}<br />{t}Subscription Form{/t}</h3>
	<span>{t}Preview the default subscription form. Its look and feel can be adjusted through the theme template ([theme]/user/subscribe.tpl).{/t}</span></a>
	</li>
    
	<li><a href="{$url.base}admin/setup/form_embed.php"><img src="{$url.theme.shared}images/icons/embed.png" alt="embed icon" class="navimage" />
	<h3>{t}Embedded{/t}<br />{t}Subscription Form{/t}</h3>
	<span>{t}Preview subscription forms that you can embed into an area of an existing webpage.{/t}</span></a>
	</li>

	<li><a href="{$url.base}admin/setup/form_generate.php" target="_blank"><img src="{$url.theme.shared}images/icons/plain.png" alt="html icon" class="navimage" />
	<h3>{t}HTML{/t}<br />{t}Subscription Form{/t}</h3>
    <span>{t}Generate a plain HTML subscription form that you can customize to fit your site.{/t}</span></a>
	</li>
</ul>

{include file="inc/admin.footer.tpl"}