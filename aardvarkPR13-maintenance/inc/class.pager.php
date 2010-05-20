<?php
// PHASE OUT

/************************************************************************************** 
 * Class: Pager 
 * Author: Tsigo <tsigo@tsiris.com>  /  Modified by Brice Burgess <bhb@iceburg.net>
 * Methods: 
 *         findStart 
 *         findPages 
 *         pageList 
 *         nextPrev 
 * Redistribute as you see fit. 
 **************************************************************************************/
class Pager {

	var $appendUrl;

	function Pager($append = NULL) {
		$this->appendUrl = $append;
		return;
	}

	/*********************************************************************************** 
	 * int findStart (int limit) 
	 * Returns the start offset based on $_GET['page'] and $limit 
	 ***********************************************************************************/
	function findStart($limit) {
		if ((!isset ($_GET['page'])) || ($_GET['page'] == "1")) {
			$start = 0;
			$_GET['page'] = 1;
		} else {
			$start = ($_GET['page'] - 1) * $limit;
		}

		return $start;
	}
	/*********************************************************************************** 
	 * int findPages (int count, int limit) 
	 * Returns the number of pages needed based on a count and a limit 
	 ***********************************************************************************/
	function findPages($count, $limit) {
		$pages = (($count % $limit) == 0) ? $count / $limit : floor($count / $limit) + 1;

		return $pages;
	}
	/*********************************************************************************** 
	 * string pageList (int curpage, int pages) 
	 * Returns a list of pages in the format of "´ < [pages] > ª" 
	 ***********************************************************************************/
	function pageList($curpage, $pages) {
		$page_list = '';
		if ($pages > 1) { // no reason to show if you can't click between more than one page
			$page_list = '<div class="page_list">'."\n";

			/* Print the first and previous page links if necessary */
			if (($curpage != 1) && ($curpage)) {
				//$page_list .= " <a href=\"".$_SERVER['PHP_SELF']."?page=1\"".htmlentities($this->appendUrl)."title=\"First Page\">´</a> ";
				$page_list .= '<a href="'.$_SERVER['PHP_SELF'].'?page=1'.htmlentities($this->appendUrl).'" class="first" title="First Page">&#171;</a>'."\n";
			}

			if (($curpage -1) > 0) {
				//$page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=". ($curpage -1)."\" title=\"Previous Page\"><</a> ";
				$page_list .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.($curpage -1).htmlentities($this->appendUrl).'" class="prev" title="Previous Page">&lt;</a>'."\n";
			}

			/* Print the numeric page list; make the current page unlinked and bold */
			for ($i = 1; $i <= $pages; $i ++) {
				if ($i == $curpage) {
					$page_list .= '<strong>'.$i.'</strong>'."\n";
				} else {
					$page_list .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.$i.htmlentities($this->appendUrl).'" title="Page '.$i.'">'.$i.'</a>'."\n";
				}
			}

			/* Print the Next and Last page links if necessary */
			if (($curpage +1) <= $pages) {
				//$page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=". ($curpage +1).htmlentities($this->appendUrl)."\" title=\"Next Page\">></a> ";
				$page_list .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.($curpage +1).htmlentities($this->appendUrl).'" class="next" title="Next Page">&gt;</a>'."\n";
			}

			if (($curpage != $pages) && ($pages != 0)) {
				//$page_list .= "<a href=\"".$_SERVER['PHP_SELF']."?page=".$pages.htmlentities($this->appendUrl)."\" title=\"Last Page\">ª</a> ";
				$page_list .= '<a href="'.$_SERVER['PHP_SELF'].'?page='.$pages.htmlentities($this->appendUrl).'" class="last" title="Last Page">&#187;</a>'."\n";
			}
				$page_list .= '</div>'."\n";
		}
		return $page_list;
	}
	/*********************************************************************************** 
	 * string nextPrev (int curpage, int pages) 
	 * Returns "Previous | Next" string for individual pagination (it's a word!) 
	 ***********************************************************************************/
	function nextPrev($curpage, $pages) {
		$next_prev = "";

		if (($curpage -1) <= 0) {
			$next_prev .= "Previous";
		} else {
			$next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."?page=". ($curpage -1).$this->appendUrl."\">Previous</a>";
		}

		$next_prev .= " | ";

		if (($curpage +1) > $pages) {
			$next_prev .= "Next";
		} else {
			$next_prev .= "<a href=\"".$_SERVER['PHP_SELF']."?page=". ($curpage +1).$this->appendUrl."\">Next</a>";
		}

		return $next_prev;
	}
}
?>