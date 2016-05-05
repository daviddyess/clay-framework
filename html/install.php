<?php
/**
 * @file install.php
 * @brief Entry point for Clay Installer
 * @details The install.php provides access to the Clay Installer Platform - a univeral installer for Clay-based projects
 */

/**
 * Clay Installer
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

# Add the base directory of Clay to the include path
# If you use any entry point in a different folder, you will need to adjust the Path here
set_include_path(dirname(dirname(__FILE__)) . PATH_SEPARATOR . get_include_path());
	
# Include the Clay Library
include 'library/Clay.php';

# Run Clay
\Clay(array (	'web.path' => 'html/',
				'conf' => 'installer',
				'siteName' => 'Clay Unified Installer',
  				'siteSlogan' => 'Package Manager',
		  		'themes.path' => 'installer/',
				'themes.dir' => 'themes/',
		  		'theme' => 'ctx-1',
		  		'page' => 'default',
				'apps.dir' => 'packages/',
				'apps.path' => 'installer/',
			  	'application' => 'installer',
			  	'component' => 'main',
			  	'action' => 'view',
				'init' => array('Installer','bootstrap')));