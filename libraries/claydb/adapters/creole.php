<?php
namespace claydb\adapter;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	class creole implements ClayDBAdapter {
		protected $database;
		protected $link;
		protected $driver;
		function connect($driver, $host, $database, $user, $pw){
			\library('creole/Creole');
  			$dsn = array('phptype' => $driver, 'hostspec' => $host, 'username' => $user, 'password' => $pw, 'database' => $database);
		/*Flags:
		Creole::PERSISTENT	Open persistent database connection
		Creole::COMPAT_ASSOC_LOWER	Always lowercase the indexes of assoc arrays (returned by functions like *_fetch_assoc())
		Creole::COMPAT_RTRIM_STRING	Trim whitepace from end of string column types
		Creole::COMPAT_ALL	Enable all compatibility constants */
		$link = \Creole::getConnection($dsn, \Creole::COMPAT_ASSOC_LOWER);
		$this->link = $link;
		$this->database = $database;
		$this->driver = $driver;
		}
		function get($sql,$bind=array(),$limit=''){
			$sth = $this->link->prepareStatement("SELECT $sql");
			$total = array();
			if(!empty($limit)) {
				$total = \explode(',',$limit);
				$sth->setLimit($total[1]);
				$sth->setOffset($total[0]);
			}
			$rs = $sth->executeQuery($bind);
			if(!empty($total[1]) AND $total[1] == '1'){
				$rs->next();
				return $rs->getRow();
			}
			return $rs;
		}
		function add($sql,$bind=array()){
			$idgen = $this->link->getIdGenerator();
			$sth = $this->link->prepareStatement("INSERT into $sql");
			$sth->executeUpdate($bind);
   			return $idgen->getId();
		}
		function update($sql,$bind=array(),$limit=''){
			return $this->change('UPDATE',$sql,$bind,$limit);
		}
		function delete($sql,$bind=array(),$limit=''){
			return $this->change('DELETE FROM',$sql,$bind,$limit);
		}
		function change($action,$sql,$bind=array(),$limit=''){
			$sth = $this->link->prepareStatement("$action $sql");
			if(!empty($limit)) {
				$sth->setLimit($limit);
			}
			return $sth->executeUpdate($bind);
		}
		function selectDB($database){
			$this->executeUpdate("USE $database");
			$this->database = $database;
		}
		function datadict(){
			\library('claydb/datadict/'.$this->driver.'_datadict');
			$driver = $this->driver.'_datadict';
			$datadict = new $driver($this->link);
			return $datadict;
		}
	}
?>