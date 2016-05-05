<?php
/**
 * @file clay.php
 */

/**
 * Clay Framework
 *
 * @copyright (C) 2007-2014 David L Dyess II
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
function Import($file){
	# Track everything we include
	static $inc = array();
	# $file should include the file path, but not the file extension
	$file = $file.'.php';
	# If we haven't dealt with this file before
	if(!isset($inc[strtolower($file)])){

		if(file_exists($file)){

			include($file);
			# Add to tracker
			$inc[strtolower($file)] = true;
			return true;
		}
		# File doesn't exist, so we let the tracker know
		$inc[strtolower($file)] = false;
		return false;
	}
	# Return true or false
	return $inc[strtolower($file)];
}
/**
 * Import a Library file
 * @param string $path
 * @return boolean
 */
function Library($path){
	# Try to import(). If we can't we throw an exception
	if(!Import(\clay\LIB_PATH.$path)){

		throw new \Exception('Library at '.\clay\LIB_PATH.$path.' doesn\'t exist!');
	}
	return true;
}

/**
 * Clay Runtime
 * @param string $config
 * @todo This is a temporary solution, this should be moved to the \Clay class or \Clay\Core
 */
function Clay($config='default'){

	# Used to show page load times
	$time_start = microtime(true);

	# zlib Settings
	//ini_set('zlib.output_compression', 'on');
	//ini_set('zlib.output_compression_level',-1);

	try {

		# Call the Bootstrap
		# You can set any Configuration name used in the Clay Installer here
		\Clay::Bootstrap($config);

	} catch (Exception $exception) {
		# Need to find a better way to do this...
		# Maybe a fall back app (such as System)?

		echo "<div class=\"app-content\"><h3>Response: ".$exception->getMessage()."</h3></div>
		<p>Exception in File: ".$exception->getFile()." on Line:  ".$exception->getLine()." </p>
		<pre> ".$exception->getTraceAsString()."</pre>";
	}

	$time_end = microtime(true);
	$timer = round($time_end - $time_start,6);
	//echo " <!-- Page created in $timer seconds. --> ";
	//echo " <!-- Peak PHP Memory Usage: ".\memory_get_peak_usage()." bytes --> ";
	//echo " <!-- PHP Shutdown Memory Usage: ".\memory_get_usage()." bytes --> ";
}

/**
 * Clay Base Class
 * @author david
 * @namespace Clay
 */
class Clay {

	/**
	 * Name
	 * @var string
	 */
	const name = 'Clay Framework';

	/**
	 * Version
	 * @var string
	 */
	const version = '0.9.20';

	/**
	 * Build
	 * @var int
	 */
	const build = '1100';

	/**
	 * Codename
	 * @var string
	 */
	const cname = 'Rai';

	/**
	 * Configuration Settings
	 * @var array
	 */
	public static $config = array();

	public static $mode = 'LIVE';

	/**
	 * Fetch data from a configuration file
	 * @param string $config
	 * @return array or false
	 */
	public static function Config($config) {

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
	public static function Bootstrap($config = 'default'){
		# This can be called more than once, so we want to only define Constants the first time:
		static $passes = 0;

		if(empty($passes)){

			define('clay\PATH', dirname(dirname(realpath(__FILE__)) .'/'));
			define('clay\DATA_PATH', \clay\PATH.'/data/');
			define('clay\CFG_PATH', \clay\DATA_PATH.'config/');
			define('clay\LOG_PATH', \clay\DATA_PATH.'log/');
			define('clay\LIB_PATH', \clay\PATH.'/library/');
			define('clay\MODS_PATH', \clay\PATH.'/modules/');
			# Skip this next time.
			$passes = 1;
		}

		if(is_array($config)) {

			static::$config = static::Config('sites/'.$config['conf'].'/config');

			if(!empty(static::$config)){

				static::$config['conf'] = $config['conf'];

			} else {

				static::$config = $config;
			}

		} else {

			static::$config = static::Config('sites/'.$config.'/config');
			static::$config['conf'] = $config; # Save configuration name for reference
		}

		if(!empty(static::$config['init'])){

			$class = static::$config['init'][0];
			if(!\Library($class))throw new \Exception("Custom initialization file requested could not be located - ".$class);
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
	public static function Init(){

		self::Library('Core');
		\clay\core::init(static::$config);
	}

	/**
	 * Output Logic.
	 * @return null
	 */
	public static function Output(){

		self::Library('Core');
		\Clay\Core::Output(static::$config);
	}

	/**
	 * Import a Clay Library file
	 * @param string $path
	 * @return boolean
	 */
	public static function Library($path){

		if(!Import(\clay\LIB_PATH.'Clay/'.$path)){

			throw new \Exception('Clay Library at '.\clay\LIB_PATH.'Clay/'.$path.' doesn\'t exist!');
		}

		return true;
	}

	/**
	 * Redirect the browser to a URL string
	 * @param string $url
	 * @return null
	 */
	public static function Redirect($url){

		session_write_close();
		header( 'Location: '.$url );
	}
}
