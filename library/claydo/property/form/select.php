<?php
namespace claydo\property\form;
/**
* Clay Framework
*
* @copyright (C) 2007-2011 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
*/
\library('claydo/object/form');

class select extends \claydo\object\form {
	
	public $object = 'form';
	public $label;
	# <option> tags - options[] array('value' => x, 'content' => y)
	public $options = array();
	# <option> value selected
	public $selected;
	public $template = 'claydo/properties/form/select';

}

?>