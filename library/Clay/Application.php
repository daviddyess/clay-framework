<?php
namespace Clay;

/**
 * Application Library
 *
 * @copyright (C) 2007-2013 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Integrated Application Controller
 */

\Library('ClayDB/Connection');

\Library( 'Clay/Application/Core' );

/**
 * Initialize an Application Component's Object (controller)
 * @param string $application
 * @param string $component
 * @param string $args
 * @return object
 * @throws \Exception
 */
function Application($application,$component='main',$args=NULL){
	
	if(!import(APPS_PATH.$application.'/components/'.$component)){
		
		throw new \Exception('Application Component application\\'.$application.'\component\\'.$component.' doesn\'t exist! in '.APPS_PATH);
	}
	
	$object = 'application\\'.$application.'\component\\'.$component;
	
	return new $object($args);
}

/**
 * Alias to \Clay\Application()
 * @param string $application
 * @param string $component
 * @param mixed $args (optional)
 * @return object
 * @throws \Exception
 */
function App($application,$component='main',$args=NULL){
	
	return Application($application,$component,$args);
	
}

/**
* Imports an Application Library.
* @param string $application
* @param string $library
* @throws \Exception
* @return string - namespace of the Library
*/
function Application_Library($application,$library){
	
	if(!import(APPS_PATH.$application.'/library/'.$library)){
		
		throw new \Exception('Application API application\\'.$application.'\\library\\'.$library.' doesn\'t exist! in '.APPS_PATH);
	}
	
	return 'application\\'.$application.'\library\\'.$library;
}

/**
 * Alias to \Clay\Application_Library()
 * @param string $application
 * @param string $library
 * @return string
 * @throws \Exception
 */
function App_Lib($application,$library){
	
	return Application_Library($application,$library);
	
}

/**
 * Get an Application's Component Privilege Object
 * @param string $application
 * @param string $component
 * @throws \Exception
 */
function Application_Privilege($application,$component){
	
	# For now enforce lower-case privilege file names.
	# Attempt to import the privilege
	if(!import(\clay\APPS_PATH.$application.'/privileges/'.\strtolower($component))){
		throw new \Exception('Application Privilege application\\'.$application.'\\privilege\\'.$component.' doesn\'t exist! in '.APPS_PATH);
	}
	
	$object = 'application\\'.$application.'\privilege\\'.$component;
	
	return new $object();
}

/**
 * An integrated Controller and View library - this library is the backbone for Applications and Themes
 *
 */
class Application {
	
	/*
	 * self::db() Database Object via Trait
	 */
	use \ClayDB\Connection;
	/**
	* Allows one to access an Application Library static method as an API Function
	* @param string $application
	* @param string $library
	* @param string $function
	* @param mixed $args - default is array()
	* @throws \Exception
	*/
	public static function API($application, $library, $function, $args = array()) {
		
		# Method name as a string (including namespace)
		$appAPI = '\application\\'.$application.'\library\\'.$library;
	
		if(!method_exists($appAPI,$function)) {
			
			import(\clay\APPS_PATH.$application.'/library/'.$library);
			
			if(!method_exists($appAPI,$function)) {
				
				throw new \Exception('Application API function "'.$appAPI.'::'.$function.'()" could not be found using known file patterns!');
			
			}
		}
		
		return $appAPI::$function($args);
	}
	
	# @TODO Make this an application library object handler!!
	public static function apiObject($application, $library, $function, $args = array()) {
		
		# Method name as a string (including namespace)
		$appAPI = '\application\\'.$application.'\library\\'.$library;
	
		if(!method_exists($appAPI,$function)) {
			
			import(\clay\APPS_PATH.$application.'/library/'.$library);
			
			if(!method_exists($appAPI,$function)){
				
				throw new \Exception('Application API function "'.$appAPI.'::'.$function.'()" could not be found using known file patterns!');
			}
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
	public static function Image($name,$image,$theme=FALSE){
		
		if(empty($theme)) { 
			
			goto app; 
		}
		
		# Theme image requested
		# If it exists, set it as $file | file is set to '' otherwise.
		$file = file_exists(\clay\THEMES_PATH.$name.'/images/'.$image) ? REL_THEMES_PATH.$name.'/images/'.$image : '';
		
		goto end;
		
		# Application image requested
		app:
		
			# Check the current Theme for an override image first - file is set to '' otherwise.
			$file = file_exists(\clay\THEMES_PATH.\clay\THEME."/applications/".$name.'/images/'.$image) ? REL_THEMES_PATH.\clay\THEME."/applications/".$name.'/images/'.$image : '';
		
			# If no Theme override was found, see if it exists in the Application - $file is set to '' otherwise.
			if(empty($file)){
			
				$file = file_exists(\clay\APPS_PATH.$name.'/images/'.$image) ? REL_APPS_PATH.$name.'/images/'.$image : '';
			}
			
		end:
		
			# TODO: Provide a 'file not found' image or something?
			return $file;
	}
	/**
	 *  app::url()
	 *  - Builds the query string for an application url
	 */
	public static function URL($application,$component = '',$action = '',$extra = array()){
		
		$application = "?app=$application";
		
		$component = !empty($component) ? "&com=$component" : '';
		
		$action = !empty($action) ? "&act=$action" : '';
		
		$args = '';
		
		if(!empty($_GET['theme'])){
			
			$extra['theme'] = \Clay\Data\Get('theme','string','base');
		}
		
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
	public static function Redirect($application,$component = '',$action = '',$extra = array()){
		
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
	public static function Info($type,$name) {
		
		if($type == 'application'){
			
			$file = \clay\APPS_PATH.$name.'/info.php';
		}
		
		if($type == 'theme'){
			
			$file = \clay\THEMES_PATH.$name.'/info.php';
		}
		
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
	public static function Template($args=array()){
		
		# Let the madness begin!
		switch(true){
		
			# For redirects or other desired reasons not to include a template (component used $this->template = NULL)
			case (is_null($args)):
			# That wasn't necessary, as none of the cases below should evaluate as true, but why let it keep going?
				break;
			# Application specified in the $args array
			case (!empty($args['application'])):
				# First we look for an override template in our current theme
				if(file_exists(\clay\THEMES_PATH.\clay\THEME.'/applications/'.$args['application'].'/templates/'.$args['template'].'.tpl')){
					# The theme override exists, set the template variable and break out.
					$template = \clay\THEMES_PATH.\clay\THEME.'/applications/'.$args['application'].'/templates/'.$args['template'].'.tpl';
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
		if(empty($template)){
			# Fallback to a default template - prevents empty results and good for debugging
			$template = \clay\APPS_PATH.'/system/templates/default.tpl';
		}
		
		return array('tpl' => $template, 'data' => $args['data'], 'debug' => $args);
		
		/*
		 * The method or function using this can do something like the following to localize data and display the template:
		 */
		# If the supplied template data is an array, we extract each array key into it's own variable.
		# if(is_array($args['data'])) extract($args['data']);
		# Here's Johnny
		# include $template;
	}
	
	/**
	 *
	 * Get an Application's System ID (appid)
	 * @param string $name
	 * @return integer
	 */
	public static function getID($name){
		# Check Data Cache for Application ID
		$id = \Clay\Data\Cache::Get('app.id',$name);
		if(!empty($id)){			
			return $id;
		}
		# Get Application ID (array)
		$app = self::db()->get("appid FROM ".\claydb::$tables['apps']." WHERE name = ?", array($name), '0,1');
		# Cache & Return Application ID
		return \Clay\Data\Cache::Set('app.id',$name,$app['appid']);
		//return $app['appid'];
	}
	/**
	 *
	 * Get an Application's Namespace (name)
	 * @param integer $id (appid)
	 * @return string
	 */
	public static function getName($id){
		# Check Data Cache for Application Name
		$name = \Clay\Data\Cache::Get('app.name',$id);
		if(!empty($name)){			
			return $name;
		}
		# Get Application Name (array)
		$app = self::db()->get("name FROM ".\claydb::$tables['apps']." WHERE appid = ?", array($id), '0,1');
		# return just the name, string
		return \Clay\Data\Cache::Set('app.name',$id,$app['name']);
		
		//return $app['name'];
	}
	/**
	 *
	 * Get All Applications
	 * @param mixed $sort ORDER BY Field (optional)
	 * @param int $offset Row from Database (optional)
	 * @param int $limit Number of Rows from Database (optional)
	 * @return array
	 */
	public static function getAll( $sort = 'name', $offset = '0', $limit = NULL ){
		
		# Get Application(s) (array)
		if( !empty( $limit )){
			
			return self::db()->get( 'appid, state, version, name FROM '.\claydb::$tables['apps']." ORDER BY $sort", array(), "$offset, $limit" );
		
		} else {
			
			return self::db()->get( 'appid, state, version, name FROM '.\claydb::$tables['apps']." ORDER BY $sort" );
		}
	}
	/**
	 *
	 * Get an Application's System Version (version)
	 * @param integer (appid) OR string (name) $app
	 * @return string
	 */
	public static function getVersion($app){
		# $app can be the appid or the name
		if(is_numeric($app)){
			$where = " WHERE appid = ?";
		} else {
			$where = " WHERE name = ?";
		}
		# Get version where $app as applicable, 1 row
		$info = self::db()->get("version FROM ".\claydb::$tables['apps'].$where, array($app), '0,1');
		# return just the version, string
		return $info['version'];
	}
	
	/**
	 *
	 * Retrieve an Application's settings by Name
	 * @param string $application - App Namespace
	 * @param string $setting - Setting Name
	 * @param array OR mixed $default - Default Return Value
	 * @return string OR $default
	 */
	public static function setting($application,$setting,$default=false){
		# Check Data Cache for Application Setting
		$appSetting = \Clay\Data\Cache::Get('app.setting',$application.'.'.$setting);
		if(!empty($appSetting)){
			return $appSetting;
		}
		# Get a setting's value, where $application and $setting are keys, 1 row
		$appSetting = self::db()->get("value FROM ".\claydb::$tables['app_settings']." WHERE appid = ? AND name = ?", array(self::getID($application),$setting), '0,1');
		# return the value || $default if $default != false
		return !empty($appSetting['value'])
			? \Clay\Data\Cache::Set('app.setting',$application.'.'.$setting, $appSetting['value'])
			: \Clay\Data\Cache::Set('app.setting',$application.'.'.$setting, $default);
	}
	
	/**
	 *
	 * Retrieve All Settings Belonging to an Application
	 * @param string $application - App Namespace
	 * @param array OR mixed $default - Default Return Value
	 * @return array or $default
	 * @since 2012-03-21
	 */
	public static function settings($application,$default=array()){
		# Check Data Cache for Application Settings
		$settings = \Clay\Data\Cache::Get('app.settings',$application);
		if(!empty($settings)){
			return $settings;
		}
		# Get all settings, in a multidimensional array, where appid is the $application's appid
		$settings = self::db()->get("name, value FROM ".\claydb::$tables['app_settings']." WHERE appid = ?", array(self::getID($application)));
		# return a multidimensional array
		return !empty($settings)
			? \Clay\Data\Cache::Set('app.settings',$application, $settings)
			: \Clay\Data\Cache::Set('app.settings',$application, $default);
	}
	
	/**
	 *
	 * Set an Application Setting
	 * @param string $application (namespace)
	 * @param string $setting (setting name)
	 * @param string $value (setting value)
	 * @return NULL
	 * @TODO: If $value === NULL delete setting (David)
	 */
	public static function set($application, $setting, $value){
		# Check to see if the setting exists first, if not add it.
		# Sets appid belonging to $application, a $setting name, and its $value
		if(!self::db()->update(\claydb::$tables['app_settings']." SET value = ? WHERE appid = ? AND name = ?", array($value,self::getID($application),$setting), 1)){
			self::db()->add(\claydb::$tables['app_settings']." (appid, name, value) VALUES (?,?,?)", array(self::getID($application),$setting,$value));
		}
		\Clay\Data\Cache::Set('app.setting',$application.'.'.$setting, $value);
	}
}