{capture name=head}{* used to inject content into the HTML <head> *}
<script src="{$url.theme.shared}js/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="{$url.theme.shared}js/scriptaculous/slider.js" type="text/javascript"></script>
{/capture}{include file="admin/inc.header.tpl"}
{include file="admin/inc.sidebar.tpl"}
	
<div id="mainbar">

	<h1>{t}Configure{/t}</h1>
	<img src="{$url.theme.shared}images/icons/settings.png" class="articleimg">

	<p>
		{t}poMMo can throttle mails so you don't overload your server or slam a common domain (such as hotmail/yahoo.com). Mail volume and bandwith can be controlled. Additionally, you can limit the mails and kilobytes sent to a single domain during a specified time frame.{/t}
	</p>
	
	<a href="{$url.base}admin/setup/setup_configure.php">
		<img src="{$url.theme.shared}images/icons/back.png" align="middle" class="navimage" border='0'>
		{t 1=$returnStr}Return to %1{/t}</a>
		
	<h2>{t}Throttling{/t} &raquo;</h2>
	
	{if $messages}
    	<div class="msgdisplay">
    	{foreach from=$messages item=msg}
    	<div>* {$msg}</div>
    	{/foreach}
    	</div>
 	{/if}




<style>
#track1 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle1 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track2 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle2 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track3 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle3 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track4 {ldelim}background:url({$url.theme.shared}images/slider_track.png) no-repeat; height:26px; width:218px;{rdelim}
#handle4 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
#track5 {ldelim}background:url({$url.theme.shared}images/slider_track2.png) no-repeat; height:26px; width:218px;{rdelim}
#handle5 {ldelim}width:18px;height:28px;background:url({$url.theme.shared}images/slider_handle.png) no-repeat bottom center;cursor:move;{rdelim}
</style>

<form action="" method="POST">

<fieldset>
    <legend>Throttle Controller</legend>

<div class="field">
<table align="center" border="0">
<tr><td></td><td>Hour</td><td>Minute</td><td>Second</td></tr>
<tr>
  <td align="right">Mails</td>
  <td><input id="mph" type="text" readonly size="7"></td>
  <td><input id="mpm" type="text" readonly size="7"></td>
  <td><input id="mps" name="mps" type="text" readonly size="7"></td>
</tr>
<tr>
  <td align="right">Megabytes</td>
  <td><input id="mbph" type="text" readonly size="7"></td>
  <td><input id="mbpm" type="text" readonly size="7"></td>
  <td><input id="mbps" type="text" readonly size="7"></td>
</tr>
<tr>
  <td align="right">Kilobytes</td>
  <td><input id="kbph" type="text" readonly size="7"></td>
  <td><input id="kbpm" type="text" readonly size="7"></td>
  <td><input id="kbps" name="kbps" type="text" readonly size="7"></td>
</tr>
</table>
</div>

<div class="field">
  Mail Rate
  <div id="track1"><div id="handle1"></div></div>
</div>

<div class="field">
  Bandwith
  <div id="track2"><div id="handle2"></div></div>

<div class="notes">
  If a controller is set to off (0), mails will be sent without consulting the throttler, resulting in very fast sending of mails but also high server load.
</div>

</div>


</fieldset>

<div align="center"><input class="button" id="throttle-submit" name="throttle-submit" type="submit" value="Save Values" /><br>&nbsp;</div>

<fieldset>
    <legend>Domain Controller</legend>

<div class="field">
  Period length (In Seconds): <table align="right" border="0"><tr><td><input id="dp" name="dp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track5"><div id="handle5"></div></div>
</div>

<div class="field">
  Max Mails sent per Period: <table align="right" border="0"><tr><td><input id="dmpp" name="dmpp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track3"><div id="handle3"></div></div>
</div>

<div class="field">
  Max Kilobytes sent per Period: <table align="right" border="0"><tr><td><input id="dbpp" name="dbpp" type="text" readonly size="3"></td><td style="width: 77px;"></td></tr></table>
  <div id="track4"><div id="handle4"></div></div>
</div>


</fieldset>

<div align="center">
	<input class="button" id="throttle-submit" name="throttle-submit" type="submit" value="Save Values" />
	<br><br><br>---<br>&nbsp;
	<input class="button" id="throttle-restore" name="throttle-restore" type="submit" value="Restore Defaults" />
	<br>&nbsp;
</div>

</form>

{literal}
<script type="text/javascript" language="javascript">
// <![CDATA[
	var s_mps = new Control.Slider('handle1','track1',{
	range: $R(0,5),
	onSlide:function(v){$('mps').value=+v,$('mpm').value=+v*60,$('mph').value=+v*60*60},
	onChange:function(v){$('mps').value=+v,$('mpm').value=+v*60,$('mph').value=+v*60*60}});

	var s_bps = new Control.Slider('handle2','track2',{
	range: $R(0,250),
	onSlide:function(v){$('kbps').value=+v,$('kbpm').value=+v*60,$('kbph').value=+v*60*60,$('mbps').value=+v/1024,$('mbpm').value=+v*60/1024,$('mbph').value=+v*60*60/1024},
	onChange:function(v){$('kbps').value=+v,$('kbpm').value=+v*60,$('kbph').value=+v*60*60,$('mbps').value=+v/1024,$('mbpm').value=+v*60/1024,$('mbph').value=+v*60*60/1024}});

	var s_dp = new Control.Slider('handle5','track5',{
	range: $R(5,20),
	values: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],
	onSlide:function(v){$('dp').value=+v},
	onChange:function(v){$('dp').value=+v}});

	var s_dmpp = new Control.Slider('handle3','track3',{
	range: $R(-0.01,5),
	values: [-0.01,0,1,2,3,4,5],
	onSlide:function(v){$('dmpp').value=+v},
	onChange:function(v){$('dmpp').value=+v}});

	var s_dbpp = new Control.Slider('handle4','track4',{
	range: $R(0,200),
	onSlide:function(v){$('dbpp').value=+v},
	onChange:function(v){$('dbpp').value=+v}});

// ]]>
</script>
{/literal}

<script type="text/javascript" language="javascript">
s_mps.setValue({$throttle_MPS});
s_bps.setValue({$throttle_BPS});
s_dp.setValue({$throttle_DP});
s_dbpp.setValue({$throttle_DBPP});
s_dmpp.setValue({$throttle_DMPP});
</script>


</div>
<!-- end mainbar -->
{include file="admin/inc.footer.tpl"}