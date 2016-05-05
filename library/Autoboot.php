<?php
/**
 * @file autoboot.php
 */

/**
 * Autoboot
 * 
 * Multisite Boot Manager
 *
 * @copyright (C) 2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Autoboot Base Library
 */
class autoboot extends clay {
	/**
	 * Selects the Site Configuration, based on $_SERVER['SERVER_NAME'] and Autoboot Configuration
	 * @throws \Exception
	 */
	public static function selector(){
		# Get the Server Name
		$host = $_SERVER['SERVER_NAME'];
		# Compare it to domain names in Autoboot's configuration file (www. is automatically checked)
		if(!empty(static::$config[$host]) || !empty(static::$config['www.'.$host])){
			# Set the configuration name to the found site name
			$config = static::$config[$host];
		# Fail and notify
		} else {
			# No site was found to match the Server Name
			throw new \Exception("Error! Please review Installation configuration for Autoboot from the Clay Installer.");
		}
		# Route the Site name back into the bootstrap process
		\clay::bootstrap($config);
	}

	/*
	 * The way this works is fairly simple: when the Autoboot package is setup in the Installer, a configuration file is generated.
	 * A setting is set in the configuration file as 'init' => array('autoboot', 'selector'). \Clay::Bootstrap() looks for a defined
	 * 'init' setting, which tells it to use \autoboot::selector() as the initialization method for this site. 
	 * 
	 * \autoboot::selector() sorts through the Autoboot configuration file and tries to match the current domain name to a selected Site
	 * configuration name. \autoboot::selector() then hands the new Site configuration name back to \Clay::Bootstrap(), which loads
	 * the Site.
	 * 
	 * For this to work, \Clay(), the Clay Runtime called in index.php, must receive the Autoboot configuration name. By default, \Clay()
	 * expects 'default', so simply naming your Autoboot configuration to default  will get this process started.
	 */
}
?>