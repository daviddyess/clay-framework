<?php
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Library
 */
/**
 * Include a file only once.
 * @param string $file
 * @return boolean
 */
function import($file){
	# Track everything we include
	static $inc = array();
	# $file should include the file path, but not the file extension
	$file = $file.'.php';
	# If we haven't dealt with this file before
	if(!isset($inc[$file])){
		if(file_exists($file)){
			include($file);
			# Add to tracker
			$inc[$file] = true;
			return true;
		}
		# File doesn't exist, so we let the tracker know
		$inc[$file] = false;
		return false;
	}
	# Return true or false
	return $inc[$file];
}
/**
 * Import a Library file
 * @param string $path
 * @return boolean
 */
function library($path){
	# Try to import(). If we can't we throw an exception
	if(!import(\clay\LIB_PATH.$path)) throw new \Exception('Library at '.\clay\LIB_PATH.$path.' doesn\'t exist!');
	return true;
}
/**
 * Clay Base Class
 * @author david
 */
class clay {

	const name = 'Clay Framework';
	const version = '0.8.9';
	const build = '682';
	const cname = 'Rai';
	public static $config = array();
	/**
	 * Fetch data from a configuration file
	 * @param string $config
	 * @return array or false
	 */
	public static function config($config) {
		if(file_exists(\clay\CFG_PATH.$config.'.php')){
			include(\clay\CFG_PATH.$config.'.php');
			return $data;
		} else {
			# Exception would be nice here? //throw new Exception('clay::config() was unable to load the following configuration file: '.$config."\nPlease ensure the file exists or check file permissions.");
			return false;
		}
	}
	/**
	 * Set data in a configuration file
	 * @param (string) $config
	 * @param (array) $data
	 * @return boolean
	 */
	public static function setConfig($config,$data) {
		$content = "<?php\n" . '$data = ' . var_export($data,1).";\n ?>";
		$file = fopen(\clay\CFG_PATH.$config.'.php', "w");
		if(fwrite($file, $content)){
			fclose($file);
			return true;
		} else {
			# Exception would be nice here? //throw new Exception('clay::setConfig() was unable to write to '.$config.'. Please check file permissions.');
			return false;
		}
	}
	/**
	 * Retrieves configuration file data, sets system constants [via init()], and initializes output.
	 * @param (string) $config
	 * @return init or callback
	 */
	public static function bootstrap($config = 'default'){
		# This can be called more than once, so we want to only define Constants the first time:
		static $passes = 0;
		if(empty($passes)){
			define('clay\PATH', dirname(dirname(realpath(__FILE__)) .'/'));
			define('clay\DATA_PATH', \clay\PATH.'/data/');
			define('clay\CFG_PATH', \clay\DATA_PATH.'config/');
			define('clay\LIB_PATH', \clay\PATH.'/libraries/');
			# Skip this next time.
			$passes = 1;
		}
		if(is_array($config)) {
			static::$config = static::config('sites/'.$config['conf'].'/config');
			if(!empty(static::$config)){
                            static::$config['conf'] = $config['conf'];
                        }else {
                            static::$config = $config;
                        }
		} else {
			static::$config = static::config('sites/'.$config.'/config');
			static::$config['conf'] = $config; # Save configuration name for reference
		}
		if(!empty(static::$config['init'])){
			$class = static::$config['init'][0];
			if(!\library($class))throw new \Exception("Custom initialization file requested could not be located - ".$class);
			$callback = static::$config['init'][1];
			$class::$callback();
		} else {
			static::init();
			static::output();
		}
	}
	/**
	 * Defines required constants
	 * @return null
	 */
	public static function init(){
		self::library('core');
		\clay\core::init(static::$config);
	}
	/**
	 * Output Logic.
	 * @return null
	 */
	public static function output(){
		self::library('core');
		\clay\core::output(static::$config);
	}
	/**
	 * Import a Clay Library file
	 * @param string $path
	 * @return boolean
	 */
	public static function library($path){
		if(!import(\clay\LIB_PATH.'clay/'.$path)) throw new \Exception('Clay Library at '.\clay\LIB_PATH.'clay/'.$path.' doesn\'t exist!');
		return true;
	}
	/**
	 * Redirect the browser to a URL string
	 * @param string $url
	 * @return null
	 */
	public static function redirect($url){
		session_write_close();
		header( 'Location: '.$url );
	}
}