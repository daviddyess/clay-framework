<?php
namespace Clay\Application;

/**
 * Clay Application Privilege Library
 * @author David L. Dyess II
 * @copyright 2012
 * @license GPL
 */
abstract class Privilege {
	
	public $Request;
	
	public $Privilege;
	
	/**
	 * Validate an Assigned Privilege Scope Against Privilege Requirements
	 * @param string $method
	 * @param int $privilegeID
	 */
	public function Validate($method){
	
		$privileges = \Clay\Module::Object('Privileges')->Roles($this->Privilege);
	
		if(!empty($privileges)){
				
			foreach($privileges as $privilege){
	
				if($this->$method(explode("::",$privilege['scope']))){
	
					return TRUE;
				}
			}
		}
	
		return FALSE;
	}
	
	function getPrivilege($application,$component,$name){
		
		$privilege = \Clay\Module::Object('Privileges')->Get($application,$component,$name);
		
		if(!empty($privilege)){
			
			$this->Privilege = $privilege['pid'];

			return TRUE;
		
		} else {
			
			return FALSE;
		}
	}
	
	function compare(){
		
		if(count($this->base[0]) > 1){
			
		}
		
	}
	
}