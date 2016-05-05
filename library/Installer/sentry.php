<?php
namespace installer;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Installer - Sentry Library
 */
/**
 * Sentry Class
 * @desc Responsible to ensuring the current user is authenticated and allowed access to the Installer
 * @author david
 *
 */
class sentry {

	public static $dir = 'sites/installer/';

	public static function security($args){
		if(file_exists(\clay\CFG_PATH.static::$dir.'sentry.php')){
			$conf = \clay::config(static::$dir.'sentry');
			if(!empty($conf['token'])) return true;
	    }
	    return false;
	}

	public static function authenticate(){
		if(file_exists(\clay\CFG_PATH.static::$dir.'sentry.php')){
			$conf = \clay::config(static::$dir.'sentry');
			If(!empty($_SESSION['csi']) && $_SESSION['csi'] == $conf['token']) return true;
		}
		return false;
	}

	public static function initiated(){
		if(file_exists(\clay\CFG_PATH.static::$dir.'sentry.php')){
			return true;
	    }
	    return false;
	}

}