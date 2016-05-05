<?php
namespace Clay\Data\Filter;
/**
 * Clay Framework
 *
 * Data Filter Library
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Data Filters
 */

/**
 * Allow only Integers
 * @param string $var
 * @return mixed
 */
function Int($var){
	
	return filter_var($var,FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Allow only Alphabetic
 * @param string $var
 */
function Alpha($var){
	
	return preg_replace('/[^[:alpha:]]/', '', $var);
}

/**
 * Allow only Alpha-numeric
 * @param string $var
 */
function Alnum($var){
	
	return preg_replace('/[^[:alnum:]]/', '', $var);
}

/**
 * Allow only Numeric
 * @param string $var
 */
function Num($var){
	
	return preg_replace('/[^\d]/', '', $var);
}

/**
 * Allow only Float
 * @param string $var
 */
function Float($var){
	
	return filter_var($var,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
}

/**
 * Allow only String
 * @param string $var
 * @return mixed
 */
function String($var){
	
	return filter_var($var,FILTER_SANITIZE_STRING);
}

/**
 * Allow only Text
 * @param string $var
 */
function Text($var){
	
	return filter_var($var,FILTER_SANITIZE_STRING);
}

/**
 * Allow only Valid Email
 * @param string $var
 * @return mixed
 */
function Email($var){
	
	return filter_var($var,FILTER_SANITIZE_EMAIL);
}

/**
 * Allow only Valid URL
 * @param string $var
 * @return mixed
 */
function URL($var){
	
	return filter_var($var, FILTER_SANITIZE_URL);
}

/**
 * Allow only PHP File Name, No PHP Extension
 * @param unknown_type $var
 * @param unknown_type $ext
 * @return string
 */
function Base($var,$ext='.*'){
	
	return basename($var, '.php');
}

/*public static function regex($var,$callback){
	$options['callback'] = $callback;
	if($val = filter_input(INPUT_POST | INPUT_GET,$var,FILTER_VALIDATE_REGEXP,$flags))
		return $val;
}*/

/**
 * Strip Tags
 * @param string $var
 * @return string
 */
function noTags($var){
	
	return strip_tags($var);
}

/**
 * Allow only Specified HTML Tags
 * @param string $var
 * @param string $custom (optional)
 */
function HTML($var,$custom=''){
	
	\Library('Clay/Data/Filter/HTML');

	if(\Clay\Data\Cache::isCached('data','filter.html')){
		
		$tags = \Clay\Data\Cache::Get('data','filter.html');
		
	} else {
		
		$tags = \Clay\Data\Cache::Set('data','filter.html', \Clay::Config('sites/'. \clay\CFG_NAME .'/html'));
	}

	if(\Clay\Data\Cache::isCached('objects','filter.html')){
		
		$sa = \Clay\Data\Cache::Get('objects','filter.html');
		
	} else {
		
		$sa = \Clay\Data\Cache::Set('objects','filter.html',new \Clay\Data\Filter\HTML());
	}

	$sa->exceptions = $tags;

	$sa->tags = '';
	
	foreach($tags as $tag => $att){
		
		$sa->tags = $sa->tags.'<'.$tag.'>';
	}
	
	unset($tags);
	return $sa->strip($var);
}

/**
 * Encode Special Characters to HTML Characters
 * @param string $var
 * @return string
 */
function HTMLEncode($var){
	
	return htmlspecialchars($var);
}