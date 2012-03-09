<?php
namespace clay\data;
/**
 * Clay Framework
 *
 * Granule Library
 * - Data Handler
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

	\library('clay/data/validate');
	\library('clay/data/filter');
	class cache {
		private static $cache = array();
		/**
		 * Get a cached value
		 * @param mixed $name
		 * @param mixed $key optional
		 * @return cached $value
		 */
		public static function get($name,$key = '',$default = ''){
			if(!empty($key)){
				if(self::isCached($name,$key)){
					return self::$cache[$name][$key];
				}
			} else {
				if(self::isCached($name)){
					return self::$cache[$name];
				}
			}
			if(!empty($default)) return $default;
		}
		/**
		 * Cache name with key and a value
		 * @return mixed $value
		 */
		public static function set($name,$key,$value){
			self::$cache[$name][$key] = $value;
			return self::$cache[$name][$key];
		}
		/**
		 * Cache a key only (not an array)
		 * @return mixed $value
		 */
		public static function setKey($key,$value){
			self::$cache[$key] = $value;
			return self::$cache[$key];
		}
		/**
		 * Cache an array
		 * @param mixed $name
		 * @param array $keyvalues
		 * @return array $keyvalues
		 */
		public static function setArray($name,$keyvalues){
			if(is_array($keyvalues)){
				self::$cache[$name] = $keyvalues;
				return self::$cache[$name];
			} else {
				die('setCachedArray() requires an array for argument #2');
			}
		}
		/**
		 * Delete name or name,key
		 * @param mixed $name
		 * @param mixed $key optional
		 * @return true
		 */
		public static function delete($name,$key = ''){
			if(!empty($key) && self::isCached($name,$key)){
				unset(self::$cache[$name][$key]);
				return true;
			}
			if(self::isCached($name) && empty($key)){
				unset(self::$cache[$name]);
				return true;
			}
			return true;
		}
		/**
		 * Find out if either name or name,key is cached
		 * @param $name
		 * @param $key optional
		 * @return true/false
		 */
		public static function isCached($name,$key = ''){
			if(empty($key)){
				if(!empty(self::$cache[$name])){
					return true;
				}
				return false;
			} else {
				if(!empty(self::$cache[$name][$key])){
					return true;
				}
				return false;
			}
		}
		/**
		 * Dump the entire cache::$cache array. Useful for debugging.
		 * @return array
		 */
		public static function dump(){
			return self::$cache;
		}
	}

	# $_POST and $_GET handling #

	/**
	 * Fetches POST or GET data, favoring POST
	 * @return /clay/data/process() or false
	 */
	function fetch($index,$val='',$filter='',$default=''){
		if(isset($_POST[$index])) return process($_POST[$index],$val,$filter,$default);
		if(isset($_GET[$index])) return process($_GET[$index],$val,$filter,$default);
		if(!empty($default)) return $default;
		return false;
	}
	/**
	 * Fetches POST data
	 * @return /clay/dat/process() or false
	 */
	function post($index,$val='',$filter='',$default=''){
		if(isset($_POST[$index])) return process($_POST[$index],$val,$filter,$default);
		if(!empty($default)) return $default;
		return false;
	}
	/**
	 * Fetches GET data
	 * @return /clay/data/process() or false
	 */
	function get($index,$val='',$filter='',$default=''){
		if(isset($_GET[$index])) return process($_GET[$index],$val,$filter,$default);
		if(!empty($default)) return $default;
		return false;
	}

	# Data processing (Filter and Validate) #

	/**
	 * Process data for Output
	 * @return mixed
	 */
	function process($var,$val='',$filter='',$default=''){
		if(!empty($filter)){
			$filters = explode(' | ',$filter);
			foreach($filters as $filter){
				$ns = '\clay\data\filter\\'.$filter;
				$var = $ns($var);
			}
		}
		if(!empty($val)){
			$_val = explode(':',$val);
			$ns = '\clay\data\validate\\'.$_val[0];
			$var = $ns($var,$_val);
		}
		if(empty($var)){
			if(!empty($default)) return $default;
		}
		return $var;
	}
	function validate($val,$var){
		$_val = explode(':',$val);
		$ns = '\clay\data\validate\\'.$_val[0];
		$var = $ns($var,$_val);
		return $var;
	}
	function filter($filter,$var){
		$filters = explode(' | ',$filter);
		foreach($filters as $filter){
			$ns = '\clay\data\filter\\'.$filter;
			$var = $ns($var);
		}
		return $var;
	}
?>