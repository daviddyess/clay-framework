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
 * Clay Installer Package Main Component
 * @author David
 *
 */
class main extends \Clay\Application\Component {

	/**
	 * View Action
	 */
	public function view(){
		
		$data = array();
		
		if(!empty($_GET['package'])){
			
			$app = \clay\application(\clay\data\get('package','string','base'));
			$data['app'] = $app;
			$data['output'] = $app->action('view');
			return $data;
		}
		
		$this->pageTitle = 'Welcome!';
		return $data;
	}
}