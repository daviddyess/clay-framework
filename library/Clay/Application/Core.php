<?php
namespace Clay\Application;

/**
 * Clay Framework
 *
 * @copyright (C) 2007-2014 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Integrated Application Controller Object
 */

/**
 * Clay Application Core
 * 
 * A central library to retrieve information provided by an application
 */
class Core {
	
	/**
	 * Application Item Types
	 * @param string $app
	 */
	public static function ItemTypes( $app ){
		
		return \Clay\Application::API( $app, 'core', 'ItemTypes');
	}
	/**
	 * Application Item Type
	 * @param integer $app
	 * @param integer $id
	 */
	public static function ItemType( $app, $id ){
	
		return \Clay\Application::API( $app, 'core', 'ItemType', $id );
	}
	/**
	 * Application Items
	 * @param string $app
	 * @param integer $itemType (optional)
	 */
	public static function Items( $app, $itemType = NULL ){
	
		return \Clay\Application::API( $app, 'core', 'Items', $itemType );
	}
	/**
	 * Application Item
	 * @param string $app
	 * @param integer $itemType (optional)
	 * @param integer $id
	 */
	public static function Item( $app, $itemType = NULL, $id ){
	
		return \Clay\Application::API( $app, 'core', 'Item', array( $itemType, $id ));
	}
	
	public static function Fields( $app, $itemType = NULL, $id = NULL ){
		
		return \Clay\Application::API( $app, 'core', 'Fields', array( $itemType, $id ));
	}
}