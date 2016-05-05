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
 * Clay Installer Package Databases Component
 * @author David
 *
 */
class databases extends \Clay\Application\Component {

	/**
	 * View Action
	 */
	public function view(){
		
		if(!\clay\data\get('site','string','base')) {
			
			return $this->system();
			
		} else {
			
			return $this->site();
		}
	}
	
	/**
	 * System Action
	 */
	public function system(){
		
		$data = array();
		\Library('Clay/Scripts');
		\clay\scripts::addApplication('common','jquery/jquery.js','head');
		$this->pageTitle = 'System Databases';
		$this->template = 'databases_system';
	    # TODO: Make this easier to access (consolidated static class?)
		\Library('ClayDB/tool/check_exts');
		$data['dbexts'] = \claydb\tool\check_exts();
		
	 	if(!\clay::config('databases')){
	 		
				$data['nodbs'] = true;
				return $data;
		}
		
		$data['message'] = !empty($_SESSION['msg']) ? $_SESSION['msg'] : '';
		unset($_SESSION['msg']);
		$data['dbs'] = \clay::config('databases');
	    return $data;
	}
	
	/**
	 * Site Action
	 * @throws \Exception
	 */
	public function site(){
		
		$data = array();
		\Library('Clay/Scripts');
		\clay\scripts::addApplication('common','jquery/jquery.js','head');
		$this->pageTitle = 'Site Databases';
		$this->template = 'databases_site';
		$data['site'] = \clay\data\get('site','string','base',\clay\data\get('s','string','base'));
		
		if(empty($data['site'])){
			
			throw new \Exception('This application action is only available from site installation packages.');
		}
		
	    # TODO: Make this easier to access (consolidated static class?)
		\Library('ClayDB/tool/check_exts');
		$data['dbexts'] = \claydb\tool\check_exts();
		
	 	if(!\clay::config('databases')){
	 		
				$data['nodbs'] = true;
				return $data;
		}
		
		$confdbs = \clay::config('sites/'.$data['site'].'/databases');
		$data['cfg'] = !empty($confdbs) ? $confdbs : array();
		$data['dbs'] = \clay::config('databases');
	    return $data;
	}
	
	/**
	 * Add Action
	 */
	public function add(){
		
	    if(!empty($_POST['dbs'])){
	    	
	    	return $this->addToSystem();
	    }
	    
	    if(!empty($_POST['site'])){
	    	
	    	return $this->addToSite();
	    }
	}
	
	/**
	 * Update Action
	 */
	public function update(){
		
		if(!empty($_POST['dbs'])){
			
	    	return $this->addToSystem();
	    }
	    
	    if(!empty($_POST['site'])){
	    	
	    	return $this->addToSite();
	    }
	}
	
	/**
	 * Delete Action
	 */
	public function delete(){
		
		if(!empty($_POST['dbs'])){
			
	    	return $this->deleteFromSystem();
	    }
	    
	    if(!empty($_POST['site'])){
	    	
	    	return $this->deleteFromSite();
	    }
	}
	
	/**
	 * Add to System Action
	 */
	private function addToSystem(){
		
		$dbcfg = \clay::config('databases');
		
    	if(empty($dbcfg)){
    		
			$dbcfg = array();
    	}
    	
    	$new = array();
    	$dbs = \clay\data\post('dbs');
    	$new['type'] = $dbs['dbtype'];
    	$new['host'] = $dbs['dbhost'];
    	$new['usern'] = $dbs['dbuser'];
    	$new['passw'] = $dbs['dbpass'];
    	$dbindex = \clay\data\post('dbindex','alnum');
    	
    	if($dbindex >= '1'){
    		
    		$dbcfg[$dbindex] = $new;
    		$_SESSION['msg'] = 'db'.$dbindex.' was successfully updated!';
    		
    	} else {
    		# what if a db was deleted?
    		//$dbindex = count($dbcfg) + 1;
    		//$dbcfg[$dbindex] = $new;
    		# better?
    		end($dbcfg); $dbindex = key($dbcfg) + 1;
    		$dbcfg[$dbindex] = $new;
    		$_SESSION['msg'] = 'db'.$dbindex.' was successfully added! You may now add it to your package installations.';
    	}
    	
    	\clay::setConfig('databases',$dbcfg);
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * Delete from System Action
	 * @throws \Exception
	 */
	private function deleteFromSystem(){
		
		$dbcfg = \clay::config('databases');
		
    	if(empty($dbcfg)){
    		
			throw new \Exception('There are no databases stored!');
    	}
    	
    	$dbindex = \clay\data\post('dbindex','alnum');
    	unset($dbcfg[$dbindex]);
    	\clay::setConfig('databases',$dbcfg);
    	$_SESSION['msg'] = 'db'.$dbindex.' was successfully deleted! Remember to delete references to it from your package installations.';
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * Add to Site Action
	 */
	private function addToSite(){
		
		$site = \clay\data\post('site');
		$dbcfg = \clay::config('sites/'.$site.'/databases');
		
    	if(empty($dbcfg)){
    		
			$dbcfg = array();
    	}
    	
    	$new = array();
    	$new['prefix'] = \clay\data\post('prefix');
    	$new['connection'] = \clay\data\post('dbcon');
    	$new['database'] = \clay\data\post('dbname');
    	$newcfg = \clay\data\post('dbcfg');
    	$dbcfg[$newcfg] = $new;
    	\clay::setConfig("sites/$site/databases",$dbcfg);
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * Delete from Site Action
	 */
	private function deleteFromSite(){
		
		$site = \clay\data\post('site');
		$dbcfg = \clay::config('sites/'.$site.'/databases');
		
    	if(empty($dbcfg)){
    		
			$dbcfg = array();
    	}
    	
    	$new = array();
    	$new['prefix'] = \clay\data\post('prefix');
    	$new['db'] = \clay\data\post('dbcon');
    	$newcfg = \clay\data\post('dbcfg');
    	$dbcfg[$newcfg] = $new;
    	\clay::setConfig("sites/$conf/databases",$dbcfg);
    	\clay::redirect($_SERVER['HTTP_REFERER']);
	}
}