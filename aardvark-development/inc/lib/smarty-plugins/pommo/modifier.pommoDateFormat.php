<?php

function smarty_modifier_pommoDateFormat($int)
{
	return PommoHelper::timeToStr($int);
}

?>
