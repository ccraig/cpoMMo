<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.mouse.js"></script>
<script type="text/javascript" src="{$url.theme.shared}js/jq/ui.slider.js"></script>
<link type="text/css" rel="stylesheet" href="{$url.theme.shared}css/ui.slider.css" />

{literal}
<script type="text/javascript">
var PommoSlider = {
	serial: 0,
	hash: [],
	defaults: {
		minValue: 0,
		maxValue: 100,
		startValue: 50,
		slide: function(e,ui) {
			PommoSlider.onSlide($(e.target).parent()[0],ui.value)
		}
		// handle: '.ui-slider-handle',
		// stepping: int, [must be divisible by]
		// steps: int, [# of steps, replaces stepping]
		// stop: function(e,ui),
		// start: function(e,ui),
		// change: function(e,ui)
	},
	init: function(e,p) {
		var p = $.extend(PommoSlider.defaults,p);
		return $(e).each(function(){
			var s = this.pommoSlider || PommoSlider.serial++;

			this.pommoSlider = s;
		
			PommoSlider.hash[s] = {
				params: p,
				value: null
			};
			$(this).slider(p);
		});	
	},
	onSlide: function(slider,value) {
		alert('no onSlide event assigned');
	}
};
</script>
{/literal}