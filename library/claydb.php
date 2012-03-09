<?php
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	class claydb {
		# resource (connections) cache
		private static $connections = array();
		# map drivers (adapters) to PHP extensions or libraries
		private static $adapters = array('mysql' => 'creoleDB', 'pdo_mysql' => 'pdo_mysql', 'pdo_sqlite' => 'pdo_sqlite');
		# configuration name
		public static $cfg = '';
		public static $tables = array();
		public static $prefix = 'clay';
		# uses the appropriate driver to create a connection
		private static function connection($dsn){
			# Import our adapter
			\library('claydb/adapters/'.self::$adapters[$dsn['driver']]);
			# Adapter class as a string (notice difference between path and namespace)
			$driver = '\claydb\adapter\\'.self::$adapters[$dsn['driver']];
			$conn = new $driver;
			# Don't be confused here, we are not using $this->connect(). We are using the adapter's connect method.
			$link = $conn->connect($dsn['driver'], $dsn['host'], $dsn['database'], $dsn['user'], $dsn['pw']);
			# Return our resource
			return $conn;
		}
		# test stuff / just checks for a connection and returns TRUE or FALSE
		public static function active($host,$type,$usern){
			return !empty(self::$conections[$host.$type.$usern]);
		}
		# Our "singleton factory" - takes db config settings and deals connections
		# FIXME: A lot of the caching procedure isn't necessary any longer, this needs to be refactored for cache[src] and cache[rsrc].
		public static function connect($src = 'default'){
			# This cache stores all database configurations for a site
			static $cache = array();
			# get connection settings
			# TODO: Load configuration specific db settings? FIXME: Configuration specific enough?
			# XXX: Trying to decide how to use the database configurations to sort out connections. Considering app.[app] / app.[app].table
			# If self::$cfg has been set, we use that as the site configuration, otherwise we use the one Clay is using
			$config = !empty(self::$cfg) ? self::$cfg : \clay\CFG_NAME;
			//self::$cfg = '';
			$cache[$config.'.databases'] = !empty($cache[$config.'.databases']) ? $cache[$config.'.databases'] : \clay::config('sites/'.$config.'/databases');
			if(!empty($cache[$config.'.databases'][$src])){
				$dbcfg = $cache[$config.'.databases'][$src]['connection'];
				$dbname = $cache[$config.'.databases'][$src]['database'];
				self::$prefix = !empty($cache[$config.'.databases'][$src]['prefix']) ? $cache[$config.'.databases'][$src]['prefix'] : 'clay';
			} else {
				$dbcfg = !empty($cache[$config.'.databases']['default']['connection']) ? $cache[$config.'.databases']['default']['connection'] : '';
				$dbname = !empty($cache[$config.'.databases']['default']['database']) ? $cache[$config.'.databases']['default']['database'] : '';
				self::$prefix = !empty($cache[$config.'.databases']['default']['prefix']) ? $cache[$config.'.databases']['default']['prefix'] : '';
			}
			# Cache system databases
			$cache['system.databases'] = !empty($cache['system.databases']) ? $cache['system.databases'] : \clay::config("databases");
			$dbinfo = !empty($dbcfg) ? $cache['system.databases'][$dbcfg] : $cache['system.databases'][1]; # if the clay configuration doesn't have a specified DB set, use the first one from the system db list
			# Unnecessary, but easier to read.
			$host = $dbinfo['host'];
			$type = $dbinfo['type'];
			$usern = $dbinfo['usern'];
			# Check if a connection has already been made.
			if(!empty(self::$connections["$host.$type.$usern"])) {
				# Success, reuse the connection
				$conn =  self::$connections["$host.$type.$usern"];
				$conn->selectDB($dbname);
				return $conn;
			}
			# DB connection info
			$dsn = array('driver' => $type, 'host' => $dbinfo['host'], 'user' => $dbinfo['usern'], 'pw' => $dbinfo['passw'], 'database' => $dbname);
			$conn = self::connection($dsn);
			# Save our connection resource, so it can be reused later.
			self::$connections["$host.$type.$usern"] = $conn;
			self::tables();
			# Returns the connection resource
			return $conn;
		}
		public static function tables($flush=FALSE) {
			if(!empty($flush)) self::$tables = array();
			if(empty(self::$tables)){
				$tables = !empty(self::$cfg) ? \clay::config('sites/'. self::$cfg .'/database.tables') : \clay::config('sites/'. \clay\CFG_NAME .'/database.tables');
				if(!empty($tables)){
					foreach($tables as $table => $info){
						self::$tables[$table] = !empty($info['prefix']) ? $info['prefix'] : self::$prefix .'_'. $info['table'];
					}
				}
			}
		}
	}

	# TODO: Explain interface methods

	# All adapters should implement this interface (currently not enforeced).
	interface ClayDBAdapter {
		function connect($driver, $host, $database, $user, $pw);
		function get($sql,$bind=array(),$limit='');
		function add($sql,$bind=array());
		function update($sql,$bind=array(),$limit='');
		function delete($sql,$bind=array(),$limit='');
		function change($action,$sql,$bind=array(),$limit='');
		function selectDB($database);
		function datadict();
	}
	# All datadicts should implement this interface (currently not enforced).
	interface ClayDBDatadict {
		function __construct($arg);
		function createTable($table,$args);
		function alterTable($args);
		function createIndex($index,$table,$cols);
		function deleteIndex($args);
		function dropTable($table);
		function dataType($args);
	}
?>