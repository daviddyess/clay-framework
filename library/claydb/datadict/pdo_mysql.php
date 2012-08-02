<?php
namespace claydb\datadict;
/**
 * ClayDB
 *
 * @copyright (C) 2007-2012 David L Dyess II
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://clay-project.com
 * @author David L Dyess II (david.dyess@gmail.com)
 */

	# Import the Data Dictionary Abstract and Interface
	\library('claydb/datadict');

	/**
	 * ClayDB PDO MySQL Data Dictionary
	 * @see \claydb\datadict
	 * @see \claydb\DataDictionaryInterface
	 * @author David L Dyess II
	 *
	 */
	class pdo_mysql extends \claydb\datadict {

		protected $link;
		/**
		 * Define database resource as $this->link
		 * @param resource $arg
		 */
		public function __construct($arg) {
		    $this->link = $arg;
		}
		/**
		 * Column Syntax Generator.
		 * Generates MySQL column definitions.
		 * @param array $args
		 * @return string
		 */
		private function columnDef($args){
			# Use data types to translate request
			$type = $this->dataType($args);
			# Form the column definition
			return $this->columnString($type);
		}
		private function indexDef($args){
			$emptyArgs = array('type' => '', 'index' => '', 'index_type' => '', 'name' => '', 'reference' => '', 'expression' => '');
			$index = array_merge($emptyArgs,$args);
			extract($index);
			if(!empty($expression)){ 
				$index = "$index $expression";
			}
			return "$type $name ($index)$reference";
			/*switch(strtoupper($args['type'])){
				case 'PRIMARY KEY':
					break;
				case 'KEY':
				case 'INDEX':
					break;
				case 'UNIQUE':
					
					break;
				case 'SPATIAL':
				case 'FULLTEXT':
					break;
				case 'FOREIGN KEY':
					break;
				case 'CHECK':
					break;
			}*/
			
		}
		/**
		 * CREATE TABLE
		 * @param $table string (optional array)
		 * @param $args array (optional string)
		 * @return TRUE
		 * @throws PDOException
		 * @see ClayDBDatadict::createTable()
		 * @TODO Add processing for more than just adding columns (indexes, etc)
		 */
		public function createTable($table,$args){
			/*
			 * $table array for table options: -- MySQL-specific
			 * 'charset'
			 * 'collate'
			 * 'engine'
			 */
			if(is_array($table)){
				$charset = !empty($table['charset']) ? $table['charset'] : '';
				$collate = !empty($table['collate']) ? $table['collate'] : '';
				$engine = !empty($table['engine']) ? $table['engine'] : '';
				$tableOptions = " $charset $collate $engine";
				$table = $table['table'];
			} else {
				$charset = '';
				$collate = '';
				$engine = '';
				$tableOptions = '';
			}
			$fields = '';
			# Loop through the arrays and process them into SQL
			if(is_array($args)){
				# Process Columns
				foreach($args['column'] as $column => $data){
					
					if(!empty($fields)){
						$fields = $fields.', ';
					}
					$data['name'] = $column;
					# Use Column Processor
					$fields = $fields.$this->columnDef($data);
				}
				unset($data);
				if(!empty($args['index'])){					
					
					# Process Indices
					foreach($args['index'] as $index => $data){
					
						if(!empty($fields)){
							$fields = $fields.', ';
						}
						
						$data['name'] = $index;
						# Use Index Processor
						$fields = $fields.$this->indexDef($data);
					}
				}
				# Free memory
				unset($args); unset($data);
			} else {
				$fields = $args;
				unset($args);
			}
			
			try {
				$this->link->exec("Create Table $table($fields)$tableOptions");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return true;
		}
		# @TODO Move all individual type settings to dataType()?
		# @TODO Format strings based on data type
		private function columnString($args){
			/* Optional $args Array Keys (with default value) -- MySQL-specific
			 * 'name'
			 * 'type'
			 * 'size'
			 * 'null' => TRUE
			 * 'zerofill' => FALSE
			 * 'unsigned' => FALSE
			 * 'autoincrement' => FALSE
			 * 'default' => FALSE
			 * 'key' => FALSE
			 * 'charset' => FALSE
			 * 'collate' => FALSE
			 */
			# NOTE: The above list is for columns and do not all apply to all data types
			# See the MySQL manual for more information about options for data types
			
			if(!empty($args['size'])){
				$args['size'] = '('.$args['size'].')';
			} else {
				$args['size'] = '';
			}
			
			if(!empty($args['default'])) {
				$args['default'] =  'DEFAULT '.$args['default'];
			} else {
				$args['default'] = '';
			}
				
			if(!empty($args['null']) && empty($args['default'])){
				$args['default'] = 'DEFAULT NULL';
			}
			
			if(!empty($args['null'])){
				$args['null'] = '';
			} else {
				$args['null'] = 'NOT NULL';
			}
			
			if(!empty($args['charset'])){
				$args['charset'] = 'CHARACTER SET '.$args['charset'];
			} else {
				$args['charset'] = '';
			}
			
			if(!empty($args['collate'])){
				$args['collate'] = 'COLLATE '.$args['collate'];
			} else {
				$args['collate'] = '';
			}

			if(!empty($args['zerofill'])){
				$args['zerofill'] = 'ZEROFILL';
			} else {
				$args['zerofill'] = '';
			}
			
			if(!empty($args['unsigned'])){
				$args['unsigned'] = 'UNSIGNED';
			} else {
				$args['unsigned'] = '';
			}
			
			if(!empty($args['autoincrement'])){
				$args['autoincrement'] = 'AUTO_INCREMENT';
			} else {
				$args['autoincrement'] = '';
			}
				
			if(empty($args['key'])) {
				$args['key'] = '';
			}
			
			#@todo Move the spaces to the values?
			#@todo Finish the string (missing pieces from above)
			#@todo Add a control structure to build strings based on type (with only applicable options)
			$column = $args['name'].' '.$args['type'].$args['size'].' '.$args['unsigned'].' '.$args['zerofill'].' '.$args['null'].' '.$args['default'].' '.$args['autoincrement'].' '.$args['key'];
			
			unset($args);
			
			return $column;
		}
		/**
		 * Alter Table handler method. Used to route requests to appropriate methods for altering tables
		 * @see ClayDBDatadict::alterTable()
		 * @example $this->alterTable('my_table', array(array('addColumns', array('name' => 'my_column...
		 */
		public function alterTable($table,$args){
			/* Options for $args[0]. These map to methods in this class
			 * See MySQL ALTER TABLE documentation
			 * 'addColumn' - ADD COLUMN [definition]
			 * 'addColumns' - ADD COLUMN [definition][,definition][,...] (definitions in arrays)
			 * 'addIndex' - ADD INDEX
			 * 'alterColumn' - ALTER COLUMN
			 * 'changeColumn' - CHANGE COLUMN
			 * 'modifyColumn' - MODIFY COLUMN
			 * 'dropColumn' - DROP COLUMN
			 * 'dropIndex' - DROP INDEX
			 * 'renameTable' - RENAME TO [table]
			 * See corresponding class methods for requirements
			 */
			# If $args is an array, processing must be performed first, otherwise we are in a callback
			if(is_array($args[0])){
			
				$specs = '';
				
				foreach($args as $alter){
					
					if(!empty($specs)){						
						$specs = $specs.', ';
					}
					
					$alteration = $alter[0];
					# The first key identifies the class method to call
					$change = $this->$alteration(NULL,$alter[1]);
					# Some methods perform the change independently from alterTable(), those return NULL
					if(!empty($change)){
						# $change returned something
						$specs = $specs.$change;
					}
				}				
			} else {
				# This should be used as a callback from another method, not as a direct ALTER TABLE query
				# Using this for an explicit ALTER TABLE query will result in the application downgraded in quality and compatibility rating
				$specs = $args;
			}
			unset($args);
			# Complete query (or act as callback)
			try {
				$this->link->exec("ALTER TABLE $table $specs");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			
		}
		/**
		 * ADD COLUMN
		 * @param string $table
		 * @param array $args
		 * @return string || QUERY
		 * @example Query: addColumn([table name],array('name' => [column name], 'type' => [data type], [data type specific] => ...))
		 * @example String: addColumn(NULL,array(...)) Same as Query example, except $table is NULL
		 */
		public function addColumn($table=NULL,$args){			
	
			# Use Column Processor
			$def = $this->columnDef($args);
			# Append string
			$column = ' ADD COLUMN '.$def;			
			# If $table is not defined, we only do processing
			if(is_null($table)){
				# Return processed string
				return $column;
			} else {
				# Perform the query
				return $this->alterTable($table,$column);
			}
			
		}
		/**
		 * ADD COLUMN (multiple)
		 * @param string $table
		 * @param array $args
		 * @return string
		 * @see addColumn()
		 */
		public function addColumns($table=NULL,$args){
			
			$columns = '';
			# Loop each column request through addColumn()
			foreach($args as $column){
				# Delimit requests with commas
				if(!empty($columns)){
					$columns = $columns.', ';
				}
				# Let addColumn do all the heavy lifting
				$columns = $columns.$this->addColumn(NULL,$column);				
			}
			
			# If $table is not defined, we only do processing
			if(is_null($table)){
				return $columns;
			} else {			
				# Perform the Query
				return $this->alterTable($table,$columns);
			}
		}
		public function addIndex($table=NULL,$args){
			# If no table is specified we only process into a string
			if(is_null($table)){
				return ' ADD '.$this->indexDef($args);
			} else {
				# Perform the query
				return $this->createIndex($table,$args);
			}
			 
		}
		
		public function alterColumn($table=NULL,$args){
			
			if($args['default'] === FALSE){
				$default = ' DROP DEFAULT ';
			} elseif(empty($args['default'])) {
				$default = " SET DEFAULT '' ";
			} else {
				$default = ' SET DEFAULT '.$args['default'];
			}
			$column = ' ALTER COLUMN '.$args['name'].$default;
			if(is_null($table)){
				return $column;
			} else {
				# Perform the Query
				return $this->alterTable($table,$column);
			}
			
		}
		public function changeColumn($table=NULL,$args){
			
			# Use Column Processor
			$def = $this->columnDef($args);
			# Append the definition to the string -- 'name' will be the new name
			$column = ' CHANGE COLUMN '.$args['oldname'].' '.$def;
		
			# If $table is not defined, just do processing
			if(is_null($table)){
				return $column;
			} else {
				# Perform the Query
				return $this->alterTable($table,$column);
			}
			
		}
		public function modifyColumn($table=NULL,$args){
		
			# Use Column Processor
			$def = $this->columnDef($args);
			# Append the definition to the string
			$column = ' MODIFY COLUMN '.$def;
			
			# If $table is not defined, just do processing
			if(is_null($table)){
				return $column;
			} else {
				# Perform the Query
				return $this->alterTable($table,$column);
			}
		}
		public function dropColumn($table=NULL,$args){
			
			$column = ' DROP COLUMN '.$args['name'];
			if(is_null($table)){
				return $column;
			} else {
				# Perform the Query
				return $this->alterTable($table,$column);
			}
		}
		public function dropIndex($table=NULL,$args){
			
			$column = ' DROP INDEX '.$args['name'];
			if(is_null($table)){
				return $column;
			} else {
				# Perform the Query
				return $this->alterTable($table,$column);
			}
				
		}
		public function renameTable($table=NULL,$args){
			
			# Create SQL string for renaming a table
			$string = ' RENAME TO '.$args['table'];
			
			# If $table is not defined, just do processing
			if(is_null($table)){
				return $string;
			} else {
				# Perform the Query
				return $this->alterTable($table,$string);
			}
			
		}
		
		function createIndex($table,$args){
			if(empty($args['type'])){
				$args['type'] = '';
			}
			try {
				$this->link->exec('Create '.$args['type'].' Index '.$args['name'].' ON '.$table.'('.$args['index'].')');
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return true;
		}		
		/**
		 * Drop Table(s)
		 * @param string $table or array(table, table, ..)
		 * @return TRUE
		 * @throws PDOException
		 */
		function dropTable($table){
			# Accept an array of tables
			if(is_array($table)){
				$tables = '';
				# Build array of tables into a comma delimited string
				foreach($table as $_table){
					
					if(!empty($tables)){
						$tables = $tables.', ';
					}
					$tables =  $tables."$_table ";
				}
				$table = $tables;
				unset($tables);
			}
			
			try {
				$this->link->exec("DROP TABLE IF EXISTS $table");
			} catch(PDOException $e) {
				throw new \Exception($e);
			}
			return TRUE;			
		}
		
		/**
		 * Data Type Translation.
		 * Generates MySQL data type information.
		 * @param $args array
		 * @return $data array - Translated data type selection to MySQL specific definitions
		 */
		public function dataType($args){
			/* Optional $args Array Keys (with default value) -- MySQL-specific
			* 'name'
			* 'type'
			* 'size'
			* 'null' => TRUE
			* 'zerofill' => FALSE
			* 'unsigned' => FALSE
			* 'autoincrement' => FALSE
			* 'default' => FALSE
			* 'key' => FALSE
			* 'charset' => FALSE
			* 'collate' => FALSE
			*/
			# Default values (- means WITHOUT, + mean WITH)
			# + NULL
			if(!isset($args['null'])) $args['null'] = TRUE;
			# - ZEROFILL
			if(!isset($args['zerofill'])) $args['zerofill'] = FALSE;
			# SIGNED
			if(!isset($args['unsigned'])) $args['unsigned'] = FAlSE;
			# - AUTO_INCREMENT
			if(!isset($args['autoincrement'])) $args['autoincrement'] = FALSE;
			# - DEFAULT
			if(!isset($args['default'])) $args['default'] = FALSE;
			# - KEY
			if(!isset($args['key'])) $args['key'] = FALSE;
			# - CHARACTER SET
			if(!isset($args['charset'])) $args['charset'] = FALSE;
			# - COLLATE
			if(!isset($args['collate'])) $args['collate'] = FALSE;
			
			# Alternative to array_merge later
			$data = $args;
			
			# Commented cases specify default Types for MySQL
			# Lowercased types are converted to uppercase - setting $args['type'] as uppercased is recommended, but not required
			# All Switch Case strings below must be uppercased
			switch(strtoupper($args['type'])){
				# Alias for INTEGER, PRIMARY KEY
				case 'ID':
					$data['type'] = 'INTEGER';
					$data['size'] = 10;
					$data['null'] = FALSE;
					$data['unsigned'] = TRUE; 
					$data['autoincrement'] = TRUE;
					$data['key'] = 'PRIMARY KEY';
					break;
				# Alias for VARCHAR(255)
				case 'STRING':
					$data['type'] = 'VARCHAR';
					$data['size'] = 255;
					break;
				case 'VARCHAR':
					$data['type'] = 'VARCHAR';
					if(empty($data['size'])){
						$data['size'] = 100;
					}
					break;
				/*case 'CHAR':
					$data['type'] = 'CHAR';
					break;
				case 'TEXT':
					$data['type'] = 'TEXT';
					break;
				case 'TINYTEXT':
					$data['type'] = 'TINYTEXT';
					break;
				case 'MEDIUMTEXT':
					$data['type'] = 'MEDIUMTEXT';
					break;
				case 'LONGTEXT':
					$data['type'] = 'LONGTEXT';
					break;
				case 'INTEGER':
				case 'INT':
					$data['type'] = 'INTEGER';
					break;
				case 'TINYINT':
					$data['type'] = 'TINYINT';
					break;
				case 'SMALLINT':
					$data['type'] = 'SMALLINT';
					break;
				case 'MEDIUMINT':
					$data['type'] = 'MEDIUMINT';
					break;
				case 'BIGINT':
					$data['type'] = 'BIGINT';
					break;
				case 'FLOAT':
					$data['type'] = 'FLOAT';
					break;
				case 'DATETIME':
					$data['type'] = 'DATETIME';
					break;
				case 'TIMESTAMP':
					$data['type'] = 'TIMESTAMP';
					break;
				case 'TIME':
					$data['type'] = 'TIME';
					break;
				case 'DATA':
					$data['type'] = 'DATE';
					break;
				case 'BINARY':
					$data['type'] = 'BINARY';
					break;
				case 'VARBINARY':
					$data['type'] = 'VARBINARY';
					break;*/
				# Alias for TINYINT(1)
				case 'BOOLEAN':
					$data['type'] = 'TINYINT';
					$data['size'] = 1;
					break;
				/*case 'DECIMAL':
					$data['type'] = 'DECIMAL';
					break;
				case 'BLOB':
					$data['type'] = 'BLOB';
					break;
				case 'TINYBLOB':
					$data['type'] = 'TINYBLOB';
					break;
				case 'MEDIUMBLOB':
					$data['type'] = 'MEDIUMBLOB';
					break;
				case 'LONGBLOB':
					$data['type'] = 'LONGBLOB';
					break;*/
					
				# This isn't necessary, it's just here for clarification
				default:
					$data['type'] = $args['type'];
					break;
			}
		
			unset($args);
			
			return $data;
		}
	}
?>