<?php
namespace Identity;
/**
 * Identity Application Platform
 *
 * @copyright (C) 2007-2016 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Identity Core Library
 */

# Core Constants
define( 'Identity\REQUIRED', 'REQUIRED' );
define( 'Identity\ERROR', 'EXCEPTION');

/**
 * Identity Core Library
 * Default core constants and output logic
 */
class Core {

	/**
	 * Identity Initialization
	 * Defines Paths in preparation for Output
	 * @param string $config
	 */
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

	/**
	 * Clay Output
	 * Initializes User, loads the Application, and outputs the Page template of the Theme
	 * @param string $config
	 * @throws \Exception
	 */
	public static function output($config){

		# Import our libraries
		\Library('Clay/Data');
		\Library('Clay/Application');
		\Library('Clay/Application/Component');
		\Library('Clay/Module');
		\Library('ClayDB');
		\Clay\Application::API('system','error','start');
		# New User - starts Session
		$user = \Clay\Module::API('User','Instance');
		# @TODO Move this further down and allow it to use a System || User setting
		\ini_set( "date.timezone", 'America/Chicago' );
		# Find out if an Application has been requested in the URL GET app
		$application = \Clay\Data\Get('app','string','base', \Clay\Application::Setting('system','application'));
		# If GET app is supplied, this is not the default.app
		$config['default.app'] = empty($_GET['app']) ? true : false;
		# Check for the GET com (Application Component)
		$component = \Clay\Data\Get('com','string','base', \Clay\Application::Setting('system','component'));
	    if(empty($component)) $component = 'main';
		# Check for the GET act (Application Component Action)
		$action = \clay\data\get('act','string','base', \Clay\Application::Setting('system','action'));
	    # Default to Action 'view'
	    if(empty($action)) $action = 'view';
	    # No longer used, records the primary application @FIXME: Review and remove.
		$config['output'] = $application.'_'.$component.'_'.$action;
		# Initialize the Primary Application
	    $output = \Clay\Application($application,$component);
	    # Import default.app into object property default.app
		$output->defaultApp = $config['default.app'];
	    # Inject settings into object properties
		$output->inject(array('siteName' => \Clay\Application::Setting('system','site.name'),
							  'siteSlogan' => \Clay\Application::Setting('system','site.slogan'),
							  'siteFooter' => \Clay\Application::Setting('system','site.footer'),
							  'siteCopyright' => \Clay\Application::Setting('system','site.copyright')));
		# Invoke the Action
	    $output->primary = $output->action($action);
	    # Theme selector
		switch(true){
	    	# Use a GET supplied Theme name - GET theme
			case (!empty($_GET['theme'])):
				$theme = \Clay\Data\Get('theme','string','base');
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Application has specified the theme - $this->theme
			case (!empty($output->theme) && empty($config['default.app'])):
				$theme = $output->theme;
				if(!empty($theme) && file_exists(\clay\THEMES_PATH.$theme)) break;
			# Fallback to system default theme.
			case (true):
				$theme = \Clay\Application::Setting('system','theme');
				break;
		}
		# None of the above applied || none of the requested Themes exist
		if(!is_dir(\clay\THEMES_PATH.$theme)) {
			# Our theme doesn't exist!
			throw new \Exception('The specified theme could not be found. '.$theme.' theme could not be located in '.\clay\THEMES_PATH.$theme.' directory!');
		}
		# Identify the Theme in an object property/Constant
		$output->theme = $theme;
		define('clay\THEME',$theme);
		# Page Template selector
		switch(true){
			# Use a GET supplied Page Template name - GET pageName
			case (!empty($_GET['pageName'])):
				$page = \Clay\Data\Get('pageName','string','base');
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Use a System setting for the homepage if applicable
			$pageMain = \clay\application::setting('system','theme.page.main');
			case (!empty($config['default.app']) && !empty($pageMain)):
				$page = $pageMain;
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Application has specified the page template - $this->template
			case (!empty($output->page)):
				$page = $output->page;
				if(!empty($page) && file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$page.'.tpl')) break;
			# Theme has a page template override for this application ([app].tpl), otherwise we use the system page setting.
			case (true):
				$page = file_exists(\clay\THEMES_PATH.$output->theme.'/pages/'.$application.'.tpl') ? $application : 'default';
				break;
		}
		# None of the requested Page Templates exist
		if(!is_file(\clay\THEMES_PATH.$theme.'/pages/'.$page.'.tpl')) {
			throw new \Exception('The specified page template could not be found. '.$page.' page could not be located in '.\clay\THEMES_PATH.$theme.'/pages/ directory!');
		}
		# Set the object property/Constant
		$output->page = $page;
		define('clay\PAGE',$page);
		# Output $this->page
		$output->page();
		# Finish our session before ending
		\session_write_close();
		# Returns NULL
	}
}
