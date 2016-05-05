<?php
/**
 * @file installer.php
 */
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Installer Library
 */

class Installer extends Clay {
	/**
	 * Fetch data about a site installation
	 * @param string $site
	 * @param string $config
	 * @return array or false
	 * @FIXME: BUG - this should return 'site', not the entire configurations array.
	 */
	public static function site($site) {
		# @FIXME: Should be change to (requires review):
		# $sdata = self::siteConfig('installer','configurations');
		# return $sdata[$site];
		
		return self::config('sites/installer/configurations');
	}
	/**
	 * Set Site Configuration Info (Package & version) in Installer Configurations data file.
	 * @param string $site - configuration name, ie. 'default'
	 * @param array $data - package and version data
	 */
	public static function setSite($site,$data) {
		$sdata = self::siteConfig('installer','configurations');
		$data = array_merge($sdata,$data);
		return self::setSiteConfig('installer','configurations',$data);
	}
	/**
	 * Upgrade a Site's package version (info only).
	 * @param string $site
	 * @param string $version
	 * @return boolean
	 */
	public static function upgrade($site,$version) {
		$sdata = self::siteConfig('installer','configurations');
		# Make sure the new version is newer.
		if($sdata[$site]['version'] < $version){
			# set the new version in the data array
			$sdata[$site]['version'] = $version;
			# save the updated data
			return self::setSiteConfig('installer','configurations',$sdata);
		}
		# returns NULL otherwise
	}
	/**
	 * Fetch data from a site specific configuration file
	 * @param string $site
	 * @param string $config
	 * @return array or false
	 */
	public static function siteConfig($site,$config) {
		return self::config('sites/'.$site.'/'.$config);
	}
	/**
	 * Set data in a site specific configuration file
	 * @param string $site
	 * @param string $config
	 * @param array $data
	 * @return boolean
	 */
	public static function setSiteConfig($site,$config,$data) {
		return self::setConfig('sites/'.$site.'/'.$config,$data);
	}
	/**
	 * Callback for \clay::bootstrap(). 
	 * Designated in config.php of Installer configuration ('init').
	 * Isn't used as a conventional override (invoked from \clay::bootstrap).
	 * @param string or array $config
	 */
	public static function bootstrap($config = 'installer'){
		# Call \clay::init() (or child)
		static::init();
		# Call self::output() (overrides \clay::output())
		static::output();
	}
	/**
	 * Output specific to Clay Installer.
	 * @throws \Exception
	 */
	public static function output(){
		# Fetch Installer site config data, this is a property of \clay::bootstrap()
		$config = static::$config;
	
		# Load the \installer\user Library
		\Library('Installer/user');
		\Library('Clay/Module');
		
		$user = new \installer\user;
		# Default user id is 1 (anonymous)
		if(empty($_SESSION['userid'])){
			$_SESSION['userid'] = 1;
		}
		# Loadup the Installer Sentry API
		\Library('Installer/sentry');
		# Loadup the Granule Data API
		\Library('Clay/Data');
		# Security check to ensure this is an authenticated administrator
		if(\installer\sentry::authenticate()){
			# User is admin, so we pull in config data for the current package.
			if(!empty($_GET['s'])){
				$site = \clay\data\get('s','string','base');
				$package = \clay::config('sites/installer/configurations');
				$application = $package[$site]['package'];
			} else {
				# No GET 's', so we are using the Installer config data
				$application = $config['application'];
			}
			# Depends on IF statement above. Defaults to EMPTY (installer)
			define('installer\SITE', !empty($site) ? $site : '');
			# Is this the default app? (No user input)
			$config['default.app'] = empty($_GET) ? true : false;
			# Check for the GET ?com
			$component = \clay\data\get('com','string','base', $config['component']);
		    if(empty($component)) $component = 'main';
			# Check for the GET ?act - Defaults to config data
			$action = \clay\data\get('act','string','base', $config['action']);
		    # Default Action - in case the config data doesn't specify 'action'
		    if(empty($action)) $action = 'view';
		    # No longer used @XXX: Review and delete
			$config['output'] = $application.'_'.$component.'_'.$action;
		} else {
			# Authentication failed, so we're forcing the page to display the log in
			$application = 'installer';
			# Not the home page.
			$config['default.app'] = false;
			$component = 'admin';
			# Check to make sure our Installer has been setup
			if(\installer\sentry::initiated()){
				$action = 'authenticate';
			} else {
				$action = 'setup';
			}
			# No longer used @ XXX: Review and delete.
			$config['output'] = $application.'_'.$component.'_'.$action;
		}
		define('installer\PACKAGE',$application);
		# Import Application controller (also theme controller)
		\Library('Installer/application');
		# Application Object
		\Library('Clay/Application/Component');
		$output = \clay\application($application,$component);
		# TRUE or FALSE
		$output->defaultApp = $config['default.app'];
		# Create Properties with some config data
		$output->inject(array('siteName' => $config['siteName'], 'siteSlogan' => $config['siteSlogan']));
		# The primary application being display (is an array for template and template data)
		$output->primary = $output->action($action);
		switch(true){
			# We allow user specified themes by using GET 'theme'
			case (!empty($_GET['theme'])):
				$theme = \clay\data\get('theme','string','base');
				# Stop here if TRUE (if the theme exists)
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Application has specified the theme
			case (!empty($output->theme) && empty($config['default.app'])):
				# Application Action defined $this->theme
				$theme = $output->theme;
				# Stop here (if the theme exists)
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Fall back to the system default theme.
			case (true):
				$theme = $config['theme'];
				break;
		}
		if(!is_dir(\clay\THEMES_PATH.$theme)) {
			# Our theme doesn't exist!
			throw new \Exception('The specified theme could not be found. '.$theme.' theme could not be located in '.\clay\THEMES_PATH.$theme.' directory!');
		}
		$output->theme = $theme;
		define('clay\THEME',$theme);
		switch(true){
			# GET 'pageName' - [url]?pageName=mypage
			case (!empty($_GET['pageName'])):
				$page = \clay\data\get('pageName','string','base');
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Home page - Use config data 'page.main' setting or Application specified
			case (!empty($config['default.app']) && !empty($config['page.main'])):
				$page = $config['page.main'];
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Application has specified the page template
			case (!empty($output->page)):
				# Application defined $this->page
				$page = $output->page;
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Theme has a page template for this application ([app].tpl), otherwise we use the system page setting - 'default'.
			case (true):
				$page = file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$application.'.tpl') ? $application : 'default';
				break;
		}
		if(!is_file(\clay\THEMES_PATH.$theme.'/pages/'.$page.'.tpl')) {
			# Our theme doesn't exist!
			throw new \Exception('The specified page template could not be found. '.$page.' page could not be located in '.\clay\THEMES_PATH.$theme.'/pages/ directory!');
		}
		$output->page = $page;
		define('clay\PAGE',$page);
		# Display the Page Template - the page template takes over control of what gets displayed.
		$output->page();
	}
}
?>