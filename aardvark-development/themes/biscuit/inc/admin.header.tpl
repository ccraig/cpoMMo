<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$title}</title>

<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/pommo.js"></script>
<script type="text/javascript">poMMo.confirmMsg = '{t escape=js}Are you sure?{/t}';</script>

<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.admin.css" />
</head>


{* If $head has been captured, print its contents here. Capture $head via templates
	using {capture name=head}..content..{/capture} before including this header file. 
	Useful for properly including javascripts and CSS in the HTML <head></head> *}
{$smarty.capture.head}
	


<body>

	<div id="wrapper">

	<div id="header">
		<a href="{$config.site_url}">{$config.site_name}</a>
	</div> 

	<div id="menu">
		<ul>
        	<li><a href="{$url.base}index.php?logout=TRUE">{t}Logout{/t}</a></li>
            
            <li><a href="{$url.base}support/support.php">{t}Support{/t}</a>
            	<ul>
					<li><a href="{$url.base}support/support.notes.php">{t}Support Notes{/t}</a></li>
					<li><a href="{$url.base}support/support.lib.php">{t}Support Library{/t}</a></li>
				</ul>
            </li>
            
            <li><a href="{$url.base}admin/setup/admin_setup.php">{t}Setup{/t}</a>
				<ul>
					<li><a href="{$url.base}admin/setup/setup_configure.php">{t}Configure{/t}</a></li>
					<li><a href="{$url.base}admin/setup/setup_form.php">{t}Subscription Forms{/t}</a></li>
                    <li><a href="{$url.base}admin/setup/setup_language.php">{t}Language Settings{/t}</a></li>
				</ul>
             </li>
            
            <li><a href="{$url.base}admin/subscribers/admin_subscribers.php">{t}Subscribers{/t}</a>
				<ul>
                    <li><a href="{$url.base}admin/subscribers/subscribers_manage.php">{t}Manage / List{/t}</a></li>
					<li><a href="{$url.base}admin/subscribers/subscribers_import.php">{t}Import{/t}</a></li>
                    <li><a href="{$url.base}admin/subscribers/subscribers_groups.php">{t}Mailing Groups{/t}</a></li>
					<li><a href="{$url.base}admin/setup/setup_fields.php">{t}Subscriber Fields{/t}</a></li>
				</ul>
            </li>
            
            <li><a href="{$url.base}admin/mailings/admin_mailings.php">{t}Mailings{/t}</a>
        		<ul>
					<li><a href="{$url.base}admin/mailings/mailings_start.php">{t}Send Mail{/t}</a></li>
                    <li><a href="{$url.base}admin/mailings/mailing_status.php">{t}Status{/t}</a></li>
                    <li><a href="{$url.base}admin/mailings/mailings_history.php">{t}History{/t}</a></li>
				</ul>
            </li>
        </ul>
	</div>
    
    <div id="content">