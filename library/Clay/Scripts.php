<?php
namespace Clay;

/**
 * Clay Framework
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Scripts Manager
 */

/**
 * Provides a means of dynamically linking or including javascripts from Application and Themes, as well as external URLs.
 */
class Scripts {

	public static $mode = 'external';
	protected static $map = array();

	private static function pattern($args){
		return $args['type'].':'.$args['name'].':'.$args['file'];
	}
	public static function add($args){
		$pattern = self::pattern($args);
		if(empty($args['position'])) $args['position'] = 'head';
		if(!isset(self::$map['scripts.'.$args['position']][$pattern]['req'])){
			self::$map['scripts.'.$args['position']][$pattern]['req'] = $args;
		}
	}
	public static function addTheme($name,$file,$position='body'){
		self::add(array('type' => 'theme', 'name' => $name, 'file' => $file, 'position' => $position));
	}
	public static function addApplication($name,$file,$position='body'){
		self::add(array('type' => 'app', 'name' => $name, 'file' => $file, 'position' => $position));
	}
	public static function addURL($name,$file,$position='body'){
		self::add(array('type' => 'url', 'name' => $name, 'file' => $file, 'position' => $position));
	}
	private static function inTheme($name,$file){
		return file_exists(THEMES_PATH.$name.'/scripts/'.$file);
	}
	private static function themeOverride($application,$file){
		return file_exists(THEMES_PATH.THEME.'/applications/'.$application.'/scripts/'.$file);
	}
	private static function inApplication($name,$file){
		return file_exists(APPS_PATH.$name.'/scripts/'.$file);
	}
	private static function addExternal($args){
		$pattern = self::pattern($args);

			switch($args['type']){
				case('app'):
					if(self::themeOverride($args['name'],$args['file'])){
						self::$map['scripts.'.$args['position']][$pattern]['src'] = REL_THEMES_PATH.THEME.'/applications/'.$args['name'].'/scripts/'.$args['file'];
					} elseif(self::inApplication($args['name'],$args['file'])) {
						self::$map['scripts.'.$args['position']][$pattern]['src'] = REL_APPS_PATH.$args['name'].'/scripts/'.$args['file'];
					}
					break;
				case('theme'):
					if(self::inTheme($args['name'],$args['file'])){
						self::$map['scripts.'.$args['position']][$pattern]['src'] = REL_THEMES_PATH.$args['name'].'/scripts/'.$args['file'];
					}
					break;
				case('url'):
					self::$map['scripts.'.$args['position']][$pattern]['src'] = $args['file'];
			}
			if(!empty(self::$map['scripts.'.$args['position']][$pattern]['src'])){
				unset(self::$map['scripts.'.$args['position']][$pattern]['req']);
			} else {
				self::$map['missing.scripts.'.$args['position']][] = $pattern;
				unset(self::$map['scripts.'.$args['position']][$pattern]);
			}


	}
	private static function addInternal($args){
		$pattern = self::pattern($args);
		switch($args['type']){
			case('app'):
				if(self::themeOverride($args['name'],$args['file'])){
					self::$map['scripts.'.$args['position']][$pattern] = THEMES_PATH.THEME.'/applications/'.$args['name'].'/scripts/'.$args['file'];
				} elseif(self::inApplication($args['name'],$args['file'])) {
					self::$map['scripts.'.$args['position']][$pattern] = APPS_PATH.$args['name'].'/scripts/'.$args['file'];
				}
				break;
			case('theme'):
				if(self::inTheme($args['name'],$args['file'])){
					self::$map['scripts.'.$args['position']][$pattern] = THEMES_PATH.$args['name'].'/scripts/'.$args['file'];
				}
				break;
		}
		if(!is_string(self::$map['scripts.'.$args['position']][$pattern])){
			self::$map['missing.scripts.'.$args['position']][] = $pattern;
			unset(self::$map['scripts.'.$args['position']][$pattern]);
		}
	}
	public static function js($position){
		if(!empty(self::$map['scripts.'.$position])){
			switch(self::$mode){
				case('internal'):
					foreach(self::$map['scripts.'.$position] as $args){
						if(!empty($args['req'])){
							switch($args['req']['type']){
								case('url'):
									self::addExternal($args['req']);
									break;
								default:
									self::addInternal($args['req']);
									break;
							}
						}
					}
					break;
				case('external'):
					foreach(self::$map['scripts.'.$position] as $args){
						if(!empty($args['req'])){
							self::addExternal($args['req']);
						}
					}
					break;
			}
			#TODO Change the templates folder to something better reflecting this purpose
			include APPS_PATH.'common/templates/scripts/'.self::$mode.'-js.tpl';
		}
		#TODO Change the templates folder to something better reflecting this purpose
		if(!empty(self::$map['missing.scripts.'.$position])) include APPS_PATH.'common/templates/scripts/missing-js.tpl';
	}
}
