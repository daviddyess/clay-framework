<?php
namespace application\installer\component;
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
 * Clay Installer Package Setup Component
 * @author David
 *
 */
class setup extends \Clay\Application\Component {
	
	# ! These are not used in other components yet, so if you change them here you have to change references other places as well !
	# Folder within /data/config/ where sites are found.
	private static $sites = 'sites/';
	# Data file where the Installer stores site installation information (package data, not configs)
	private static $installer_sites = 'sites/installer/configurations';
	# Folder within /data/ where deleted files are stored.
	private static $restore = 'backups/restore';
	# Folder where deleted sites are stored.
	private static $installer_restore = 'backups/restore/sites/';

	/**
	 * Installations
	 * @return array
	 */
	public function view(){
		
		$this->pageTitle = 'Site Installations';
		$data = array();
		# Import our Javascript handler
		\Library('Clay/Scripts');
		# Add jquery to the js handler queue
		\clay\scripts::addApplication('common','jquery/jquery.js','head');
		# If our application path exists, build a list of apps
	  	$pkgs = file_exists(\clay\APPS_PATH) ? scandir(\clay\APPS_PATH) : array();
	    $rowclass = '';
	    
	    foreach($pkgs as $pkg){
	    	
	    	$rowclass = ( 'alt-' != $rowclass ) ? 'alt-' : '';
	    	
	    	# Hide these
			if(($pkg == '.') || ($pkg == '..')){
	    		
		        unset($pkg);
		        continue;
			}
			
			$pkgdata = \installer\application::package($pkg);
			
			if(!empty($pkgdata)){
				
				$pkgdata['info'] = true;
				$data['pkgs'][] = $pkgdata;
			}
	    }
	    
	    # Check for deleted sites
		$backups = file_exists(\clay\DATA_PATH.self::$installer_restore) ? scandir(\clay\DATA_PATH.self::$installer_restore) : array();
	    $rowclass = '';
	    
	    foreach($backups as $backup){
	    	
	    	$rowclass = ( 'alt-' != $rowclass ) ? 'alt-' : '';
	    	
	    	# Hide these
			if(($backup == '.') || ($backup == '..')){
		        unset($backup);
		        continue;
			}
			
			# Read the restore.php data files to get info on the deleted sites
			$backupdata = $this->backupInfo($backup);
			
			if(!empty($backupdata)){
				
				$backupdata['info'] = true;
				if(empty($backupdata['name'])) $backupdata['name'] = $backup;
				$data['backups'][] = $backupdata;
			}
	    }
	    
	    # Data file of installed packages
		$systemConfs = \Clay::Config(self::$installer_sites);
	    
		if(!empty($systemConfs)){
			
			foreach($systemConfs as $conf => $setting){
				
				$package = \installer\application::package($setting['package']);
				
				if($package['version'] != $setting['version']){
					
					$systemConfs[$conf]['update'] = $package['version'];
				}
			}
		}
		
		# Installations, has to be an array
		$data['confs'] = !empty($systemConfs) ? $systemConfs : array();
		# We use this for prompting and user-friendly error reporting
		$data['message'] = !empty($_SESSION['msg']) ? $_SESSION['msg'] : '';
		# Kill the message so it doesn't show up again
		unset($_SESSION['msg']);
	    return $data;
	}
	
	/**
	 * Adds a new Package installation
	 * @return redirect
	 */
	public function add(){
		
		# Name
		$newconf = \clay\data\post('new_conf','string','base','');
		# Package and version
	    $package = \clay\data\post('pkgchoice','string','base','');
	    $version = \clay\data\post('pkgver','string','base','');
	    
	    # If true, set an error message
		if(empty($newconf) || empty($package) || empty($version)){
	    	
			if(empty($newconf)) {
				
				$_SESSION['msg'] = 'Oops! You must provide a name for the package installation.';
				
			} elseif(empty($package)) {
				
				$_SESSION['msg'] = 'Oops! Please choose a package. If a package was selected there is a problem with that package. No package name exists in the package configuration file.';
			
			} elseif(empty($version)) {
				
				$_SESSION['msg'] = 'Oops! There is a problem with that package. No version information exists in the package configuration file.';
			}
			
			# Go back so the user can correct the error
			return \clay::redirect($_SERVER['HTTP_REFERER']);
		}
		
		# Get the installations or set an empty array
		$confs = \clay::config(self::$installer_sites);

		if(empty($confs[$newconf])){
			
			# Installation doesn't exist, setup the config data file and create it
			$confs[$newconf] = array('package' => $package, 'version' => $version);
			\clay::setConfig(self::$installer_sites,$confs);
			
			if(!file_exists(\clay\CFG_PATH.self::$sites.$newconf)){
				
				\mkdir(\clay\CFG_PATH.self::$sites.$newconf);
			}
			
			$_SESSION['msg'] = 'Package installation '.$newconf.' was successfully created!';
    		\clay::redirect($_SERVER['HTTP_REFERER']);
    		
		} else {
			
			# Installation exists, go back and prompt the user
			$_SESSION['msg'] = 'Package installation '.$newconf.' already exists! Please choose a difference name.';
    		\clay::redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * rename()
	 * Rename a site configuration
	 * @return NULL
	 */
	public function rename(){
		
		# Name
		$newconf = \clay\data\post('new_conf','string','base','');
		$oldconf = \clay\data\post('old_conf','string','base','');
		
		if(empty($newconf)) {
			
			$_SESSION['msg'] = 'Oops! You must provide a name for the package installation.';
			# Go back so the user can correct the error
			return \clay::redirect($_SERVER['HTTP_REFERER']);
		}
		
		# Get the installations or set an empty array
		$confs = \clay::config(self::$installer_sites);
		
		if(empty($confs[$newconf])){
			
			# Installation doesn't exist, setup the config data file and create it
			$confs[$newconf] = $confs[$oldconf];
			unset($confs[$oldconf]);
			\clay::setConfig(self::$installer_sites,$confs);
			if(!file_exists(\clay\CFG_PATH.self::$sites.$newconf)) \rename(\clay\CFG_PATH.self::$sites.$oldconf, \clay\CFG_PATH.self::$sites.$newconf);
			$_SESSION['msg'] = 'Package installation '.$oldconf.' was successfully renamed to '.$newconf.'!';
			\clay::redirect($_SERVER['HTTP_REFERER']);
			
		} else {
			
			# Installation exists, go back and prompt the user
			$_SESSION['msg'] = 'Package installation '.$newconf.' already exists! Please choose a difference name.';
			\clay::redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * Delete a Package Installation
	 * @return redirect|array
	 */
	public function delete(){
		
		$site = \clay\data\get('site','string','base',\clay\data\post('site','string','base'));
		$data = array();
		$data['site'] = $site;
		
		if(empty($site)){
			
			$_SESSION['msg'] = 'Oops! You must select a package installation to delete.';
			return \clay::redirect($_SERVER['HTTP_REFERER']);
		}
		
		$confirmation = \clay\data\post('confirm','int');
		if(!empty($confirmation)){
			
			$systemConfs = \clay::config(self::$installer_sites);
			$restore = $systemConfs[$site];
			$restore['name'] = $site;
			unset($systemConfs[$site]);
			\clay::setConfig(self::$installer_sites,$systemConfs);
			\chdir(\clay\DATA_PATH);
			
			if(!\is_dir(self::$restore)){
				
				\mkdir(self::$restore);
			}
			
			if(!\is_dir(self::$installer_restore)){
				
				\mkdir(self::$installer_restore);
			}
			
			if(\rename('config/'.self::$sites.$site, self::$installer_restore.$site)){
				
				$this->setRestore($site,$restore);
				$_SESSION['msg'] = $site.' has been deleted. Old configuration files were backed up to '.\clay\DATA_PATH.'backups/sites/'.$site;
			
			} else {

				$_SESSION['msg'] = $site.' has been deleted, however, no configuration files were found to perform a backup.';
			}
			
			\installer\application::redirect('setup','view');
		}
		
		return $data;
	}
	
	/**
	 * Restore a Package Installation Backup
	 * @return redirect|array
	 */
	public function restore(){
		
		$site = \clay\data\get('site','string','base',\clay\data\post('site','string','base'));
		$data = array();
		$data['site'] = $site;
		
		if(empty($site)){
			
			$_SESSION['msg'] = 'Oops! You must select a package installation to restore.';
			return \clay::redirect($_SERVER['HTTP_REFERER']);
		}
		
		$confirmation = \clay\data\post('confirm','int');
		
		if(!empty($confirmation)){
			
			$systemConfs = \clay::config(self::$installer_sites);
			$siteRestore = $this->backupInfo($site);
			$systemConfs[$site] = array('package' => $siteRestore['package'],'version' => $siteRestore['version']);
			\clay::setConfig('system/configurations',$systemConfs);
			\chdir(\clay\DATA_PATH);
			
			if(\rename(self::$installer_restore.$site, 'config/'.self::$sites.$site)){
				
				unlink('config/'.self::$sites.$site.'/restore.php');
				$_SESSION['msg'] = $site.' has been restored.';
				
			} else {
				
				$_SESSION['msg'] = $site.' could not be restored.';
			}
			
			\installer\application::redirect('setup','view');
		}
		
		return $data;
	}
	
	/**
	 * Delete a Package Installation backup
	 * @return redirect|array
	 */
	public function delbackup(){
		
		$site = \clay\data\get('site','string','base',\clay\data\post('site','string','base'));
		$data = array();
		$data['site'] = $site;
		
		if(empty($site)){
			
			$_SESSION['msg'] = 'Oops! You must select a backup to delete.';
			return \clay::redirect($_SERVER['HTTP_REFERER']);
		}
		
		$confirmation = \clay\data\post('confirm','int');
		
		if(!empty($confirmation)){
			
			\chdir(\clay\DATA_PATH);
			
			if($this->deleteDir(self::$installer_restore.$site)){
				
				$_SESSION['msg'] = 'Backup of '.$site.' has been deleted.';
				
			} else {
				
				$_SESSION['msg'] = 'Backup of '.$site.' could not be deleted.';
			}
			
			\installer\application::redirect('setup','view');
		}
		
		return $data;
	}
	
	/**
	 * Set backup restoration data file
	 * @param string $site
	 * @param array $data
	 * @return boolean
	 */
	private function setRestore($site,$data) {
		
		$content = "<?php\n" . '$data = ' . var_export($data,1).";\n ?>";
		$file = fopen(\clay\DATA_PATH.self::$installer_restore.$site.'/restore.php', "w");
		
		if(fwrite($file, $content)){
			
			fclose($file);
			return true;
			
		} else {
			
			# Exception would be nice here? //throw new Exception('clay::setConfig() was unable to write to '.$config.'. Please check file permissions.');
			return false;
		}
	}
	
	/**
	 * Get data from a restoration data file
	 * @param string $site
	 * @return array|boolean
	 */
	private function backupInfo($site) {
		
		$file = \clay\DATA_PATH.self::$installer_restore.$site.'/restore.php';
		
		if(file_exists($file)){
			
			include($file);
			return $data;
			
		} else {
			
			return false;
		}
	}
	
	/**
	 * Recursively delete a folder and any subfolders or files.
	 * @param string $dir [path]
	 * @return boolean
	 * @TODO Move this to an appropriate API
	 */
	private function deleteDir($dir){
		
		if(is_dir($dir)) {
			
			$objects = scandir($dir);
			
			foreach ($objects as $object) {
				
				if ($object != "." && $object != "..") {
					
					if (filetype($dir."/".$object) == "dir") $this->deleteDir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			
			reset($objects);
			rmdir($dir);
			return true;
		}
		
		return false;
	}
}