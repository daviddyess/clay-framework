<?php
namespace Clay\Application;
/**
 * Clay Framework
 *
 * @package Clay Integrated Application Controller
 * @subpackage Application Setup Controller
 *
 * @copyright (C) 2010-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * Application Setup Library
 * @author David
 *
 */
abstract class Setup {

	/**
	 * Path to Applications
	 * @var string
	 */
	public $path = \clay\APPS_PATH;
	
	/**
	 * Application name
	 * @var string
	 */
	public $application;
	
	/**
	 * Application State
	 * @var int
	 */
	public $state;
	
	/**
	 * Application Version
	 * @var string
	 */
	public $version;
	
	/**
	 * Application Setup Class
	 * @var string
	 */
	public $api = 'setup';
	
	/**
	 * Defer Registration to After Installation
	 */
	private $bypass = NULL;

	/**
	 * Register the Installed Application
	 */
	abstract function Register();

	/**
	 * Run Application Setup Installation
	 * @param string $app
	 * @param int $state
	 * @param array $args
	 * @param boolean $bypass
	 * @return boolean
	 */
	public function Install($app,$state,$args=array(),$bypass=NULL){
		
		$this->application = $app;
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
	 * Update Registered Application
	 */
	abstract function Update();

	/**
	 * Run Application Setup Upgrade
	 * @param string $app
	 * @param int $state
	 * @param string $version
	 * @return boolean
	 */
	public function Upgrade($app,$state,$version){
		
		$this->application = $app;
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
	 * Unregister the Removed Application
	 */
	abstract function Remove();

	/**
	 * Uninstall an Application
	 * @param string $app
	 * @return boolean
	 */
	public function Delete($app){
		
		$this->application = $app;
		
		$this->Import();
		
		$api = $this->Setup();
		
		$api::delete();
		
		$this->version = $this->Info('version');
		
		if(!$this->Remove()){
			
			return false;
		}
		
		return true;
	}

	/**
	 * Import Application Setup Class
	 * @throws \Exception
	 */
	protected function Import(){
		
		if(empty($this->application)){
			
			throw new \Exception('You must specify an application for the Clay Application Setup Library to work.');
		}
		
		if(!\import($this->path.$this->application.'/library/'.$this->api)){
			
			throw new \Exception('Application Library for '.$this->application.' named '.$this->api.' could not be found.');
		}
	}
	
	/**
	 * Get Setup API Namespace
	 * @return string
	 */
	protected function Setup(){
		
		return '\application\\'.$this->application.'\library\\'.$this->api;
	}
	
	/**
	 * Get Application Information File Data
	 * @param string $key (optional)
	 * @throws \Exception
	 * @return array
	 */
	public function Info($key=NULL){
		
		$file = $this->path.$this->application.'/info.php';
		
		if(file_exists($file)){
			
			include($file);
			
			if(!is_null($key)) return $data[$key];
			
			return $data;
			
		} else {
			
			throw new \Exception('Application '.$this->application." doesn't have a valid info data file ($file).");
		}
	}
}