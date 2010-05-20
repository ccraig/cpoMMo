{* $pagerPrev and $pagerNext are assigned in PommoTemplate::addPager() (inc/classes/template.php)
   text is wrapped through translation function *} 

<div class="pager">
{paginate_first text="&laquo;"}
{paginate_prev text=$pagerPrev}
{paginate_middle page_limit="8" prefix="[" suffix="]" link_prefix=" " link_suffix=" "}
{paginate_next text=$pagerNext}
{paginate_last text="&raquo;"}
</div>