<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.tabs.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/ui.tabs.css" />


<script type="text/javascript">
/* TabWizzard JS (c) 2007 Brice Burgess, <bhb@iceburg.net>
	Licensed under the GPL */
	
	// forms with class "mandatory" are force submitted and verified before changing tabs.
	
var PommoTabs = {ldelim}
	tabs: null,
	clicked: false,
	mandatoryForm: false,
	force: false,
	defaults: {ldelim}
		spinner: "{t escape=js}Processing{/t}...",
		{literal}
		ajaxOptions: { async: false }, // make synchronous requests when loading tabs
		click: function(clicked,hide,show) { return PommoTabs.click(clicked,hide,show); },
		load: function(clicked,content) { return PommoTabs.load(content); }
	},
	init: function(e,p) {
		this.tabs = $(e).tabs($.extend(this.defaults,p));
		return this;
	},
	load: function(tab) {
		this.clicked = false;
		this.mandatoryForm = false;
		$('form.json',tab).each(function(){
			var form = poMMo.form.init(this,{type: 'json', onValid: PommoTabs.change});
			if($(this).hasClass('mandatory'))
				PommoTabs.mandatoryForm = form;
		});
	},
	click: function(tab) {
		this.clicked = tab;
		if(this.mandatoryForm && !this.force) {
			this.mandatoryForm.submit(); // onSuccess fires PommoTabs.switch();
			return false;
		}
		this.force = false;
		return true;
	},
	change: function() {
		PommoTabs.force = true;
		if(!PommoTabs.clicked)
			PommoTabs.clicked = $('li a',PommoTabs.tabs)[$(PommoTabs.tabs).tabsSelected()];
		
		$(PommoTabs.clicked).click();
	}
}
</script>
{/literal}