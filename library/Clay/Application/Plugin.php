<?php
namespace Clay\Application;
/**
* Clay
*
* @copyright (C) 2007-2015 David L Dyess II
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://clay-project.com
* @author David L Dyess II (david.dyess@gmail.com)
*/

/**
 * Clay Application Plugin library
 *
 */

abstract class Plugin {

	public $App;
	public $ItemType;
	public $ItemID;
	public $PluginApp;
	public $PluginType;
	public $Plugin;
	public $Template;
	
	/**
	* Object wrapper Method for Static \clay\application::template()
	* Object template method for Plugins (based on Application Component::Template()). This allows templates to assume $this is in context.
	* @param string $action Generally 'view' (summary) or 'display'
	*/
	public function Plugin( $action, $param = array() ){
		# Plugin object access allows setting a custom template name
		if( !empty( $this->Template )){
			
			$tpl = 'plugins/'.$this->PluginType.'/'.$this->Template;
			
		} else {
		# Default Template - eg. plugins/content/example_view.tpl
			$tpl = 'plugins/'.$this->PluginType.'/'.$this->Plugin.'_'.$action;
		}
		# Build template information array to build the file path
		$args = array( 'application' => $this->PluginApp,
						'template' => $tpl,
						'data' => $this->$action( $param ),
					  );
		# Get template path (and override detection)
		$this->template( $args );
	}
	
	/**
	 * Output for Plugin Templates
	 * @param array $args ('application' => [app], 'template' => [template], 'data' => [array..])
	 */
	public function template( $args ){
		
		if( empty( $args['application'] )){
			
			$args['application'] = $this->PluginApp;
		}
		
		if( empty( $args['data'] )){
			
			$args['data'] = array();
		}
		
		$template = \clay\application::template( $args );
		# If nothing was found, stop here.
		if(empty($template['tpl'])) return;
		# If the supplied template data is an array, we extra each array key into it's own variable.
		if(is_array($template['data'])) extract($template['data']);
		# Template Debugging - Make this a system setting
		echo "<!-- Clay Template: ".$template['tpl']." --> \n";
		# Here's Johnny
		include $template['tpl'];
	}
}