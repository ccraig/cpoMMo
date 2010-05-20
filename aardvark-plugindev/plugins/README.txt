/**
 * Copyright (C) 2005, 2006, 2007  Brice Burgess <bhb@iceburg.net>
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

 In order to enable plugins:
 
 -	Enable plugins in the config.php file in the poMMo root.
 	//TODO config file was changed
 	Set the value $useplugins = TRUE;
 	MAYBE put this in the pommo CLASS $pommo->_useplugins = TRUE;
 	IN inc/classes/pommo.php!!!!!!!!!!!!!!
 	// TODO REVISION mechanism from single user pommo
 
 -	Generate TABLES in the database with the script:
 	yourpommourl/plugins/installplugins.php
 	It contains some basic configurations. To edit the configurations use the pluginmanager plugin.
 	All configs should be editeble through the database.
 	
 
 There are some constraints -> e.g. the authentication plugins
 Maybe some constraints will be added in the future.
