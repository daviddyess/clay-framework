<?php
namespace clay\data\filter;
/**
 * Clay Framework
 *
 * Granule Library
 * - Data Filter
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

function int($var){
	return filter_var($var,FILTER_SANITIZE_NUMBER_INT);
}
function alpha($var){
	return preg_replace('/[^[:alpha:]]/', '', $var);
}
function alnum($var){
	return preg_replace('/[^[:alnum:]]/', '', $var);
}
function num($var){
	return preg_replace('/[^\d]/', '', $var);
}
function float($var){
	return filter_var($var,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
}
function string($var){
	return filter_var($var,FILTER_SANITIZE_STRING);
}
function text($var){
	return filter_var($var,FILTER_SANITIZE_STRING);
}
function email($var){
	return filter_var($var,FILTER_SANITIZE_EMAIL);
}
function url($var){
	return filter_var($var, FILTER_SANITIZE_URL);
}
function base($var,$ext='.*'){
	return basename($var, '.php');
}
/*public static function regex($var,$callback){
	$options['callback'] = $callback;
	if($val = filter_input(INPUT_POST | INPUT_GET,$var,FILTER_VALIDATE_REGEXP,$flags))
		return $val;
}*/
function noTags($var){
	return strip_tags($var);
}
function html($var,$custom=''){
	\library('clay/data/filter/html');
	/*$tags = array('attributes' => array('id','class'),
					'elements' => array('img' => array('src','alt'), 'a' => array('href','title')),
					'ignore' => array(),
					'strip' => array('param','script','style','applet','form','object','embed','head','noframes','noscript','noembed'));*/
	if(\clay\data\cache::isCached('data','filter.html')){
		$tags = \clay\data\cache::get('data','filter.html');
	} else {
		$tags = \clay\data\cache::set('data','filter.html', \clay::config('sites/'. \clay\CFG_NAME .'/html'));
	}
	#die(var_dump($tags));
	if(\clay\data\cache::isCached('objects','filter.html')){
		$sa = \clay\data\cache::get('objects','filter.html');
	} else {
		$sa = \clay\data\cache::set('objects','filter.html',new html());
	}

	$sa->exceptions = $tags;
//die(var_dump($sa->exceptions));
	$sa->tags = '';
	foreach($tags as $tag => $att){
		$sa->tags = $sa->tags.'<'.$tag.'>';
	}
	unset($tags);
	//$str = $sa->strip($var);
	//unset($sa);
	//return $str;
	
	return $sa->strip($var);
}
function htmlencode($var){
	return htmlspecialchars($var);
}
?>