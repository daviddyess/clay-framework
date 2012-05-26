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
\library('claydo/property/form/select');

class multiselect extends \claydo\property\form\select {
	
	# Inherits public $object = 'form';
	# Inherits public $options = array();
	# Override
	public $selected = array();
	# Override
	public $template = 'claydo/properties/form/multiselect';

}

?>