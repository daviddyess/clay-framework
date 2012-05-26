<?php
namespace clay;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Styles Manager
 */
/**
 * Provides a means of dynamically linking or including stylesheets from Application and Themes, as well as external URLs.
 */
class styles {

	public static $mode = 'external';
	public static $map = array();

	private static function pattern($args){
		return $args['type'].':'.$args['name'].':'.$args['file'];
	}
	public static function add($args){
		$pattern = self::pattern($args);
		if(!isset(self::$map['styles'][$pattern]['req'])){
			self::$map['styles'][$pattern]['req'] = $args;
		}
	}
	public static function addTheme($name,$file){
		self::add(array('type' => 'theme', 'name' => $name, 'file' => $file));
	}
	public static function addApplication($name,$file){
		self::add(array('type' => 'app', 'name' => $name, 'file' => $file));
	}
	public static function addURL($name,$file){
		self::add(array('type' => 'url', 'name' => $name, 'file' => $file));
	}
	private static function inTheme($name,$file){
		return file_exists(THEMES_PATH.$name.'/styles/'.$file);
	}
	private static function themeOverride($application,$file){
		return file_exists(THEMES_PATH.THEME.'applications/'.$application.'/styles/'.$file);
	}
	private static function inApplication($name,$file){
		return file_exists(APPS_PATH.$name.'/styles/'.$file);
	}


	private static function processExternal($args){
		$pattern = self::pattern($args);
		switch($args['type']){
			case('app'):
				if(self::themeOverride($args['name'],$args['file'])){
					self::$map['styles'][$pattern]['src'] = REL_THEMES_PATH.THEME.'applications/'.$args['name'].'/styles/'.$args['file'];
				} elseif(self::inApplication($args['name'],$args['file'])) {
					self::$map['styles'][$pattern]['src'] = REL_APPS_PATH.$args['name'].'/styles/'.$args['file'];
				}
				break;
			case('theme'):
				if(self::inTheme($args['name'],$args['file'])){
					self::$map['styles'][$pattern]['src'] = REL_THEMES_PATH.$args['name'].'/styles/'.$args['file'];
				}
				break;
			case('url'):
				self::$map['styles'][$pattern]['src'] = $args['file'];
				break;
		}
		if(!empty(self::$map['styles'][$pattern]['src'])){
			unset(self::$map['styles'][$pattern]['req']);
		} else {
			self::$map['missing.styles'][] = $pattern;
			unset(self::$map['styles'][$pattern]);
		}
	}
	private static function processInternal($args){
		$pattern = self::pattern($args);
		switch($args['type']){
			case('app'):
				if(self::themeOverride($args['name'],$args['file'])){
					self::$map['styles'][$pattern] = THEMES_PATH.THEME.'applications/'.$args['name'].'/styles/'.$args['file'];
				} elseif(self::inApplication($args['name'],$args['file'])) {
					self::$map['styles'][$pattern] = APPS_PATH.$args['name'].'/styles/'.$args['file'];
				}
				break;
			case('theme'):
				if(self::inTheme($args['name'],$args['file'])){
					self::$map['styles'][$pattern] = THEMES_PATH.$args['name'].'/styles/'.$args['file'];
				}
				break;
		}
		if(!is_string(self::$map['styles'][$pattern])){
			self::$map['missing.styles'][] = $pattern;
			unset(self::$map['styles'][$pattern]);
		}
	}
	public static function css(){
		if(!empty(self::$map['styles'])){
			switch(self::$mode){
				case('internal'):
					foreach(self::$map['styles'] as $args){
						switch($args['req']['type']){
							case('url'):
								self::processExternal($args['req']);
								break;
							default:
								self::processInternal($args['req']);
								break;
						}
					}
					break;
				case('external'):
					foreach(self::$map['styles'] as $args){
						self::processExternal($args['req']);
					}
					break;
			}
			include APPS_PATH.'common/templates/styles/'.self::$mode.'-css.tpl';
		}
		if(!empty(self::$map['missing.styles'])) include APPS_PATH.'common/templates/styles/missing-css.tpl';
	}
}
