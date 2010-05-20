{capture name=head}{* used to inject content into the HTML <head> *}
<link href="{$url.theme.this}inc/mailing_status.css" type="text/css" rel="STYLESHEET">
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
{/capture}{include file="admin/inc.header.tpl"}


<div>
<img src="{$url.theme.shared}images/icons/alert.png" align="middle" style="float: left;">
{t}The mailing process takes place in the background, so feel free to close your browser, visit other sites, or work within poMMo. Throttle settings can be adjusted, although you must pause and revive a mailings before the changes take effect.{/t}
</div>

<div style="width: 514px; margin: auto;">
	
	<div style="text-align: center;">
		<br>
		<strong>{t 1=$subscriberCount}Sending message to %1 subscribers.{/t}</strong>
	</div>
	
	<div id="ajaxUpdate"></div>
	
	<div style="position: relative;">
		<div class="stacked" id="started" style="z-index: 1; visibility: hidden;">
			<em>({t}Processing Mailing{/t})</em>
		</div>
		
		<div class="stacked" id="stopped" style="z-index: 2; visibility: hidden;">
			<em>({t}Mailing Paused{/t})</em>
		</div>
		
		<div class="stacked" id="finished" style="z-index: 3; visibility: hidden;">
			<em>({t}Mailing Finished{/t})</em>
		</div>
		
		<div class="stacked" id="frozen" style="z-index: 4; visibility: hidden;">
			<em>({t}Mailing Frozen{/t})</em>
		</div>
		
		<br>
		<hr>
		
		<div class="stacked" id="startedCmd" style="z-index: 1; visibility: hidden;">
			<span style="position: absolute; left: 0;">
				<a href="mailing_status2.php?command=pause">
					<img src="{$url.theme.shared}images/icons/pause-small.png" border="0" align="absmiddle">
					{t}Pause Mailing{/t}
				</a> 
			</span>
			<span style="position: absolute; right: 0;">
				<a href="mailing_status2.php?command=restart">
					{t}Resume Mailing{/t}
					<img src="{$url.theme.shared}images/icons/restart-small.png" border="0" align="absmiddle">
				</a>
			</span>
		</div>
		
		<div class="stacked" id="stoppedCmd" style="z-index: 2; visibility: hidden;">
			<span style="position: absolute; left: 0;">
				<a href="mailing_status2.php?command=restart">
					<img src="{$url.theme.shared}images/icons/restart-small.png" border="0" align="absmiddle">
					{t}Resume Mailing{/t}
				</a> 
			</span>
			<span style="position: absolute; right: 0;">
				<a href="mailing_status2.php?command=kill">
					{t}Cancel Mailing{/t}
					<img src="{$url.theme.shared}images/icons/stopped-small.png" border="0" align="absmiddle">
				</a>
			</span>
		</div>
		
		<div class="stacked" id="frozenCmd" style="z-index: 5; visibility: hidden;">
			<span style="position: absolute; left: 0;">
				<a href="mailing_status2.php?command=restart">
					<img src="{$url.theme.shared}images/icons/restart-small.png" border="0" align="absmiddle">
					{t}Dethaw Mailing{/t}
				</a> 
			</span>
			<span style="position: absolute; right: 0;">
				<a href="mailing_status2.php?command=kill">
					{t}Cancel Mailing{/t}
					<img src="{$url.theme.shared}images/icons/stopped-small.png" border="0" align="absmiddle">
				</a>
			</span>
		</div>
		
		<div class="stacked" id="finishedCmd" style="z-index: 3; visibility: hidden;">
			{t}Mailing Finished{/t} -- <a href="admin_mailings.php">{t}Return to{/t} {t}Mailings Page{/t}</a>
		</div>
		
		<div class="stacked" id="waitCmd" style="z-index: 4;">
			{t}Command Recieved... Please wait.{/t}
		</div>
		
		<br>
		<hr>
		
	</div>
		
	<div class="pbBarText" id="pbBarText">
		<span id="pbTimer" style="font-weight: normal">
			{t escape=no 1='<span id="updateTime"></span>'}Polling in %1 seconds{/t}
		</span>
		<span id="pbBarTextPercent" style="margin-left: 30px;">0</span> {t}sent{/t}
	</div>
	<div class="pbTrack">
		<div class="pbBarContainer">
			<div class="pbBar" id="pbBar"></div>
		</div>
	</div>
	<div class="pbText" id="pbText">0%</div>

</div>


<div id = "notices" style="position: relative; visibility: hidden;">
	<div class="notices">{t}NOTICES{/t}</div>
	<a href="mailing_status2.php?command=clear">{t}Clear Notices{/t}</a>
	<hr>
	<span id="noticeWarn" style="float: right; visibility: padding: 3px 3px 3px 3px;" class="errMsg">
	 	{t}Too many notices. Displaying the last 50...{/t}<br>
	 	<a href="mailing_status2.php?command=clear50">{t}Clear Last 50 Notices{/t}</a>
	</span>
	<span id="notice"></span>
</div>

{literal}
<script type="text/javascript">
// <![CDATA[

/* JSON objects - pb (progress bar) ,  ce (command executer) */

var pb = {
  init: function() {
    this.percent = 0;
    this.updater = new Ajax.PeriodicalUpdater(
      'ajaxUpdate',
      'ajax_status.php',
      { 
        frequency: 5, 
        onSuccess: this.update.bind(this)
      }
    );
    pb.nextUpdate = 5;
    pb.updateTimer();
  },
  update: function(resp, json) {
    $('pbBarTextPercent').innerHTML = json.sent;
    $('pbText').innerHTML = json.percent + "%";
    $('pbBar').setStyle({width: json.percent + '%' });
    
     if (json.status == 'frozen') {
     	this.updater.stop(); 
     	setTimeout(pb.stopper,300);
     	
     	$('started').style.visibility = 'hidden';
    	$('stopped').style.visibility = 'hidden';
    	$('finished').style.visibility = 'hidden';
    	$('frozen').style.visibility = 'visible';
    	
    	$('startedCmd').style.visibility = 'hidden';
    	$('stoppedCmd').style.visibility = 'hidden';
    	$('waitCmd').style.visibility = 'hidden';
    	$('finishedCmd').style.visibility = 'hidden';
    	$('frozenCmd').style.visibility = 'visible';
     }
    
     else if (json.status == 'finished') { 
     	this.updater.stop(); 
     	setTimeout(pb.stopper,300);

     	$('started').style.visibility = 'hidden';
    	$('stopped').style.visibility = 'hidden';
    	$('frozen').style.visibility = 'hidden';
    	$('finished').style.visibility = 'visible';
    	
    	$('startedCmd').style.visibility = 'hidden';
    	$('stoppedCmd').style.visibility = 'hidden';
    	$('waitCmd').style.visibility = 'hidden';
    	$('frozenCmd').style.visibility = 'hidden';
    	$('finishedCmd').style.visibility = 'visible';
    	
    	$('pbTimer').style.visibility = 'hidden';
     }
    else if (json.status == 'stopped') {
    	$('started').style.visibility = 'hidden';
    	$('frozen').style.visibility = 'hidden';
    	$('stopped').style.visibility = 'visible';
    	
    	 if (json.command == 'none') {
    	 	$('startedCmd').style.visibility = 'hidden';
    	 	$('waitCmd').style.visibility = 'hidden';
    	 	$('frozenCmd').style.visibility = 'hidden';
    		$('stoppedCmd').style.visibility = 'visible';
    	 }
    	 else {
    	 	$('startedCmd').style.visibility = 'hidden';
    		$('stoppedCmd').style.visibility = 'hidden';
    		$('frozenCmd').style.visibility = 'hidden';
    		$('waitCmd').style.visibility = 'visible';
    	 }
    	 
    }
    else {
    	$('stopped').style.visibility = 'hidden';
    	$('frozen').style.visibility = 'hidden';
    	$('started').style.visibility = 'visible';
    	
    	
    	if (json.command == 'none') {
    	 	$('stoppedCmd').style.visibility = 'hidden';
    	 	$('waitCmd').style.visibility = 'hidden';
    	 	$('frozenCmd').style.visibility = 'hidden';
    		$('startedCmd').style.visibility = 'visible';
    	 }
    	 else {
    	 	$('startedCmd').style.visibility = 'hidden';
    		$('stoppedCmd').style.visibility = 'hidden';
    		$('frozenCmd').style.visibility = 'hidden';
    		$('waitCmd').style.visibility = 'visible';
    	 }
    }
    
    if (json.notices) {
    	$('notices').style.visibility = 'visible';
    	
    	if (json.notices.length > 40) {
    		$('noticeWarn').style.visibility = 'visible';
    	}
    	else {
    		$('noticeWarn').style.visibility = 'hidden';
    	}
    	
    	$('notice').innerHTML = "<ul>";
    	for (var i = 0;i<json.notices.length;i++) {
  			$('notice').innerHTML += "<li>" + json.notices[i] + "</li>";
		}
		$('notice').innerHTML += "</ul>";
    }
    else {
    	$('notices').style.visibility = 'hidden';
    }
    
    pb.nextUpdate = 5;
    
  },
  stopper: function() {
  	pb.updater.stop();
  },
  updateTimer: function() {
  	if (pb.nextUpdate > 0) {
  		pb.nextUpdate = pb.nextUpdate - 1;
  		$('updateTime').innerHTML = pb.nextUpdate;
  	}
  	setTimeout(pb.updateTimer,1000);
  }
}

/* INIT FUNCTIONS */
pb.init();

// ]]>
</script>
{/literal}
	
{include file="admin/inc.footer.tpl"}