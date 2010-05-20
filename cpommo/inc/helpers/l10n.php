<?php
/**
 * Copyright (C) 2005, 2006, 2007, 2008  Brice Burgess <bhb@iceburg.net>
 * 
 * This file is part of poMMo (http://www.pommo.org)
 * 
 * poMMo is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published 
 * by the Free Software Foundation; either version 2, or any later version.
 * 
 * poMMo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with program; see the file docs/LICENSE. If not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */

class PommoHelperL10n {
	function init($language, $baseDir) {

		if (!is_file($baseDir . 'language/' . $language . '/LC_MESSAGES/pommo.mo'))
			Pommo::kill('Unknown Language (' .$language . ')');
			
		// if LC_MESSAGES is not available.. make it (helpful for win32)
		if (!defined('LC_MESSAGES')) define('LC_MESSAGES', 6);

		// load gettext emulation layer if PHP is not compiled w/ gettext support
		if (!function_exists('gettext')) {
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.php');
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.inc');
		}
		
		// set the locale
		if (!PommoHelperL10n::_setLocale(LC_MESSAGES, $language, $baseDir)) {
			
			// *** SYSTEM LOCALE COULD NOT BE USED, USE EMULTATION ****
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.php');
			Pommo::requireOnce($baseDir.'inc/lib/gettext/gettext.inc');
			if (!PommoHelperL10n::_setLocaleEmu(LC_MESSAGES, $language, $baseDir))
				Pommo::kill('Error setting up language translation!');
		}
		else {
		
			// *** SYSTEM LOCALE WAS USED ***
			if (!defined('_poMMo_gettext')) {	
				// set gettext environment
				$domain = 'pommo';
				bindtextdomain($domain, $baseDir . 'language');
				textdomain($domain);
				if (function_exists('bind_textdomain_codeset'))
					bind_textdomain_codeset($domain, 'UTF-8');
			}
		}
	}
	
	function _setlocaleEmu($category, $locale, $baseDir) {
		$domain = 'pommo';
		$encoding = 'UTF-8';

		T_setlocale($category, $locale);
		T_bindtextdomain($domain, $baseDir . '/language');
		T_bind_textdomain_codeset($domain, $encoding);
		T_textdomain($domain);
		
		return true;
	}

	// setlocale modified from from Gallery2
	function _setlocale($category, $locale, $baseDir) {
		
		if (defined('_poMMo_gettext'))
			return PommoHelperL10n::_setLocaleEmu($category, $locale, $baseDir);
		
		// append _LC to locale
		if (!strpos($locale,'_')) {
			$locale = $locale.'_'.strtoupper($locale);
		}
		
		if (($ret = setlocale($category, $locale)) !== false) {
			return $ret;
		}
		/* Try just selecting the language */
		if (($i = strpos($locale, '_')) !== false && ($ret = setlocale($category, substr($locale, 0, $i))) !== false) {
			return $ret;
		}
		/*
		 * Try appending some character set names; some systems (like FreeBSD) need this.
		 * Some require a format with hyphen (e.g. gentoo) and others without (e.g. FreeBSD).
		 */
		foreach (array (
				'UTF-8',
				'UTF8',
				'utf8',
				'ISO8859-1',
				'ISO8859-2',
				'ISO8859-5',
				'ISO8859-7',
				'ISO8859-9',
				'ISO-8859-1',
				'ISO-8859-2',
				'ISO-8859-5',
				'ISO-8859-7',
				'ISO-8859-9',
				'EUC',
				'Big5'
			) as $charset) {
			if (($ret = setlocale($category, $locale . '.' . $charset)) !== false) {
				return $ret;
			}
		}
		return false;
	}

	function translate($msg) {
		if (defined('_poMMo_gettext'))
			return T_($msg);
		return gettext($msg);
	}

	function translatePlural($msg, $plural, $count) {
		if (defined('_poMMo_gettext'))
			return T_ngettext($msg, $plural, $count);
		return ngettext($msg, $plural, $count);
	}

}
?>
