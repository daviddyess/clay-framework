<?php
namespace claydb\adapter;
/**
 * ClayDB
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	# Import the Adapter Abstract and Interface
	\Library('ClayDB/adapter');

	class pdo_mysql extends \claydb\adapter {
		# Database Name
		public $database;
		# PDO Object
		public $link;
		/**
		 * Internal method for creating a database connection to a MySQL server
		 */
		public function connect($driver, $host, $database, $user, $pw){
			$dsn = 'mysql:host='.$host.';dbname='.$database;
			$link = new \PDO($dsn,$user,$pw);
			$link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  			$link->exec('USE '.$database);
  			$this->link = $link;
			$this->database = $database;
		}
		
		/**
		 * Fetch a result set as an associative array.
		 * @uses \PDOStatement::fetch(\PDO::FETCH_ASSOC)
		 * @uses \PDOStatement::fetchAll(\PDO::FETCH_ASSOC)
		 * @see \claydb\adapter::get()
		 * @updated ClayDB 2
		 */
		public function get($sql,$bind=array(),$limit=''){
			$total = array();
			if(!empty($limit)) {
				$total = \explode(',',$limit);
				$limit = "LIMIT $limit";
			}
			$sth = $this->link->prepare("SELECT $sql $limit");
			$sth->execute($bind);
			# If a limit of 1 has been set, only 1 row as an array
			if(!empty($total[1]) AND $total[1] == '1'){
				# Return the row as an array
				return $sth->fetch(\PDO::FETCH_ASSOC);
			}
			# Return an array of rows
			return $sth->fetchAll(\PDO::FETCH_ASSOC);
		}
		/**
		 * Fetch a result set as an object
		 * @uses \PDOStatement::fetchObject()
		 * @uses \PDOStatement::fetchAll(\PDO::FETCH_OBJ)
		 * @return object OR array of objects
		 * @since ClayDB 2 (2012-07-08)
		 */
		public function getObject($sql,$bind=array(),$limit=''){

			$total = array();
			if(!empty($limit)) {
				$total = \explode(',',$limit);
				$limit = "LIMIT $limit";
			}
			$sth = $this->link->prepare("SELECT $sql $limit");
			$sth->execute($bind);
			# If a limit of 1 has been set, only fetch 1 row as an object
			if(!empty($total[1]) AND $total[1] == '1'){
				# Return the row as an object
				return $sth->fetchObject();
			}
			# Return an array of rows as objects
			return $sth->fetchAll(\PDO::FETCH_OBJ);
		}
		# @TODO Make this versatile enough to be used for different COUNT() methods
		# @XXX Neccesary?
		public function count($table,$name=NULL){
			# Count rows in a table
		}
		/**
		 * Insert Data
		 */
		public function add($sql,$bind=array()){
			$sth = $this->link->prepare("INSERT into $sql");
			$sth->execute($bind);
			return $this->link->lastInsertID();
		}
		/**
		 * Update Data
		 */
		public function update($sql,$bind=array(),$limit=''){
			return $this->change('UPDATE',$sql,$bind,$limit);
		}
		/**
		 * Delete Data
		 */
		public function delete($sql,$bind=array(),$limit=''){
			return $this->change('DELETE FROM',$sql,$bind,$limit);
		}
		/**
		 * Internal method to process data changes
		 */
		public function change($action,$sql,$bind=array(),$limit=''){
			if(!empty($limit)) {
				$limit = "LIMIT $limit";
			}
			$sth = $this->link->prepare($action.' '.$sql.' '.$limit);
			try {
				$sth->execute($bind);
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return $sth->rowCount();
		}
		/**
		 * Select Database
		 */
		public function selectDB($database){
			$this->link->exec('USE '.$database);
			$this->database = $database;
		}
		/**
		 * ClayDB Data Dictionary Object.
		 * Used for database manipulation
		 */
		public function datadict(){
			\Library("ClayDB/datadict/pdo_mysql");
			$datadict = new \claydb\datadict\pdo_mysql($this->link);
			return $datadict;
		}
	}
?>