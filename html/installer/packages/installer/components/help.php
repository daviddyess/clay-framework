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
 * Clay Installer Package Help Component
 * @author David
 *
 */
class help extends \Clay\Application\Component {

	/**
	 * View Action
	 */
	public function view(){
		
		$data = array();
		$this->pageTitle = 'Help System';
		return $data;
	}
	
	/**
	 * Definitions Action
	 */
	public function definitions(){
		
		$data = array();
		$this->pageTitle = 'Definitions :: Help System';
		return $data;
	}
}