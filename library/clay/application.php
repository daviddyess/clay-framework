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
	if(!import(APPS_PATH.$application.'/library/'.$library)) throw new \Exception('Application API application\\'.$application.'\\library\\'.$library.' doesn\'t exist! in '.APPS_PATH);
	return 'application\\'.$application.'\library\\'.$library;
}
/**
 * An integrated Controller and View library - this library is the backbone for Applications and Themes
 *
 */
abstract class application {

	public static function api($application, $library, $function, $args = array()) {
		# Method name as a string (including namespace)
		$appAPI = '\application\\'.$application.'\library\\'.$library;
		if(!method_exists($appAPI,$function)) {
			import(\clay\APPS_PATH.$application.'/library/'.$library);
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
	/**
	 * Static info();
	 * Fetches data from the Theme or Application's info.php file.
	 * @param string 'theme' or 'application'
	 * @param string Name of param 1
	 * @return array $data or FALSE on failure
	 */
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
	/**
	* 	Static template()
	* 	Analyzes a Template request and returns the file path of the Template and its Data in an Array.
	* 	This Static method was implemented to allow reuse of this code, while allowing objects to keep $this in context.
	* 	Splitting template() into 2 methods (static and object) was done to obtain PHP Strict Standards compliance.
	* 	@param array (tpl, data)
	* 	@example array('[application]' or '[theme]' => (string)[app] or [theme], 'template' => (string)[template], 'data' => (mixed));
	* 	@TODO Work in some error handling in case an expected template is missing.
	* 	@TODO Add debug option to display template information in HTML comments. (credit to Xaraya on the idea)
	*/
	public static function template($args=array()){
		# Let the madness begin!
		switch(true){
			# For redirects or other desired reasons not to include a template (component used $this->template = NULL)
			case (is_null($args)):
			# That wasn't necessary, as none of the cases below should evaluate as true, but why let it keep going?
				break;
				# Application specified in the $args array
			case (!empty($args['application'])):
				# First we look for an override template in our current theme
				if(file_exists(\clay\THEMES_PATH.\clay\THEME.'/applications/'.$args['application'].'/'.$args['template'].'.tpl')){
					# The theme override exists, set the template variable and break out.
					$template = \clay\THEMES_PATH.\clay\THEME.'/applications/'.$args['application'].'/'.$args['template'].'.tpl';
					break;
				}
				# Second we look for the template as specified in the application. (Note: This only happens when no theme template exists)
				if(file_exists(\clay\APPS_PATH.$args['application'].'/templates/'.$args['template'].'.tpl')){
					# The application template exists, set the template variable and break out.
					$template = \clay\APPS_PATH.$args['application'].'/templates/'.$args['template'].'.tpl';
					break;
				}
				break;
				# Theme specified in the $args array
				case (!empty($args['theme'])):
				# Look for the specified theme template (Note: This doesn't have to be the current theme)
				if(file_exists(\clay\THEMES_PATH.$args['theme'].'/templates/'.$args['template'].'.tpl')){
					# The template exists, set and break out.
					$template = \clay\THEMES_PATH.$args['theme'].'/templates/'.$args['template'].'.tpl';
					break;
				}
				break;
		}
		# For whatever reason, no template was found. Return. TODO: We need some kind of exception if a template was specified and not found.
		if(empty($template)) return; # Still trying to decide how to handle missing templates...
		return array('tpl' => $template, 'data' => $args['data']);
		
		/*
		 * The method or function using this can do something like the following to localize data and display the template:
		 */
		# If the supplied template data is an array, we extra each array key into it's own variable.
		# if(is_array($args['data'])) extract($args['data']);
		# Here's Johnny
		# include $template;
	}
}