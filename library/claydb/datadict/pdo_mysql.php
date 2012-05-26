<?php
namespace claydb\datadict;
/**
 * ClayDB
 *
 * @copyright (C) 2007-2011 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	class pdo_mysql implements \ClayDBDatadict {

		protected $link;

		public function __construct($arg) {
		    $this->link = $arg;
		}
		function createTable($table,$args){
			foreach($args as $data){
				if(empty($fields)){
					$fields = '';
				} else {
					$fields = $fields.', ';
				}
				$col = $this->dataType($data);
				$fields = $fields.$col['name'].' '.$col['type'].$col['size'].' '.$col['attribute'].' '.$col['default'].' '.$col['key'];
			}
			try {
				$this->link->exec("Create Table $table($fields)");
				//error_log("SQL Query: Create Table $table($fields)");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return true;
			//$string = "Create Table $table($fields)";
			//return $string;

		}
		function alterTable($args){

		}
		function createIndex($index,$table,$cols,$type=''){
			try {
				$this->link->exec("Create $type Index $index ON $table($cols)");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return true;
			//$string = "Create Index $index ON $table($cols)";
			//return $string;
		}
		function deleteIndex($args){

		}
		function dropTable($table){
			try {
				$this->link->exec("DROP TABLE $table");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return true;
		}
		function dataType($args){
			$data = $args;
			switch($args['type']){
				case 'id':
					$data['type'] = 'INTEGER';
					$data['size'] = 10;
					$data['attribute'] = 'UNSIGNED AUTO_INCREMENT';
					$data['key'] = 'PRIMARY KEY';
					break;
				case 'string':
					$data['type'] = 'VARCHAR';
					$data['size'] = 255;
					break;
				case 'varchar':
					$data['type'] = 'VARCHAR';
					if(empty($data['size']))
						$data['size'] = 100;
					break;
				case 'char':
					$data['type'] = 'CHAR';
					break;
				case 'text':
					$data['type'] = 'TEXT';
					break;
				case 'sm-text':
					$data['type'] = 'TINYTEXT';
					break;
				case 'med-text':
					$data['type'] = 'MEDIUMTEXT';
					break;
				case 'lg-text':
					$data['type'] = 'LONGTEXT';
					break;
				case 'integer':
				case 'int':
					$data['type'] = 'INTEGER';
					break;
				case 'tiny-int':
					$data['type'] = 'TINYINT';
					break;
				case 'sm-int':
					$data['type'] = 'SMALLINT';
					break;
				case 'med-int':
					$data['type'] = 'MEDIUMINT';
					break;
				case 'big-int':
					$data['type'] = 'BIGINT';
					break;
				case 'float':
					$data['type'] = 'FLOAT';
					break;
				case 'datetime':
					$data['type'] = 'DATETIME';
					break;
				case 'timestamp':
					$data['type'] = 'TIMESTAMP';
					break;
				case 'time':
					$data['type'] = 'TIME';
					break;
				case 'date':
					$data['type'] = 'DATE';
					break;
				case 'binary':
					$data['type'] = 'BINARY';
					break;
				case 'varbinary':
					$data['type'] = 'VARBINARY';
					break;
				case 'boolean':
					$data['type'] = 'TINYINT';
					$data['size'] = 1;
					break;
				case 'decimal':
					$data['type'] = 'DECIMAL';
					break;
				case 'blob':
					$data['type'] = 'BLOB';
					break;
				case 'sm-blob':
					$data['type'] = 'TINYBLOB';
					break;
				case 'med-blob':
					$data['type'] = 'MEDIUMBLOB';
					break;
				case 'lg-blob':
					$data['type'] = 'LONGBLOB';
					break;
			}
			if(empty($data['type']))
				$data['type'] = $args['type'];
			if(!empty($data['size'])){
				$data['size'] = '('.$data['size'].')';
			} else {
				$data['size'] = '';
			}
			if(empty($data['attribute']))
				$data['attribute'] = '';
			if(isset($data['default'])) {
				$data['default'] =  !empty($data['default']) ? "NOT NULL Default '".$data['default']."'" : "NOT NULL Default ''";
			} else {
				$data['default'] = '';
			}
			if(empty($data['key']))
				$data['key'] = '';
			return $data;
		}
		/*
		 *  primary_key  	NOT NULL auto_increment
			string 			varchar(255)
			text 			text
			integer 		int(11)
			float 			float
			datetime 		datetime
			timestamp 		datetime
			time 			time
			date 			date
			binary 			blob
			boolean 		tinyint(1)
		 */
		public function registerTables($tables){
			$path = !empty(\claydb::$cfg) ? 'sites/'.\claydb::$cfg.'/database.tables' : 'sites/'.\clay\CFG_NAME.'/database.tables';
			$storedTables = \clay::config($path);
			switch(true) {
				case !is_array($tables):
					return false;
				case !is_array($storedTables) AND is_array($tables):
					$tableData = $tables;
					break;
				case is_array($storedTables) AND is_array($tables):
					$tableData = array_merge($storedTables,$tables);
					break;
			}
			\clay::setConfig($path,$tableData);
			return true;
		}
	}
?>