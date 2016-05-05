<?php
namespace installer;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Installer Controller
 */
/**
 * Provides an easy method of calling Application Components
 * @param string $application
 * @param string $component
 * @return string
 */
\Library('Clay/Application');
/**
 * An integrated Controller and View library - this library is the backbone for Applications and Themes
 *
 */
abstract class application extends \clay\application {

	/**
	 *  - Builds the query string for an application url
	 */
	public static function url($com = '',$act = '',$extra = array(),$null = NULL){
		$args = !empty($_GET['s']) ? '?s='.\clay\data\get('s','string','base') : '?';
		if(is_array($com)){
			foreach($com as $piece){
				$args = $args.empty($args) ? '?'.key($com).'='.$piece : '&'.key($com).'='.$piece;
				next($com);
			}
			return $args;
		} else {
			if(!empty($com)){
				$args = $args."&com=$com";
			}
			if(!empty($act)){
				$args = $args."&act=$act";
			}
			$params = '';
			if(is_array($extra)){
				foreach($extra as $piece){
					$params = $params.'&'.key($extra).'='.$piece;
					next($extra);
				}
			}
			return $args.$params;
		}
	}
	/**
	 *  - Redirects the browser to another component url
	 */
	public static function redirect($com = '',$act = '',$extra = array(), $null = NULL){
		session_write_close();
		header( 'Location: '.self::url($com,$act,$extra) );
	}
	public static function package($name) {
		$file = \clay\APPS_PATH.$name.'/package.php';
		if(file_exists($file)){
			include($file);
			return $data;
		} else {
			return false;
		}
	}
	public static function menu(){
		$file = \clay\APPS_PATH.\installer\PACKAGE.'/menu.php';
		if(file_exists($file)){
			include($file);
			return $data;
		} else {
			return false;
		}
	}

}