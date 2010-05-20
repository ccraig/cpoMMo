<?php
/** [BEGIN HEADER] **
 * COPYRIGHT: (c) 2005 Brice Burgess / All Rights Reserved    
 * LICENSE: http://www.gnu.org/copyleft.html GNU/GPL 
 * AUTHOR: Brice Burgess <bhb@iceburg.net>
 * SOURCE: http://pommo.sourceforge.net/
 *
 *  :: RESTRICTIONS ::
 *  1. This header must accompany all portions of code contained within.
 *  2. You must notify the above author of modifications to contents within.
 * 
 ** [END HEADER]**/

/**********************************
	INITIALIZATION METHODS
 *********************************/
require ('bootstrap.php');
Pommo::requireOnce($pommo->_baseDir.'inc/helpers/subscribers.php');

$pommo->init(array('authLevel' => 0));
$logger = & $pommo->_logger;
$dbo = & $pommo->_dbo;

// quick'n'dirty creation of sample data
$count = 5000; // # of subscribers to insert
$fieldCount = 6; // @ # of required fields
$clearTable = false; // clear the subscriber, pending, and data tables first?

$pendingCount = intval($count*4/100); // 4% of subscribers are pending
$unsubscribeCount = intval($count*10/100); // 10% of subscribers are inactive (unsubscribed)


if($clearTable) {
	$query = "DELETE FROM ".$dbo->table['subscribers'];
	$dbo->query($query);
	$query = "DELETE FROM ".$dbo->table['subscriber_data'];
	$dbo->query($query);
	$query = "DELETE FROM ".$dbo->table['subscriber_pending'];
	$dbo->query($query);
}

for ($i = 0; $i < $count; $i++) {

	$email = 
	$subscriber = array(
		'email' => makeEmail(),
		'touched' => makeTime(),
		'registered' => makeTime(),
		'flag' => makeFlag(),
		'ip' => makeIP(),
		'status' => 1
	);
	
	if ($i < $pendingCount) {
		$subscriber['status'] = 2;
		$subscriber['pending_code'] = md5(md5(rand()).rand());
		
		if (rand(1,100) < 10)
			$subscriber['pending_array'] = array('email' => makeEmail());
			
		$subscriber['pending_type'] = 'change';
		
		$subscriber = PommoSubscriber::make($subscriber,true);
	}
	else {
		if ($i < ($pendingCount + $unsubscribeCount))
			$subscriber['status'] = 0;
		$subscriber = PommoSubscriber::make($subscriber);
	}
	
	$subscriber['data'] = makeData($fieldCount);
	
	if(!PommoSubscriber::add($subscriber)) {
		$logger->addErr('error adding subscriber');
	}
}

function makeData($count) {
	
	$words = array('Lemur','From','Wikipedia','the','free','encyclopedia','Jump','to','navigation','search','Lemurs','1','Cheirogaleoidea','Scientific','classification','Kingdom','Animalia','Phylum','Chordata','Class','Mammalia','Order','Primates','Suborder','Strepsirrhini','Infraorder','Lemuriformes','Gray','1821','Superfamilies','and','Families','o','Cheirogaleidae','Lemuroidea','Lemuridae','Lepilemuridae','Indriidae','are','members','of','a','class','primates','known','as','prosimians','make','up','infraorder','This',
'type','primate','is','considered','evolutionary','predecessor','monkeys','apes','simians','The','term','lemur','derived','from','Latin','word','lemures','which','means','spirits','night','likely','refers','many','nocturnal','species','their','large','reflective','eyes','generically','used','for','four','lemuriform','families','but','it','also','genus','one','two','flying','not','lemurs','nor','they','even','Contents','hide','Biology',
'Endangered','2','Classification','3','References','4','External','links','edit','found','naturally','only','on','island','Madagascar','some','smaller','surrounding','islands','including','Comoros','where','were','introduced','by','humans','Fossil','evidence','indicates','that','made','way','across','ocean','after','broke','away','continent','Africa','While','ancestors','displaced','in','rest','world','other','safe','competition','differentiated','into','number',
'These','range','size','tiny','30','gram','Pygmy','Mouse','10','kilogram','Indri','larger','have','all','become','extinct','since','settled','early','20th','century','largest','reach','about','7','kilograms','Typically','active','at','while','ones','during','day','diurnal','small','cheirogaleoids','generally','omnivores','eating','variety','fruits','flowers','leaves','sometimes','nectar','well','insects','spiders','vertebrates','remainder','lemuroids',
'primarily','herbivores','although','supplement','diet','with','All','endangered','or','threatened','due','mainly','habitat','destruction','deforestation','hunting','Although','conservation','efforts','under','options','limited','because','desperately','poor','Currently','there','82','living','accounted','current','publications','5','more','currently','awaiting','publication','In','remote','areas','cultural','motivation','behind','posting','traps','indigenous','superstition','omens','harbingers','bad','fortune',
'hindsight','commonly','inspired','s','unique','features','One','foremost','research','facilities','Duke','University','Center','Thermographic','image','ringtailed','morning','sun','Enlarge','As','shown','here','split','superfamilies','pedal','structure','similar','strepsirrhine','haplorrhines','suggesting','off','first','such','sister','clade','ORDER','PRIMATES','non','tarsier','Superfamily','Family','dwarf','mouse','sportive','woolly','allies','Chiromyiformes','Lorisiformes','Haplorrhini','tarsiers','b',
'Groves','Colin','16','November','2005','Wilson','D','E','Reeder','M','eds','Mammal','Species','World','3rd','edition','Johns','Hopkins','Press','111','121','ISBN','0','801','88221','What','A','Retrieved','2006','04','19','Mittermeier','Russell','Konstant','William','R','Hawkins','Frank','Louis','Edward','Langrand','Olivier','2nd','Conservation','International','29','Andriaholinirina','N','Fausser','J','Roos',
'C','Rumpler','Y','et','al','February','23','Molecular','phylogeny','taxonomic','revision','Lepilemur','BMC','Evolutionary','6','17','DOI','1186','1471','2148','Jr','Shannon','Engberg','Runhua','Lei','Huimin','Geng','Julie','Sommer','Richard','Randriamampionona','Jean','Randriamanana','John','Zaonarivelo','Rambinintsoa','Andriantompohavana','Gisele','Randria','Prosper','Borom','Ramaromilanto','Gilbert','Rakotoarisoa','Alejandro','Rooney','Rick','Brenneman','morphological','analyses','Megaladapidae',
'Genus','reveals','11','previously','unrecognized','PDF','Texas','Tech','Special','Publications','49','Wikimedia','Commons','has','media','related','Wikispecies','information','Lots','photographs','programs','List','Books','Rare','indri','born','forest','reserve','East','Coast','New','z');

	

	$data = array();
	
	for($i = 0; $i < $count; $i++) {
		$key = rand(0,(count($words)-2));
		$value = $words[$key];
		if ((rand(1,100)) < 36) // 35% of the time add to value..
			$value .= ' '.$words[rand(0,(count($words)+1))];
		$data[$i] = $value;
	}
	
	return $data;
}

function makeIP() {
	return ''.rand(10,254).'.'.rand(1,254).'.'.rand(1,254).'.'.rand(1,254);	
}

// flag the subscriber 8 % of the time
function makeFlag() {
	return (mt_rand(1,100) < 9) ? 9 : null;
}

// from http://www.php.net comment
function makeTime($time = "" , $time2 = "")
{
   if(!$time) $time = strtotime("10 September 1995");
   if(!$time2) $time2 = strtotime("11 November 2006");
   $timestamp = date(" D, d M Y", rand( settype($time , int) , settype($time2 , int) )); //Must be called once before becoming random, ???
   $timestamp = date(" D, d M Y", rand($time , $time2))." ";//Now it's random
  
   $h = rand(1,23);
   if(strlen($h) == 1 ) $h = "0$h";
   $t = $h.":";
  
   $d = rand(1,29);
   if(strlen($d) == 1 ) $d = "0$d";
   $t .= $d.":";
  
   $s = rand(0,59);
   if(strlen($s) == 1 ) $s = "0$s";
   $t .= $s;
  
   $timestamp .= $t." GMT";
   
   $timestamp = strtotime($timestamp);
   return $timestamp;
}

// taken @ http://www.laughing-buddha.net/jon/php/spamtrap/
function makeEmail() {
	$tlds = array("com","net","gov");
	$char = "0123456789abcdefghijklmnopqrstuvwxyz";
	$ulen = mt_rand(3, 8);
  	$dlen = mt_rand(6, 15);
  	
	for ($i = 1; $i <= $ulen; $i++) 
    	$a .= substr($char, mt_rand(0, strlen($char)), 1);
  	$a .= "@";

  	for ($i = 1; $i <= $dlen; $i++) 
    	$a .= substr($char, mt_rand(0, strlen($char)), 1);
    	
 	$a .= ".";
  	$a .= $tlds[mt_rand(0, (sizeof($tlds) - 1))];
  	return $a;
  
}

?>
