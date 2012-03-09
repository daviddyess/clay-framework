<?php
namespace clay\data\validate;
/**
 * Clay Framework
 *
 * Granule Library
 * - Data Validation
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

function int($var,$range){
	if(!empty($range[1])) $options['options']['min_range'] = $range[1];
	if(!empty($range[2])) $options['options']['max_range'] = $range[2];
	if(empty($options)) $options = '';
	return filter_var($var,FILTER_VALIDATE_INT,$options);
}
function num($var,$range){
	if(!is_numeric($var)) return false;
	if(!empty($range[1])) if($var < $range[1]) return false;
	if(!empty($range[2])) if($var > $range[2]) return false;
	return $var;
}
function float($var,$range){
	if(!empty($range[1])) if($var < $range[1]) return false;
	if(!empty($range[2])) if($var > $range[2]) return false;
	return filter_var($var, FILTER_VALIDATE_FLOAT);
}
function object($var){
	if(is_object($var)) return $var;
	return false;
}
function string($var,$range){
	if(!is_string($var)) return false;
	$length = strlen($var);
	if(!empty($range[1])) if($length < $range[1]) return false;
	if(!empty($range[2])) if($length > $range[2]) return false;
	//return filter_var($var,FILTER_SANITIZE_STRING);
	return $var;
}
function scalar($var){
	if(is_scalar($var)) return $var;
	return false;
}
function bool($var){
	return filter_var($var, FILTER_VALIDATE_BOOLEAN);
}
function isarray($var){
	if(is_array($var)) return $var;
	return false;
}
function alnum($var){
	if(ctype_alnum($var)) return $var;
	return false;
}
function alpha($var){
	if(ctype_alpha($var)) return $var;
	return false;
}
function email($var){
	return filter_var($var, FILTER_VALIDATE_EMAIL);
}
function url($var){
	return filter_var($var, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED);
}
/**
 * HTML Validation
 * @desc Determines if a string contains only allowed HTML elements.
 * @param (string) $var - string to validate
 * @param (array) $custom - array of unallowed HTML elements
 * @return (string)$var on success or (bool) false if validation fails
 */
function html($var,$custom=''){
	$tags = array('param','script','style','applet','form','object','embed');
	foreach($tags as $tag){
		//if(preg_match('#</?'.$tag.'[^>]*>#is', '', $var)) return false;
	}
	return $var;
}
?>