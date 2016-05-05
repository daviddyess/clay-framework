<?php
namespace application\installer\library;
/**
 * Clay Installer
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Installer
 */

/**
 * Clay Installer Package Setup API
 * @author David
 */
class setup {

	public static function website($args){

	}
	
	/**
	 * Determine a site installation has a database selected
	 * @param string $site
	 * @return boolean
	 */
	public static function database($site){
		
		$dbs = \installer::siteConfig($site,'databases');
		return !empty($dbs) ? true : false;
	}

	public static function options($args){

	}

	public static function version(){
		
		$data = \clay\application::info('application','installer');
		return $data['version'];
	}

	public static function config(){
		
		return array (		# Use if you want to use an index.php file in /installer/
							#'web.dir' => 'installer/',
							'web.path' => 'html/',
	 						# conf value is only needed when no configuration file is specified
							//'conf' => 'installer',
							'siteName' => 'Clay Unified Installer',
		  					'siteSlogan' => 'Package Manager',
		  					'themes.path' => 'installer/',
		  					'theme' => 'ctx-1',
		  					'page' => 'default',
							'apps.dir' => 'packages/',
							'apps.path' => 'installer/',
			  				'application' => 'installer',
			  				'component' => 'main',
			  				'action' => 'view',
							'installer.version' => self::version(),
							'init' => array('Installer','Bootstrap'));
		
		/* Test Config for combining app platforms
		return array(	'web.path' => 'html/',
						//'conf' => 'installer',
						'siteName' => 'Clay Installer',
		  				'siteSlogan' => 'Web Site Manager',
				  		'themes.path' => 'applications/installer/',
						'themes.dir' => 'themes/',
				  		'theme' => 'ctx-1',
				  		'page' => 'default',
						'apps.dir' => 'applications/',
						'apps.path' => '',
					  	'application' => 'installer',
					  	'component' => 'main',
					  	'action' => 'view',
						'installer.version' => self::version(),
						'init' => array('installer','bootstrap') );*/
	}

	public static function install($version){

	}

	public static function upgrade($version){
		
		switch($version){
			# incremental upgrades here
		}
	}
}