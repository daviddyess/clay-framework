<?php
namespace claydo\object;
/**
* Clay Framework
*
* @copyright (C) 2007-2011 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
*/
\Library('ClayDO/object');

class form extends \claydo\object {

	public $object = 'form';
	public $template = 'claydo/form';

	/**
	 * Create/Access a Property Object
	 * @param string $name of Property
	 * @param string $type of Property (on creation only)
	 */
	public function property($name, $type = NULL){
		if(!is_null($type)) {
			\Library('ClayDO/property/'.$this->object.'/'.$type);
			$prop = '\claydo\property\\'.$this->object.'\\'.$type;
			$this->properties[$name] = new $prop;
		}
		return $this->properties[$name];
	}

	/**
	 * Output for Child object's template
	 * Uses Clay's Application Object Template Method
	 */
	public function template(){
		$template = \clay\application::template(array('application' => 'common', 'template' => $this->template, 'data' => $this));
		# If nothing was found, stop here.
		if(empty($template['tpl'])) return;
		# If the supplied template data is an array, we extra each array key into it's own variable.
		if(is_array($template['data'])) extract($template['data']);
		# Here's Johnny
		include $template['tpl'];
	}

}

?>