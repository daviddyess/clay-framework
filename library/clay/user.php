<?php
namespace clay;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 * TODO: Need to comment this better.
 * TODO: Separate Classes into packages.
 * XXX: David: I have transitioned some of the nest if/else statements to switch()'s, in case you were wondering about the inconsistency.
 */
# Import our Database class
\library('claydb');
	/**
	 * Session Handling
	 * TODO: Allow configurable Session options, such as file system storage, etc - preferably setup a base class and extend it.
	 */
	/*class session {
		private static $db;
		private static $lifetime = 1800;

		public static function start() {
			self::$db = database::connect('default');
			ini_set( "session.gc_maxlifetime", self::$lifetime );
			ini_set( "session.gc_probability", 10 );
			session_set_save_handler(array('\clay\session', 'open'),
			array('\clay\session', 'close'),
			array('\clay\session', 'read'),
			array('\clay\session', 'write'),
			array('\clay\session', 'destroy'),
			array('\clay\session', 'gc')
			);
			\session_start();
		}
		public static function open($save_path, $session_name) {
			return true;
		}
		public static function close() {}
		public static function read($id) {
			srand(crc32($id)); // Seed the pseudo randomizer with a CRC32 checksum of the session ID
			$data = '';
			//$db = clay::api('claydb.connect','sessions');
			$data = self::$db->get("session_data FROM sessions WHERE session_id = ? AND expires > ?", array($id,time()), '0,1');
			return $data['session_data'];
		}
		public static function write($id, $data) {
			srand(crc32($id)); // Seed the pseudo randomizer with a CRC32 checksum of the session ID
			$timestamp = time() + self::$lifetime;
			//$db = clay::api('claydb.connect','sessions');
			if(!self::$db->update("sessions SET session_data = ?, expires = ? WHERE session_id = ?", array($data,$timestamp,$id))){
				self::$db->add("sessions (session_id, session_data, expires) VALUES (?,?,?)", array($id,$data,$timestamp));
			}
			return;
		}
		public static function destroy($id) {
			srand(crc32($id) ); // Seed the pseudo randomizer with a CRC32 checksum of the session ID
			//$db = clay::api('claydb.connect','sessions');
			$stmt = self::$db->delete("sessions WHERE session_id = ?",array($id),1);
			return true;
		}
		public static function gc() {
			//$db = clay::api('claydb.connect','sessions');
			$stmt = self::$db->delete("sessions WHERE expires <= ?",array(time()));
			return true;
		}
	}*/
	/**
	 * User Handling
	 */
	class user {
		public function __construct(){
			//session::start();
		}
		public static function isMember(){
			if(!empty($_SESSION['userid']) && ($_SESSION['userid'] > 1)){
				return true;
			}
			return false;
		}
		public static function getInfo($id){
			if($id == 1) return array('fname' => 'Guest');
			$db = database::connect('default');
    		$profile = $db->get('* FROM profiles WHERE userid = ?',array($id),'0,1');
			return $profile;
		}

	}

?>