<?php
namespace Clay\Module;
/**
 * Clay Framework
 *
 * @package Clay Module Setup
 * @subpackage Module Setup Base Class
 *
 * @copyright (C) 2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Module Setup Library
 * @author David
 *
 */
abstract class BaseSetup {

	/**
	 * Path to Modules
	 * @var string
	 */
	public $path = \clay\MODS_PATH;
	
	/**
	 * Module name
	 * @var string
	 */
	public $module;
	
	/**
	 * Module State
	 * @var int
	 */
	public $state;
	
	/**
	 * Module Version
	 * @var string
	 */
	public $version;
	
	/**
	 * Module Setup Class
	 * @var string
	 */
	public $api = 'Setup';
	
	/**
	 * Defer Registration to After Installation
	 */
	private $bypass = NULL;

	/**
	 * Register the Installed Application
	 */
	abstract function Register();

	/**
	 * Run Module Setup Installation
	 * @param string $mod
	 * @param int $state
	 * @param array $args
	 * @param boolean $bypass
	 * @return boolean
	 */
	public function Install($mod,$state,$args=array(),$bypass=NULL){
		
		$this->module = $mod;
		$this->state = $state;
		
		if(is_null($bypass)){
			
			$this->Register();
		}
		
		$this->Import();
		
		$api = $this->Setup();
		
		if(!$api::install($args)){
			
			return false;
		}
		
		if(!is_null($bypass)) {
			
			if(!$this->Register()) return false;
		}
		
		return true;
	}

	/**
	 * Update Registered Module
	 */
	abstract function Update();

	/**
	 * Run Module Setup Upgrade
	 * @param string $mod
	 * @param int $state
	 * @param string $version
	 * @return boolean
	 */
	public function Upgrade($mod,$state,$version){
		
		$this->module = $mod;
		$this->state = $state;
		$this->version = $version;
		
		$this->Import();
		
		$api = $this->Setup();
		
		$api::upgrade($version);
		
		$this->version = $this->Info('version');
		
		if(!$this->Update()){
			
			return false;
		}
		
		return true;
	}

	/**
	 * Unregister the Removed Module
	 */
	abstract function Remove();

	/**
	 * Uninstall a Module
	 * @param string $mod
	 * @return boolean
	 */
	public function Delete($mod){
		
		$this->module = $mod;
		
		$this->Import();
		
		$api = $this->Setup();
		
		$api::delete();
		
		if(!$this->Remove()){
			
			return false;
		}
		
		return true;
	}

	/**
	 * Import Module Setup Class
	 * @throws \Exception
	 */
	protected function Import(){
		
		if(empty($this->module)){
			
			throw new \Exception('You must specify a Module for the Clay Module Setup Library to work.');
		}
		
		if(!\import($this->path.$this->module.'/'.$this->api)){
			
			throw new \Exception('Module Setup API for '.$this->module.' named '.$this->api.' could not be found.');
		}
	}
	
	/**
	 * Get Setup API Namespace
	 * @return string
	 */
	protected function Setup(){
		
		return '\Clay\Module\\'.$this->module.'\\'.$this->api;
	}
	
	/**
	 * Get Module Information File Data
	 * @param string $key (optional)
	 * @throws \Exception
	 * @return array
	 */
	public function Info($key=NULL){
		
		$file = $this->path.$this->module.'/Info.php';
		
		if(file_exists($file)){
			
			include($file);
			
			if(!is_null($key)) return $data[$key];
			
			return $data;
			
		} else {
			
			throw new \Exception('Module '.$this->module." doesn't have a valid info data file.");
		}
	}
}