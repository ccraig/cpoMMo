{capture name=head}{* used to inject content into the HTML <head> *}
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/default.mailings.css" />
<script type="text/javascript" src="{$url.theme.shared}js/jq/jquery.js"></script>
{/capture}
{include file="inc/admin.header.tpl"}

<p><img src="{$url.theme.shared}images/icons/alert.png" class="navimage right" alt="thunderbolt icon" />
{t escape=no 1="<span class='advanced'><a href='`$url.base`admin/setup/setup_throttle.php'>" 2='</a>'}Mailings take place in the background so feel free to close this page, visit other sites, or even turn off your computer. You can always return to this status page by visiting the Mailings section.  %1Throttle settings%2 can also be adjusted -- although you must pause and revive the mailing before changes take effect.{/t}</span></p>

<div>

{t 1=$mailing.tally}Sending message to %1 subscribers.{/t}

</div>

{* Updates via AJAX: Processing Mailing, Mailing Finished, Mailing Frozen *}
<div class="warn">

{t}Mailing Status{/t} &raquo; <span id="status"></span>

</div>

{* Updates via AJAX: Pause/Resume (started), Resume/Cancel (stopped), DeThaw/Cancel (frozen) *}
<div id="commands">

<div class="error init">

{t}Initializing...{/t}

</div>

</div>

{* Hidden until mailing is finished *}
<div id="finished" class="hidden error">

{t}Mailing Finished{/t} -- <a href="admin_mailings.php">{t}Return to{/t} {t}Mailings Page{/t}</a>

</div>

{* Displayed when a command is clicked *}
<div id="wait" class="hidden error">

{t}Command Recieved. Please wait...{/t}

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

{* the folowing populate #commands via Javascript, and are here for reference/translation *}
<div class="hidden" id="started">

<div class="first">

<a class="cmd" href="#stop"><img src="{$url.theme.shared}images/icons/pause-small.png" alt="pause icon" />{t}Pause Mailing{/t}</a>

</div>

<div class="second">

<a class="cmd" href="#restart">{t}Resume Mailing{/t} <img src="{$url.theme.shared}images/icons/restart-small.png" alt="restart icon" /></a>

</div>

</div>

	<div class="hidden" id="stopped">
	
	<div class="first">
	
	<a class="cmd" href="#restart"><img src="{$url.theme.shared}images/icons/restart-small.png" alt="pause icon" /> {t}Resume Mailing{/t}</a>
	
	</div>
	
	<div class="second">
	
	<a class="cmd" href="#cancel">{t}Cancel Mailing{/t}	<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="restart icon" /></a>
	
	</div>

</div>

<div class="hidden" id="frozen">

<div class="first">

<a class="cmd" href="#restart"><img src="{$url.theme.shared}images/icons/restart-small.png" alt="pause icon" />{t}Dethaw Mailing{/t}</a>

</div>

<div class="second">

<a class="cmd" href="#cancel">{t}Cancel Mailing{/t}	<img src="{$url.theme.shared}images/icons/stopped-small.png" alt="restart icon" /></a>

</div>

</div>

{literal}
<script type="text/javascript">
pommo = {
	init: function() {
		this.disabled = true;
		this.attempt = 1;
		this.waiting = false;
		this.poll();
	},
	poll: function() {
		this.polling = true;
		$.post("ajax/status_poll.php?id={/literal}{$mailing.id}{literal}&attempt="+pommo.attempt, {}, function(out) {
			pommo.disabled = false; // enable commands after AJAX success
			eval("var json = " + out);
				if (typeof(json.status) == 'undefined')
			alert('ajax error!');

			if (this.waiting && !json.waiting)
				return;

			$('#status').html(json.statusText);

			// status >> 1: Processing  2: Stopped  3: Frozen  4: Finished
			if (json.status == 1)
				$('#barHead').find('img.go').css('display', 'inline').end().find('img.stop').css('display', 'none');
			else 
				$('#barHead').find('img.go').css('display', 'none').end().find('img.stop').css('display', 'inline');

			if(!json.command || $('#commands div.init').size() > 0) {
				switch(json.status) {
					case 1: $('#commands').html($('#started').html()); break;
					case 2: $('#commands').html($('#stopped').html()); break;
					case 3: $('#commands').html($('#frozen').html()); break;
					case 4: $('#commands').html('').append($('#finished').clone().css('display','block')); break;
				}
			}

			$('#commands a.cmd').click(function() { return pommo.click(this); });

			$('#sent').html(json.sent);
			$('#barFoot').html(json.percent+'%');
			$('#bar').width(json.percent+'%');

			if (typeof(json.notices) == 'object')
				for (i in json.notices)
					if (json.notices[i] != '')
						$('#notices').prepend('<li>'+json.notices[i]+'</li>');

			// TODO --> make a nice XPATH selector out of this...
			if ($('#notices li').size() > 50) {
				$('#notices li').each(function(i){ if (i > 40) $(this).remove(); });
			}

			pommo.attempt = (json.incAttempt) ? pommo.attempt + 1 : 1;

			// repoll
			if (json.status < 3) {
				if(pommo.attempt == 1)
					setTimeout('pommo.poll()',5500);
				else if(pommo.attempt == 2)
					setTimeout('pommo.poll()',7500);
				else
					setTimeout('pommo.poll()',8500);
			}
			else
				pommo.polling = false;
		});

	},
	sendCmd: function(cmd) {
		this.disabled = true;
		this.attempt = 1;
		$('#commands').html('').append($('#wait').clone().css('display','block'));

		$.post('ajax/status_cmd.php?cmd='+cmd,{}, function(out) {
			eval("var json = " + out);
				if (typeof(json.success) == 'undefined')
					alert('ajax error!');

			if (!pommo.polling)
				pommo.poll();
		});
	},
	click: function(e) {
		if (this.disabled)
			return false;
		this.sendCmd($(e).href().replace(/.*\#/,''));
		return false;
	}
};

$().ready(function(){ pommo.init(); });
</script>
{/literal}

{include file="inc/admin.footer.tpl"}