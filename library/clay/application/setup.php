<?php
namespace clay\application;
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

abstract class setup {

	public $path = \clay\APPS_PATH;
	public $application;
	public $version;
	public $api = 'setup';
	private $bypass = NULL;

	abstract function register();

	public function install($app,$state,$args=array(),$bypass=NULL){
		$this->application = $app;
		$this->state = $state;
		if(is_null($bypass)) $this->register();
		$this->import();
		$api = $this->setup();
		if(!$api::install($args)) return false;
		if(!is_null($bypass)) {
			if(!$this->register()) return false;
		}
		return true;
	}

	abstract function update();

	public function upgrade($app,$state,$version){
		$this->application = $app;
		$this->state = $state;
		$this->version = $version;
		$this->import();
		$api = $this->setup();
		$api::upgrade($version);
		$this->version = $this->info('version');
		if(!$this->update()) return false;
		return true;
	}

	abstract function remove();

	public function delete($app){
		$this->application = $app;
		$this->import();
		$api = $this->setup();
		$api::delete();
		if(!$this->remove()) return false;
		return true;
	}

	protected function import(){
		if(empty($this->application)) throw new \Exception('You must specify an application for the Clay Application Setup Library to work.');
		if(!\import($this->path.$this->application.'/libraries/'.$this->api)) throw new \Exception('Application Library for '.$this->application.' named '.$this->api.' could not be found.');
	}
	protected function setup(){
		return '\application\\'.$this->application.'\api\\'.$this->api;
	}
	public function info($key=NULL){
		$file = $this->path.$this->application.'/info.php';
		if(file_exists($file)){
			include($file);
			if(!is_null($key)) return $data[$key];
			return $data;
		} else {
			throw new \Exception('Application '.$this->application." doesn't have a valid info data file.");
		}
	}
}