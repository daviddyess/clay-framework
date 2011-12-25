<?php
namespace claydb\datadict;
/**
 * Clay Framework
 *
 * @copyright (C) 2007-2010 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */
	class sqlite implements \ClayDBDatadict {
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
				$fields = $fields.$col['name'].' '.$col['type'].$col['size'].' '.$col['key'].' '.$col['attribute'];
			}
			$sth = $this->link->exec("Create Table $table($fields)");
			return true;
			//$string = "Create Table $table($fields)";
			//return $string;

		}
		function alterTable($args){

		}
		function createIndex($index,$table,$cols){
			$sth = $this->link->exec("Create Index $index ON $table($cols)");
			return true;
			//$string = "Create Index $index ON $table($cols)";
			//return $string;
		}
		function deleteIndex($args){

		}
		function dropTable($table){

		}
		function dataType($args){
			$data = $args;
			switch($args['type']){
				case 'id':
					$data['type'] = 'INTEGER';
					$data['size'] = '';
					$data['attribute'] = 'AUTOINCREMENT';
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
				case 'tinyint':
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
					$data['type'] = 'BOOLEAN';
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
	}
?>