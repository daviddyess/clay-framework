<?php
namespace installer;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * TODO: needs some sessions work
 */


	/**
	 * User Handling	 *
	 */
	class user {
		public function __construct(){
			ini_set( "session.gc_maxlifetime", 1800 );
			ini_set( "session.gc_probability", 10 );
			\session_start();
		}
		public static function isAdmin(){
			if(!empty($_SESSION['userid']) && ($_SESSION['userid'] > 1)){
				return true;
			}
			return false;
		}

	}

?>