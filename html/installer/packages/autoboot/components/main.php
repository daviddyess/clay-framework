<?php
namespace application\autoboot\component;
/**
* Autoboot
* 
* This package is a utility to be used as a boot selector. It matches domain names with corresponding CF configuration names.
*
* @copyright (C) 2012 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
* @package Autoboot Installer Package
*/

\Library('Clay/Application/Component');

/**
 * Autoboot Package
 */
class main extends \Clay\Application\Component {

	public function view(){
		
		$data = array();
		$data['configs'] = \installer::siteConfig(\clay\data\get('s','string','base'), 'config');
		
		if(empty($data['configs'])){
			
			\installer::setSiteConfig(\clay\data\get('s','string','base'),'config',array('init' => array (0 => 'Autoboot', 1 => 'selector')));
			$data['configs'] = \installer::siteConfig(\clay\data\get('s','string','base'), 'config');
		}
		
		unset($data['configs']['init']);
		return $data;
	}
	
	public function create (){
		
		$configs = \clay\data\post('conf');
		$data = array();
		# $configs[] = array('name' => string, 'conf' => string)
		
		foreach($configs as $domain){
			
			if(!empty($domain['name'])){
				
				$data[$domain['name']] = $domain['conf'];
			}			
		}
		
		if(!empty($data)){
			
			$config = \installer::siteConfig(\clay\data\get('s','string','base'),'config');
			$autoboot = array_merge($config,$data);
			\installer::setSiteConfig(\clay\data\get('s','string','base'),'config',$autoboot);
		}
		
		\clay::redirect($_SERVER['HTTP_REFERER']);
	}
}