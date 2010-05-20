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
 
 // Generates a simple HTML form based from active subscriber criteria
 
 define('_IS_VALID', TRUE);
 
 require('../../bootstrap.php');
 require_once(bm_baseDir.'/inc/db_fields.php');
 $poMMo =& fireup("secure");
 $dbo = & $poMMo->_dbo;

 
// URL which processes the form input + adds (or warns) subscriber to pending table.
$signup_url = "http://" . $_SERVER['HTTP_HOST'] . bm_baseUrl . "user/process.php";
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Sample form</title>
</head>

<?php
$form_name = "signup";
?>
<body>

<p><em>VIEW THE SOURCE TO COPY, PASTE, EDIT, AND SAVE THE FORM TO AN APPROPRIATE LOCATION ON YOUR WEBSITE</em></p>

<hr>

<h1><?php echo $poMMo->_config['list_name']; ?> Subscriber Form</h1>

<!-- 	Set "ACTION" to the URL of poMMo's process.php
		process.php located in the "user" directory of your poMMo installation.
		** poMMo attempted to detect this location, and it may not need to be changed. ** -->
		
<form method="post" action="<?php echo $signup_url; ?>"name="<?php echo $form_name; ?>">

<p><em>Fields in <strong>bold</strong> are required.</em></p>

<div>
<!--	Email field must be named "bm_email" -->
<label for="email"><strong>Your Email:</strong></label>
<input type="text" name="bm_email" id="email" maxlength="60">
</div>

<?php

$fields = & dbGetFields($dbo, 'active');
foreach (array_keys($fields) as $field_id) {
	$field = & $fields[$field_id];
	
	if ($field['required'] == 'on')
		echo "\n<div>\n<!-- BEGIN INPUT FOR REQUIRED FIELD ".$field['name']." -->\n<label for=\"field".$field_id."\"><strong>".db2str($field['prompt']).":</strong></label>\n";
	else
		echo "\n<div>\n<!-- BEGIN INPUT FOR FIELD ".$field['name']." -->\n<label for=\"field".$field_id."\">".db2str($field['prompt']).":</label>\n";
	
	switch ($field['type']) {
		case "checkbox": // checkbox	
			if (empty($field['normally']))
				echo "\n<input type=\"checkbox\" name=\"d[".$field_id."]\" id=\"field".$field_id."\">";
			else
				echo "\n<input type=\"checkbox\" name=\"d[".$field_id."]\" id=\"field".$field_id."\" checked>";
			break;
			
		case "multiple": // select
		
			echo "\n<select name=\"d[".$field_id."]\" id=\"field".$field_id."\">\n";
			
			echo "<option value=\"\"> Please Choose...</option>\n";
			
			foreach ($field['options'] as $option) {
				
				if (!empty($field['normally']) && $option == $field['normally'])
					echo "<option value=\"".db2str($option)."\" selected> ".db2str($option)."</option>\n";
				else
					echo "<option value=\"".db2str($option)."\"> ".db2str($option)."</option>\n";
			}			
			echo "</select>\n";
					
			break;
			
		case "text": // select
		case "number": // select
		case "date": // select
		
			if (empty($field['normally']))
				echo "<input type=\"text\" name=\"d[".$field_id."]\" id=\"field".$field_id."\" maxlength=\"60\">\n";
			else
				echo "<input type=\"text\" name=\"d[".$field_id."]\" maxlength=\"60\" value=\"".db2str($field['normally'])."\">\n";
			break; 

		default:
			break;
	}

	echo "\n</div>\n";
}

?>

<!--  *** DO NOT CHANGE name="pommo_signup" ! ***
	  If you'd like to change the button text change the "value=" text. -->

<input type="hidden" name="pommo_signup" value="true">
<input type="submit" name="submit" value="Signup">
<input type="reset" name="reset">

</form>

</body>
</html>