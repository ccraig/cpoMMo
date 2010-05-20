{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />
{/capture}
{include file="inc/admin.header.tpl"}

<p><img src="{$url.theme.shared}images/icons/alert.png" class="navimage right" alt="thunderbolt icon" />
{t escape=no 1="<a href='`$url.base`admin/setup/setup_configure.php#mailings'>" 2='</a>'}Mailings take place in the background so feel free to close this page, visit other sites, or even turn off your computer. You can always return to this status page by visiting the Mailings section.  %1Throttle settings%2 can also be adjusted -- although you must pause and revive the mailing before changes take effect.{/t}</p>

<div>{t 1=$mailing.tally}Sending message to %1 subscribers.{/t}</div>

{* Updates via AJAX: Processing Mailing, Mailing Finished, Mailing Frozen *}
<div class="warn">
{t}Mailing Status{/t} &raquo; <span id="status"></span>
</div>

{* Updates via AJAX: Pause/Resume (started), Resume/Cancel (stopped), DeThaw/Cancel (frozen) *}
<div id="commands">

	<div class="error uniq" id="init">{t}Initializing...{/t}</div>

	<div class="hidden uniq" id="started">
		<div class="first"><a class="cmd" href="#stop"><img src="{$url.theme.shared}images/icons/pause-small.png" alt=" icon" />{t}Pause Mailing{/t}</a></div>
		<div class="second"><a class="cmd" href="#restart">{t}Resume Mailing{/t} <img src="{$url.theme.shared}images/icons/restart-small.png" alt="icon" /></a></div>
	</div>
	
	<div class="hidden uniq" id="stopped">
		<div class="first"><a class="cmd" href="#restart"><img src="{$url.theme.shared}images/icons/restart-small.png" alt="icon" /> {t}Resume Mailing{/t}</a></div>
		<div class="second"><a class="cmd" href="#cancel">{t}Cancel Mailing{/t}	<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="icon" /></a></div>
	</div>
	
	<div class="hidden uniq" id="frozen">
		<div class="first"><a class="cmd" href="#restart"><img src="{$url.theme.shared}images/icons/restart-small.png" alt="icon" />{t}Resume Mailing{/t}</a></div>
		<div class="second"><a class="cmd" href="#cancel">{t}Cancel Mailing{/t}	<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="icon" /></a></div>
	</div>
	
	{* Hidden until mailing is finished *}
	<div id="finished" class="hidden error uniq">
		{t}Mailing Finished{/t} -- <a href="admin_mailings.php">{t}Return to{/t} {t}Mailings Page{/t}</a>
	</div>
	
	{* Displayed when a command is clicked *}
	<div id="pause" class="hidden error uniq">
		{t}Command Recieved. Please wait...{/t}
	</div>
	
</div>


<div id="barHead">
{t escape="no" 1='<span id="sent">0</span>'}%1 mails sent{/t}

<img class="anim go" src="{$url.theme.shared}images/loader.gif" alt="Processing" />
<img class="anim hidden stop" src="{$url.theme.shared}images/icons/stopped-small.png" alt="Stopped" />

</div>

<div id="barBox">
	<div id="barTrack">
		<div id="bar"></div>
	</div>
</div>

<div id="barFoot"></div>

<form>
<fieldset>
<legend>{t}Last 50 notices{/t}</legend>

<ul class="inpage_menu">
<li><a href="ajax/status_download.php?type=sent">{t}View{/t} {t}Sent Emails{/t}</a></li>
<li><a href="ajax/status_download.php?type=unsent">{t}View{/t} {t}Unsent Emails{/t}</a></li>
<li><a href="ajax/status_download.php?type=error">{t}View{/t} {t}Failed Emails{/t}</a></li>
</ul>

<div id="notices"></div>

</fieldset>
</form>


{literal}
<script type="text/javascript">

var pommo = {
	status: null,
	poll: function(get){get = get || '';  $.getJSON("ajax/status_poll.php?id={/literal}{$mailing.id}{literal}&"+get,pommo.process)},
	process: function(mailing) {
		$('#status').html(mailing.statusText);
		
		// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished    5: command Sent	
		$('#barHead img.go').css({display:((mailing.status == 1)?'inline':'none')});
		$('#barHead img.stop').css({display:((mailing.status == 1)?'none':'inline')});
		
		$('#sent').html(mailing.sent);
		$('#barFoot').html(mailing.percent+'%');
		$('#bar').width(mailing.percent+'%');
		
		if (mailing.status != pommo.status) {
			pommo.status = mailing.status;
			var id = null;
			switch(mailing.status) {
				case 1: id = 'started'; break;
				case 2: id = 'stopped'; break;
				case 3: id = 'frozen'; break;
				case 4: id = 'finished'; break; 
			}
			$('#'+id).show().siblings('div.uniq').hide();
		}
		
		if (typeof(mailing.notices) == 'object')
			for (i in mailing.notices)
				if (mailing.notices[i] != '')
					$('#notices').prepend('<li>'+mailing.notices[i]+'</li>');
	
		// TODO --> make a nice XPATH selector out of this...
		if ($('#notices li').size() > 50) {
			$('#notices li').each(function(i){ if (i > 40) $(this).remove(); });
		}		
		
	}
};

// continually ("hearbeat") poll the mailing
$('body').ajaxStop(function(){ 
	if (pommo.status == 1)
		setTimeout('pommo.poll()',4500);
});

$().ready(function(){ 

	// assign command events
	$('#commands a.cmd').click(function() { 
		if(pommo.status != 5) {
			pommo.status = 5;
			$('#pause').show().siblings('div.uniq').hide();
			var cmd = $(this).attr('href').replace(/.*\#/,'');
			$.getJSON(
				'ajax/status_cmd.php?cmd='+cmd,
				function(ret) { setTimeout('pommo.poll()',1500); }
			);
		}
		return false;
	});
	
	// init
	pommo.poll('resetNotices=true'); 
});

</script>
{/literal}

{include file="inc/admin.footer.tpl"}