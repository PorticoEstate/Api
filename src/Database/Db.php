<?php
namespace App\Database;
use PDO;
use Exception;

class Db extends PDO
{
	private static $instance = null;
	private $isTransactionActive = false;
	private static $domain;
	private static $config;

	private function __construct($dsn, $username = null, $password = null, $options = null)
	{
		parent::__construct($dsn, $username, $password, $options);
	}

	public static function getInstance($dsn='', $username = null, $password = null, $options = null)
	{
		if (self::$instance === null) {
			self::$instance = new self($dsn, $username, $password, $options);
		}
		return self::$instance;
	}

	public function transaction_begin()
	{
		if (!$this->isTransactionActive) {
			parent::beginTransaction();
			$this->isTransactionActive = true;
		}
	}

	public function transaction_commit()
	{
		if ($this->isTransactionActive) {
			parent::commit();
			$this->isTransactionActive = false;
		}
	}

	public function transaction_abort()
	{
		if ($this->isTransactionActive) {
			parent::rollBack();
			$this->isTransactionActive = false;
		}
	}

	public function db_addslashes($string)
	{
		if ($string === '' || $string === null) {
			return '';
		}
		return substr(parent::quote($string), 1, -1);
	}

	public function get_transaction()
	{
		return $this->isTransactionActive;
	}

	public function unmarshal($value, $type)
	{
		$type = strtolower($type);
		/* phpgw always returns empty strings (i.e '') for null values */
		if (($value === null) || ($type != 'string' && (strlen(trim($value)) === 0))) {
			return null;
		} else if ($type == 'int') {
			return intval($value);
		} else if ($type == 'decimal') {
			return floatval($value);
		} else if ($type == 'json') {
			$_value = json_decode($value, true);
			if (!is_array($_value)) {
				$this->stripslashes($_value);
				$_value = trim($_value, '"');
			}
			return $_value;
		} else if ($type == 'string') {
			return	$this->stripslashes($value);
		}

		//Sanity check
		if (!$this->valid_field_type($type)) {
			throw new Exception(sprintf('Invalid type "%s"', $type));
		}

		return $value;
	}

	function valid_field_type($type)
	{
		$valid_types = array('int', 'decimal', 'string', 'json');
		return in_array($type, $valid_types);
	}

	function stripslashes(&$value)
	{
		return	htmlspecialchars_decode(
			stripslashes(
				str_replace(
					array('&amp;', '&#40;', '&#41;', '&#61;', '&#8722;&#8722;', '&#59;'),
					array('&', '(', ')', '=', '--', ';'),
					(string)$value
				)
			),
			ENT_QUOTES
		);
	}

	/**
	 * Execute prepared SQL statement for insert and update
	 *
	 * @param string $sql
	 * @param array $valueset  values,id and datatypes for the insert
	 * Use type = PDO::PARAM_STR for strings and type = PDO::PARAM_INT for integers
	 * @return boolean TRUE on success or FALSE on failure
	 */

	public function insert($sql, $valueset, $line = '', $file = '')
	{
		try {
			$statement_object = $this->prepare($sql);
			foreach ($valueset as $fields) {
				foreach ($fields as $field => $entry) {
					$statement_object->bindParam($field, $entry['value'], $entry['type']);
				}
				$ret = $statement_object->execute();
			}
		} catch (\PDOException $e) {
			trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
		}
		return $ret;
	}

	/**
	 * Execute prepared SQL statement for delete
	 *
	 * @param string $sql
	 * @param array $valueset  values,id and datatypes for the insert
	 * Use type = PDO::PARAM_STR for strings and type = PDO::PARAM_INT for integers
	 * @return boolean TRUE on success or FALSE on failure
	 */
	public function delete($sql, $valueset, $line = '', $file = '')
	{
		try {
			$statement_object = $this->prepare($sql);
			foreach ($valueset as $fields) {
				foreach ($fields as $field => $entry) {
					$statement_object->bindParam($field, $entry['value'], $entry['type']);
				}
				$ret = $statement_object->execute();
			}
		} catch (\PDOException $e) {
			trigger_error('Error: ' . $e->getMessage() . "<br>SQL: $sql\n in File: $file\n on Line: $line\n", E_USER_ERROR);
		}
		return $ret;
	}

	public function metadata($table)
	{
		$stmt = $this->prepare("SELECT column_name, data_type, character_maximum_length
		FROM   information_schema.columns
		WHERE  table_schema = 'public'
		AND    table_name   = :table");
		$stmt->execute(['table' => $table]);

		$meta = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $meta;
	}

	public static function datetime_format()
	{
		return 'Y-m-d H:i:s';
	}

	public static function date_format()
	{
		return 'Y-m-d H:i:s';
	}
	/**
	 * Convert a unix timestamp to a rdms specific timestamp
	 *
	 * @param int unix timestamp
	 * @return string rdms specific timestamp
	 */
	public function to_timestamp($epoch)
	{
		return date($this->datetime_format(), $epoch);
	}

	/**
	 * Convert a rdms specific timestamp to a unix timestamp
	 *
	 * @param string rdms specific timestamp
	 * @return int unix timestamp
	 */
	public function from_timestamp($timestamp)
	{
		return strtotime($timestamp);
	}

	public function get_domain()
	{
		return self::$domain;
	}

	public function set_domain($domain)
	{
		self::$domain = $domain;
	}

	public function set_config($config)
	{
		self::$config = $config;
	}

	public function get_config()
	{
		return self::$config;
	}

	/**
	 * Finds the next ID for a record at a table
	 *
	 * @param string $table tablename in question
	 * @param array $key conditions
	 * @return int the next id
	 */

	final public function next_id($table = '', $key = '')
	{
		$where = '';
		$condition = array();
		$params = array();
		if (is_array($key)) {
			foreach ($key as $column => $value) {
				if ($value) {
					$condition[] = $column . " = :" . $column;
					$params[$column] = $value;
				}
			}

			if ($condition) {
				$where = 'WHERE ' . implode(' AND ', $condition);
			}
		}

		$stmt = $this->prepare("SELECT max(id) as maximum FROM $table $where");
		$stmt->execute($params);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$next_id = (int)$row['maximum'] + 1;
		return $next_id;
	}

	
}