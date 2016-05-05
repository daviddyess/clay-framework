<?php
namespace claydo;
/**
* Clay Framework
*
* @copyright (C) 2007-2011 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
*/
abstract class object {

	public $object;
	public $properties = array();
	public $attributes = array();

	/**
	 * Create/Access a Property Object
	 * @param string $name of Property
	 * @param string $type of Property (on creation only)
	 */
	abstract function property($name, $type);
	/**
	 * Add an attribute to a Property.
	 * For use in HTML attributes or other more property specific purposes.
	 * @param string $name of attribute
	 * @param string $value of attribute
	 */
	public function attribute($name, $value){
		$this->attributes[$name] = $value;
	}
	/**
	 * Add attributes to a Property.
	 * For use in HTML attributes or other more property specific purposes.
	 * @param $array('attribute' => 'value')
	 */
	public function attributes($array){
		foreach($array as $attr => $value){
			$this->attributes[$attr] = $value;
		}
	}
	/**
	 * Output for Child object's template
	 */
	abstract function template();
	/**
	 * Alias for property() - Distinguish Container Properties
	 */
	public function container($name, $type = NULL){
		return $this->property($name,$type);
	}

}
?>