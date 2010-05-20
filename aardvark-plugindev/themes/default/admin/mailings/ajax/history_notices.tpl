<div id="addOut" class="error"></div>
<div class="warn"></div>

<ul>
{foreach from=$notices item=n}
<li>{$n}</li>
{foreachelse}
<li>{t}Unavailable{/t}</li>
{/foreach}
</ul>