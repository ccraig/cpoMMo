<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>{$config.site_name} {t}Subscription{/t}</title>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.user.css" />
{if $datePicker}{include file="`$config.app.path`themes/shared/datepicker/datepicker.tpl"}{/if}
</head>
<body>

<h2>{$config.list_name} {t}Subscription{/t}</h2>

{include file="subscribe/form.subscribe.tpl"}

</body>
</html>