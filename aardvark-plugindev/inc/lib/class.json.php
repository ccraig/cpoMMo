<?php
    /*----------------------------------------------------------------------
        PHP JSON Class
        ==============
        The PHP JSON Class can be used to encode a php array or object into
        Java Script Object Notation, without the need for an additional PHP
        Extension.
        
        Normal usage is as follows:
        
            $json = new json;
            $encoded = $json->encode($var);
            echo $encoded;
                    
        Version 0.5
        Copyright Jack Sleight - www.reallyshiny.com
        This script is licensed under the:
            Creative Commons Attribution-ShareAlike 2.5 License
    ----------------------------------------------------------------------*/

    class json {
    
        /*--------------------------------------------------
            Encode the variable/array/object
        --------------------------------------------------*/
    
        function encode($input) {
        
            $output = $this->get(NULL, $input);
            
            return $output;    
        
        }
        
        /*--------------------------------------------------
            Get the encoded variable
        --------------------------------------------------*/
    
        function get($key, $value, $parent = NULL) {
                
            $type = $this->type($key, $value);
                
            switch ($type) {
            
                case 'object':
                    $value = '{'.$this->loop($value, $type).'}';
                    break;
                case 'array':
                    $value = '['.$this->loop($value, $type).']';
                    break;
                case 'number':
                    $value = $value;
                    break;
                case 'string':
                    $value = '"'.$this->escape($value).'"';
                    break;
                case 'boolean':
                    $value = ($value) ? 'true' : 'false';
                    break;
                case 'null':
                    $value = 'null';
                    break;
            
            }
            
            if(!is_null($key) && $parent != 'array')
                $value = '"'.$key.'":'.$value;
            
            return $value;
            
        }
    
        /*--------------------------------------------------
            Check the type of the variable
        --------------------------------------------------*/
    
        function type($key, $value) {
        
            if(is_object($value)) {
                $type = 'object';
            }
            elseif(is_array($value)) {
                if($this->is_assoc($value))
                    $type = 'object';
                else
                    $type = 'array';                    
            }
            elseif(is_int($value) || is_float($value)) {
                $type = 'number';
            }
            elseif(is_string($value)) {
                $type = 'string';
            }
            elseif(is_bool($value)) {
                $type = 'boolean';
            }
            elseif(is_null($value)) {
                $type = 'null';
            }
            else {
                die($key.' is of an unsupported type.');
            }
            
            return $type;
            
        }
    
        /*--------------------------------------------------
            Loop through the array/object
        --------------------------------------------------*/
    
        function loop($input, $type) {
        
            $output = NULL;
            
            foreach($input as $key => $value) {
                $output .= $this->get($key, $value, $type).',';
            }
            
            $output = trim($output, ',');
            
            return $output;    
        
        }    
    
        /*--------------------------------------------------
            Escape strings
        --------------------------------------------------*/
    
        function escape($string) {

            $find = array('\\',        '"',    '/',    "\b",    "\f",    "\n",    "\r",    "\t",    "\u");
            $repl = array('\\\\',    '\"',    '\/',    '\b',    '\f',    '\n',    '\r',    '\t',    '\u');
            
            $string = str_replace($find, $repl, $string);

            return $string;
        
        }    
    
        /*--------------------------------------------------
            Check if the array is associative
        --------------------------------------------------*/
    
        function is_assoc($array) {

            krsort($array, SORT_STRING);
            return !is_numeric(key($array));
            
        }
    
    }

?>