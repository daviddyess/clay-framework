<?php
namespace claydb\adapter;
/**
 * Clay Database
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

/**
 * 
 * Experimental! Probably doesn't work as-is
 * @author David
 *
 */
	class pdo_pgsql implements \ClayDBAdapter {
		public $database;
		public $link;

		function connect($driver, $host, $database, $user, $pw){
			$dsn = 'pgsql:host='.$host.';dbname='.$database;
			$link = new \PDO($dsn,$user,$pw);
			$link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  			$link->exec('USE '.$database);
  			$this->link = $link;
			$this->database = $database;
		}
		function get($sql,$bind=array(),$limit=''){
			$total = array();
			if(!empty($limit)) {
				$total = \explode(',',$limit);
				$limit = "LIMIT $limit";
			}
			$sth = $this->link->prepare("SELECT $sql $limit");
			$sth->execute($bind);
			if(!empty($total[1]) AND $total[1] == '1'){
				return $sth->fetch(\PDO::FETCH_ASSOC);
			}
			return $sth->fetchAll();
		}
		function add($sql,$bind=array()){
			$sth = $this->link->prepare("INSERT into $sql");
			$sth->execute($bind);
			return $this->link->lastInsertID();
		}
		function update($sql,$bind=array(),$limit=''){
			return $this->change('UPDATE',$sql,$bind,$limit);
		}
		function delete($sql,$bind=array(),$limit=''){
			return $this->change('DELETE FROM',$sql,$bind,$limit);
		}
		function change($action,$sql,$bind=array(),$limit=''){
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
		function selectDB($database){
			$this->link->exec('USE '.$database);
			$this->database = $database;
		}
		function datadict(){
			\Library("ClayDB/datadict/pdo_pgsql");
			$datadict = new \claydb\datadict\pdo_pgsql($this->link);
			return $datadict;
		}
	}
?>