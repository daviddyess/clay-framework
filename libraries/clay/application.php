<?php
namespace clay;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Integrated Application Controller
 */
/**
 * Provides an easy method of calling Application Components
 * @param string $application
 * @param string $component
 * @return string
 * @TODO Make this return the object instead of class name?
 */
function application($application,$component='main'){
	if(!import(APPS_PATH.$application.'/components/'.$component)) throw new \Exception('Application Component application\\'.$application.'\\'.$component.' doesn\'t exist! in '.APPS_PATH);
	return 'application\\'.$application.'\\'.$component;
}
function application_library($application,$library){
	if(!import(APPS_PATH.$application.'/libraries/'.$library)) throw new \Exception('Application API application\\'.$application.'\\api\\'.$library.' doesn\'t exist! in '.APPS_PATH);
	return 'application\\'.$application.'\api\\'.$library;
}
/**
 * An integrated Controller and View library - this library is the backbone for Applications and Themes
 *
 */
abstract class application {

	public static function api($application, $library, $function, $args = array()) {
		# Method name as a string (including namespace)
		$appAPI = '\application\\'.$application.'\api\\'.$library;
		if(!method_exists($appAPI,$function)) {
			import(\clay\APPS_PATH.$application.'/libraries/'.$library);
			if(!method_exists($appAPI,$function)) throw new \Exception('Application API function "'.$appAPI.'::'.$function.'()" could not be found using known file patterns!');
		}
		return $appAPI::$function($args);
	}
	/**
	 * Get image URLs from Application (with theme overrides) and Themes
	 * @param string $name | Application/Theme name
	 * @param string $image | image file name (with extension)
	 * @param boolean $theme | optional, unset = Application image, set = Theme image
	 * @return string
	 */
	public static function image($name,$image,$theme=FALSE){
		if(empty($theme)) { goto app; }
		# Theme image requested
		# If it exists, set it as $file | file is set to '' otherwise.
		$file = file_exists(\clay\THEMES_PATH.$name.'/images/'.$image) ? REL_THEMES_PATH.$name.'/images/'.$image : '';
		goto end;
		# Application image requested
		app:
		# Check the current Theme for an override image first - file is set to '' otherwise.
		$file = file_exists(\clay\THEMES_PATH.\clay\THEME."/applications/".$name.'/images/'.$image) ? REL_THEMES_PATH.\clay\THEME."/applications/".$name.'/images/'.$image : '';
		# If no Theme override was found, see if it exists in the Application - $file is set to '' otherwise.
		if(empty($file)) $file = file_exists(\clay\APPS_PATH.$name.'/images/'.$image) ? REL_APPS_PATH.$name.'/images/'.$image : '';
		end:
		# TODO: Provide a 'file not found' image or something?
		return $file;
	}
	/**
	 *  app::url()
	 *  - Builds the query string for an application url
	 */
	public static function url($application,$component = '',$action = '',$extra = array()){
		$application = "?app=$application";
		$component = !empty($component) ? "&com=$component" : '';
		$action = !empty($action) ? "&act=$action" : '';
		$args = '';
		foreach($extra as $piece){
			$args = $args.'&'.key($extra).'='.$piece;
			next($extra);
		}

		return $application.$component.$action.$args;
	}
	/**
	 *  app::redirect()
	 *  - Redirects the browser to another application url
	 *  - Mainly used within app api function
	 */
	public static function redirect($application,$component = '',$action = '',$extra = array()){
		session_write_close();
		header( 'Location: '.self::url($application,$component,$action,$extra) );
	}
	public static function info($type,$name) {
		if($type == 'application') $file = \clay\APPS_PATH.$name.'/info.php';
		if($type == 'theme') $file = \clay\THEMES_PATH.$name.'/info.php';
		if(file_exists($file)){
			include($file);
			return $data;
		} else {
			return false;
		}
	}
}