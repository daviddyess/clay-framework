<?php
namespace clay\user;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * @package Clay Library - User Security
 */
/**
 * User Security
 * @todo Just a starter, if even that. Needs a lot of work.
 * @author david
 *
 */
	class sec {

		public static function auth_gen($args){
		    extract($args);
		    $id = md5(time() * time() / 1.1);
		    $_SESSION[$origin]['authid'] = $id;
		    return $id;
		}

		public static function auth_check($args){
		    extract($args);
		    if($_SESSION[$origin]['authid'] != $id){
		      die('<strong>Your request could not be Authenticated! </strong>'.$_SESSION[$origin]['authid']." does not match ".$id);
		    }
		}

	}