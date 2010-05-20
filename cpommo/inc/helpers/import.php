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


class PommoCSVStream{
   var $position; 
   var $varname; 
   function stream_open($path, $mode, $options, &$opened_path){ 
       $url = parse_url($path); 
       $this->varname = $url['host'] ;
       $this->position = 0; 
       return true;
   }
  function stream_read($count){ 
       $ret = substr($GLOBALS[$this->varname], $this->position, $count); 
       $this->position += strlen($ret); 
       return $ret; 
   }
  function stream_eof(){ 
       return $this->position >= strlen($GLOBALS[$this->varname]); 
   } 
   function stream_tell(){ 
       return $this->position; 
   } 
}

?>