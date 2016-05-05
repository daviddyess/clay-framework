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
 * Clay Installer Package System Component
 * @author David
 *
 */
class system extends \Clay\Application\Component {

	/**
	 * View Action
	 */
	public function view(){
		
	    $data = array();
	    $this->pageTitle = 'Server System Settings';
		$data['data_priv'] = is_writeable(\clay\DATA_PATH) ? " <span style=\"color:green\">is writeable.</span>" : " <span style=\"color:red\">is not writeable!</span> Please make this directory writeable by the web server.";
		# TODO: Make this easier to access (consolidated static class?)
		\Library('ClayDB/tool/check_exts');
		$data['dbexts'] = \claydb\tool\check_exts();
	    return $data;
	}
}