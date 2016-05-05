<?php
namespace Clay\Data\Validate;
/**
 * Clay Framework
 *
 * Data Validation Library
 * - Data Validation
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Validate Integer
 * @param string $var
 * @param string $range
 * @return boolean
 */
function Int($var,$range){
	
	if(!empty($range[1])){
		
		$options['options']['min_range'] = $range[1];
	}
	
	if(!empty($range[2])){
		
		$options['options']['max_range'] = $range[2];
	}
	
	if(empty($options)){
		
		$options = '';
	}
	
	return filter_var($var,FILTER_VALIDATE_INT,$options);
}

/**
 * Validate Numeric
 * @param string $var
 * @param string $range
 * @return boolean|numeric
 */
function Num($var,$range){
	
	if(!is_numeric($var)){
		
		return false;
	}
	
	if(!empty($range[1])){
		
		if($var < $range[1]) return false;
	}
	
	if(!empty($range[2])){
		
		if($var > $range[2]) return false;
	}
	
	return $var;
}

/**
 * Validate Float
 * @param string $var
 * @param string $range
 * @return boolean|float
 */
function Float($var,$range){
	
	if(!empty($range[1])){
		
		if($var < $range[1]) return false;
	}
	
	if(!empty($range[2])){
		
		if($var > $range[2]) return false;
	}
	
	return filter_var($var, FILTER_VALIDATE_FLOAT);
}

/**
 * Validate Object
 * @param string $var
 * @return object|boolean
 */
function Object($var){
	
	if(is_object($var)){
		
		return $var;
	}
	
	return false;
}

/**
 * Validate String
 * @param string $var
 * @param string $range
 * @return boolean|string
 */
function String($var,$range){
	
	if(!is_string($var)){
		
		return false;
	}
	
	$length = strlen($var);
	if(!empty($range[1])){
		
		if($length < $range[1]) return false;
	}
	if(!empty($range[2])){
		
		if($length > $range[2]) return false;
	}

	return $var;
}

/**
 * Validate Scalar
 * @param string $var
 * @return scalar|boolean
 */
function Scalar($var){
	
	if(is_scalar($var)){
		
		return $var;
	}
	
	return false;
}

/**
 * Validate Boolean
 * @param string $var
 * @return boolean
 */
function Bool($var){
	
	return filter_var($var, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Validate Array
 * @param array $var
 * @return array|boolean
 */
function isArray($var){
	
	if(is_array($var)){
		
		return $var;
	}
	
	return false;
}

/**
 * Validate Alphanumeric
 * @param string $var
 * @return string|boolean
 */
function Alnum($var){
	
	if(ctype_alnum($var)){
		
		return $var;
	}
	
	return false;
}

/**
 * Validate Alphabetic
 * @param string $var
 * @return string|boolean
 */
function Alpha($var){
	
	if(ctype_alpha($var)){
		
		return $var;
	}
	
	return false;
}

/**
 * Validate Email Address
 * @param string $var
 * @return string|boolean
 */
function Email($var){
	
	return filter_var($var, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate URL Address
 * @param string $var
 * @return string|boolean
 */
function URL($var){
	
	return filter_var($var, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED);
}

/**
 * Validate HTML
 * @todo This obviously isn't operational
 * @desc Determines if a string contains only allowed HTML elements.
 * @param (string) $var - string to validate
 * @param (array) $custom - array of unallowed HTML elements
 * @return (string)$var on success or (bool) false if validation fails
 */
function HTML($var,$custom=''){
	
	$tags = array('param','script','style','applet','form','object','embed');
	foreach($tags as $tag){
		//if(preg_match('#</?'.$tag.'[^>]*>#is', '', $var)) return false;
	}
	return $var;
}
?>