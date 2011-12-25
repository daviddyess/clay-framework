<?php
namespace clay;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Core Library
 */
/**
 * Default core constants and output logic; primarily intended to serve as examples.
 */
class core {

	public static function init($config){
		# Some constants for our paths
		define('clay\DB_CFG', !empty($config['db.cfg']) ? \clay\DATA_PATH.\clay\CFG_PATH.$config['db.cfg'] : \clay\DATA_PATH.\clay\CFG_PATH.'system/databases');
		# Path from base directory all the way through the web directory
		define('clay\WEB_PATH', !empty($config['web.path']) ? \clay\PATH.'/'.$config['web.path'] : \clay\PATH.'/html/');
		# Path from base (ie. var/www or home) directory all the way through the location of the current script
		define('clay\WEB_DIR', !empty($config['web.dir']) ? \clay\WEB_PATH.$config['web.dir'] : \clay\WEB_PATH);
		# Find the relative number of folders between path and dir
		$relative_web_path_count = substr_count(\clay\WEB_DIR,'/') - substr_count(\clay\WEB_PATH,'/');
		# Create a relative path from WEB_DIR to WEB_PATH
		define('clay\WEB_REL_PATH',str_repeat('../',$relative_web_path_count));
		define('clay\CFG_NAME', $config['conf']);
		define('clay\APPS_DIR', !empty($config['apps.dir']) ? $config['apps.dir'] : 'applications/');
		define('clay\APPS_PATH', !empty($config['apps.path']) ? \clay\WEB_DIR.$config['apps.path'].\clay\APPS_DIR : \clay\WEB_DIR.\clay\APPS_DIR);
		# Relative Path to Applications Directory from Web Root (for linking in HTML)
		define('clay\REL_APPS_PATH', !empty($config['apps.path']) ? $config['apps.path'].\clay\APPS_DIR : \clay\APPS_DIR);
		define('clay\THEMES_DIR', !empty($config['themes.dir']) ? $config['themes.dir'] : 'themes/');
		define('clay\THEMES_PATH', !empty($config['themes.path']) ? \clay\WEB_DIR.$config['themes.path'].\clay\THEMES_DIR : \clay\WEB_DIR.\clay\THEMES_DIR);
		# Relative Path to Themes Directory from Web Root (for linking in HTML)
		define('clay\REL_THEMES_PATH', !empty($config['themes.path']) ? $config['themes.path'].\clay\THEMES_DIR : \clay\THEMES_DIR);
	}

	public static function output($config){
		# Loadup the Granule Data API
		\clay::library('data');
		# Find out if an Application has been requested in the URL (?app=)
		$application = \clay\data\get('app','string','base', $config['application']);
		$config['default.app'] = empty($_GET['app']) ? true : false;
		# Check for the ?com
		$component = \clay\data\get('com','string','base', $config['component']);
	    if(empty($component)) $component = 'main';
		# Check for the ?act
		$action = \clay\data\get('act','string','base', $config['action']);
	    if(empty($action)) $action = 'view';
		$config['output'] = $application.'_'.$component.'_'.$action;
		# FIXME: We need a session handler selector
		/*if(!empty($system['sessions']) && $system['sessions'] == 'session'){ // Temporary solution
			\user::session_start(); // Start the session class
		} else {
			\session_start(); // Start the PHP session
		}*/
		\clay::library('user');
		$user = new \clay\user;
		# Default user id is 1 (anonymous)
		if(empty($_SESSION['userid'])){
			$_SESSION['userid'] = 20;
		}
		# Add 'userid' to the 'system' cache
		//\clay\data\cache::set('system','userid',$system['userid']);
		# Import our Controller class
		\clay::library('application');
		$primaryApp = \clay\application($application,$component);
		$output = new $primaryApp;
		$output->defaultApp = $config['default.app'];
		$output->inject(array('siteName' => $config['siteName'], 'siteSlogan' => $config['siteSlogan'], 'pageTitle' => $config['pageTitle']));
		$output->primary = $output->action($action);
		switch(true){
			case (!empty($_GET['theme'])):
				$theme = \clay\data\get('theme','string','base');
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Application has specified the theme
			case (!empty($output->theme) && empty($config['default.app'])):
				$theme = $output->theme;
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Theme has a page template for this application ([app].tpl), otherwise we use the system page setting.
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
			# [url]?pageName=mypage
			case (!empty($_GET['pageName'])):
				$page = \clay\data\get('pageName','string','base');
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Home page - Use system/page.main setting or Application specified
			case (!empty($config['default.app']) && !empty($config['page.main'])):
				$page = $config['page.main'];
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Application has specified the page template
			case (!empty($output->page)):
				$page = $output->page;
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Theme has a page template for this application ([app].tpl), otherwise we use the system page setting.
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
		$output->page();
	}

}